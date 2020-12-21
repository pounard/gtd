<?php

namespace GestionBundle\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use GestionBundle\Mapper\ContractMapper;
use GestionBundle\Mapper\LogementMapper;
use GestionBundle\Mapper\PersonneMapper;
use GestionBundle\Mapper\QuittanceMapper;
use Goat\AccountBundle\Controller\AccountMapperAwareController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

abstract class AbstractGestionController extends Controller
{
    use AccountMapperAwareController;

    /**
     * Get quittance mapper
     */
    final protected function getQuittanceMapper() : QuittanceMapper
    {
        return $this->getMapper('Gestion:Quittance');
    }

    /**
     * Get quittance mapper
     */
    final protected function getContractMapper() : ContractMapper
    {
        return $this->getMapper('Gestion:Contract');
    }

    /**
     * Get quittance mapper
     */
    final protected function getLogementMapper() : LogementMapper
    {
        return $this->getMapper('Gestion:Logement');
    }

    /**
     * Get quittance mapper
     */
    final protected function getPersonneMapper() : PersonneMapper
    {
        return $this->getMapper('Gestion:Personne');
    }

    /**
     * Render given response as PDF
     *
     * @param Response $previous
     * @param string $filename
     *
     * @return $response
     */
    final protected function renderAsPdf(Response $previous, string $filename) : Response
    {
        $content = $previous->getContent();

        if (false === stripos($previous->headers->get('Content-Type'), 'html')) {
            if (!strripos($content, '</html>')) {
                throw new NotAcceptableHttpException();
            }
        }

        $options = new Options();
        //$options->set('defaultFont', 'Courier');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('fontHeightRatio', 1);
        $options->set('pdfBackend', 'auto');
        $options->set('defaultPaperSize', 'A4');
        $options->set('defaultPaperOrientation', 'portrait');
        $options->set('defaultMediaType', 'print');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($content);
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();

        $response = new Response();
        $response->setContent($dompdf->output());
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/pdf');

        if ($filename) {
            $response->headers->set('Content-Disposition', 'attachment; filename=' . rawurlencode($filename));
        }

        return $response;
    }
}
