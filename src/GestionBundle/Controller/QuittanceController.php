<?php

namespace GestionBundle\Controller;

use AppBundle\Controller\RefererControllerTrait;
use Dompdf\Dompdf;
use Dompdf\Options;
use GestionBundle\Controller\ArgumentResolver\DateTimeValueResolver;
use GestionBundle\Entity\Contrat;
use GestionBundle\Entity\Quittance;
use Goat\Error\GoatError;
use Goat\Query\Where;
use Symfony\Component\Form\Extension\Core\Type as Form;
use Symfony\Component\HttpFoundation\Request;

class QuittanceController extends AbstractGestionController
{
    use RefererControllerTrait;

    /**
     * Generate missing quittances for the given contract
     */
    private function generateMissingQuittances(Contrat $contract)
    {
        /*
        $insert = $this
            ->getQuittanceMapper()
            ->createInsertValues()
            ->columns(['id_contrat', 'serial', 'date_start', 'date_stop', 'periode', 'loyer', 'provision_charges'])
        ;
        $current = clone $start;
        do {
            $mutableDate = clone $current;
            $insert->values([
                $contratId,
                $index++,
                clone $mutableDate->modify('first day of'),
                clone $mutableDate->modify('last day of'),
                'mensuel',
                50000,
                2500,
            ]);
            $current->modify("+1 month");
        } while ($current < $now);
        $insert->execute();
        die();
         */
    }

    /**
     * Quittances paiement input form
     */
    public function listAction(Request $request, Contrat $contract)
    {
        $this->getUserAccountOrDie();

        return $this->render('GestionBundle:quittance:list.html.twig', [
            'contract'  => $contract,
            'quittances' => $this->getQuittanceMapper()->paginateForContrat($contract->getId(), 100, $request->get('page', 1)),
        ]);
    }

    /**
     * Quittances paiement input form
     */
    public function controlAction(Request $request, Contrat $contract)
    {
        $this->getUserAccountOrDie();

        $quittances = $this->getQuittanceMapper()->findBy(['id_contrat' => $contract->getId(), 'date_paiement' => null]);
        $database = $this->getDatabase();

        if ($request->isMethod('post')) {
            $paiementTypes = $request->get('paiment_type');
            $paiementDates = $request->get('paiment_date');

            try {
                $transaction = $database->startTransaction()->start();

                /** @var \GestionBundle\Entity\Quittance $quittance */
                foreach ($quittances as $quittance) {
                    $id = $quittance->getId();

                    if (!empty($paiementDates[$id])) {
                        $database
                            ->update('quitance')
                            ->sets([
                                'date_paiement' => \DateTime::createFromFormat('Y-m-d', $paiementDates[$id]),
                                'type_paiement' => $paiementTypes[$id] ?? 'autre',
                            ])
                            ->condition('id', $id)
                            ->execute()
                        ;
                    }
                }

                $transaction->commit();

                return $this->redirectToReferer($request);

            } catch (GoatError $e) {
                $this->addFlash('error', $this->get('translator')->trans('an error happened, please retry'));

                if ($this->getParameter('kernel.debug')) {
                    throw $e;
                }
            }
        }

        return $this->render('GestionBundle:quittance:control.html.twig', [
            'contract'    => $contract,
            'quittances'  => $quittances,
        ]);
    }

    /**
     * Generate quittances form
     */
    public function generateFormAction(Request $request, Contrat $contract)
    {
        // Provide default values
        $from = new \DateTime("first day of January");
        $to   = new \DateTime();

        $form = $this
            ->createFormBuilder()
            ->add('date1', Form\DateType::class, [
                'label'     => "From",
                'html5'     => true,
                'widget'    => 'single_text',
                'required'  => true,
                'data'      => $from,
            ])
            ->add('date2', Form\DateType::class, [
                'label'     => "To",
                'html5'     => true,
                'widget'    => 'single_text',
                'required'  => true,
                'data'      => $to,
            ])
            ->add('submit', Form\SubmitType::class, [
                'label'     => "Generate",
            ])
            ->getForm()
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $dates  = $form->getData();
            $from   = min($dates);
            $to     = max($dates);

            return $this->redirectToRoute('gestion_quittance_generate', [
                'contract'  => $contract->getId(),
                'from'      => $from->format(DateTimeValueResolver::DATE_SIMPLE),
                'to'        => $to->format(DateTimeValueResolver::DATE_SIMPLE)
            ]);
        }

        return $this->render('GestionBundle:quittance:generate-form.html.twig', [
            'contract'  => $contract,
            'form'      => $form->createView(),
        ]);
    }

    /**
     * Generate quittances action
     */
    public function generateAction(Request $request, Contrat $contract, \DateTimeInterface $from, \DateTimeInterface $to, $format = 'html')
    {
        // Be nice with the user
        $from = min([$from, $to]);
        $to = max([$from, $to]);

        /** @var \GestionBundle\Entity\Logement $logement */
        $logement = $this->getLogementMapper()->findOne($contract->getIdLogement());
        $locataire = $this->getPersonneMapper()->findOne($contract->getIdLocataire());
        $proprietaire = $this->getPersonneMapper()->findOne($logement->getIdMandataire());

        $quittances = $this
            ->getQuittanceMapper()
            ->createSelect()
            ->condition('q.id_contrat', $contract->getId())
            ->condition('q.date_start', clone $from, '>=')
            ->condition('q.date_stop', clone $to, '<=')
            ->condition('q.date_paiement', null, Where::NOT_IS_NULL)
            ->orderBy('q.serial')
            ->execute([], Quittance::class)
        ;

        $response = $this->render('GestionBundle:quittance:generate.html.twig', [
            'logement'      => $logement,
            'locataire'     => $locataire,
            'quittances'    => $quittances,
            'proprietaire'  => $proprietaire,
            'from'          => $from,
            'to'            => $to,
            'total'         => 12,
        ]);

        if ('pdf' === $format) {
            $options = new Options();
            //$options->set('defaultFont', 'Courier');
            $options->set('isHtml5ParserEnabled', true);
            $options->set('fontHeightRatio', 1);
            $options->set('pdfBackend', 'auto');
            $options->set('defaultPaperSize', 'A4');
            $options->set('defaultPaperOrientation', 'portrait');
            $options->set('defaultMediaType', 'print');

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($response->getContent());
            $dompdf->setPaper('letter', 'portrait');
            $dompdf->render();

            $dompdf->stream();
            exit;
        }

        return $response;
    }
}
