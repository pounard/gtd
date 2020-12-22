<?php

declare(strict_types=1);

namespace Gtd\Domain\Location\Handler;

use Gtd\Application\Location\Command\QuittanceAcquitteCommand;
use Gtd\Application\Location\Command\QuittanceGenerateCourrierCommand;
use Gtd\Application\Location\Command\QuittanceGenerateCourrierResponse;
use Gtd\Application\Location\Command\QuittanceStubGenerateCommand;
use Gtd\Domain\Courrier\Event\CourrierAddedEvent;
use Gtd\Domain\Courrier\Repository\CourrierRepository;
use Gtd\Domain\Location\Event\QuittanceAcquittedEvent;
use Gtd\Domain\Location\Model\Contrat;
use Gtd\Domain\Location\Model\Logement;
use Gtd\Domain\Location\Model\Quittance;
use Gtd\Domain\Location\Repository\ContratRepository;
use Gtd\Domain\Location\Repository\LogementRepository;
use Gtd\Domain\Location\Repository\PersonneRepository;
use Gtd\Domain\Location\Repository\QuittanceRepository;
use Gtd\Domain\Location\Service\QuittanceCourrierGenerator;
use MakinaCorpus\CoreBus\CommandBus\AbstractCommandHandler;

final class QuittanceHandler extends AbstractCommandHandler
{
    private ContratRepository $contratRepository;
    private CourrierRepository $courrierRepository;
    private LogementRepository $logementRepository;
    private PersonneRepository $personneRepository;
    private QuittanceCourrierGenerator $quittancesCourrierGenerator;
    private QuittanceRepository $quittanceRepository;

    public function __construct(
        ContratRepository $contratRepository,
        QuittanceRepository $quittanceRepository,
        QuittanceCourrierGenerator $quittancesCourrierGenerator,
        CourrierRepository $courrierRepository,
        LogementRepository $logementRepository,
        PersonneRepository $personneRepository
    ) {
        $this->contratRepository = $contratRepository;
        $this->courrierRepository = $courrierRepository;
        $this->logementRepository = $logementRepository;
        $this->personneRepository = $personneRepository;
        $this->quittanceRepository = $quittanceRepository;
        $this->quittancesCourrierGenerator = $quittancesCourrierGenerator;
    }

    public function doAquittement(QuittanceAcquitteCommand $command): void
    {
        $quittance = $this->quittanceRepository->find($command->quittanceId);

        if (!$quittance) {
            throw new \DomainException(\sprintf("La quittance avec l'identifiant '%s' n'existe pas", $command->quittanceId));
        }
        \assert($quittance instanceof Quittance);

        $this->quittanceRepository->acquitte($command->quittanceId, $command->gracieux, $command->dateAcquittement);
        $this->notifyEvent(new QuittanceAcquittedEvent($command->quittanceId, $command->gracieux, $command->dateAcquittement));
    }

    public function doGenerateCourrier(QuittanceGenerateCourrierCommand $command): QuittanceGenerateCourrierResponse
    {
        if (empty($command->quittanceIdList)) {
            throw new \DomainException("Impossible de générer un courrier de quittances acquittées sans quittances.");
        }

        $quittances = [];
        $contrat = null;

        // @todo Only one SQL query would be best here I guess.
        foreach ($command->quittanceIdList as $id) {
            $quittance = $this->quittanceRepository->find($id);
            if (!$quittance || !$quittance->acquitte) {
                throw new \DomainException(\sprintf("Tentative de génération d'une quittance qui n'existe pas ou non acquittée: '%s'", $id));
            }
            \assert($quittance instanceof Quittance);

            if (null === $contrat) {
                $contrat = $this->contratRepository->find($quittance->contratId);
                \assert($contrat instanceof Contrat);
            } else if (!$contrat->id->equals($quittance->contratId)) {
                throw new \DomainException("Vous ne pouvez générer des quittances que pour un seul contrat.");
            }

            $quittances[] = $quittance;
        }

        $logement = $this->logementRepository->find($contrat->logementId);
        \assert($logement instanceof Logement);

        $content = $this->quittancesCourrierGenerator->generateQuittancesCourrier(
            $logement,
            $this->personneRepository->find($contrat->locataireId),
            $this->personneRepository->find($logement->proprietaireId ?? $logement->mandataireId),
            $quittances
        );

        // @todo Meilleur titre.
        $courrier = $this->courrierRepository->create($content, "Quittances");
        $this->notifyEvent(new CourrierAddedEvent($courrier));

        return new QuittanceGenerateCourrierResponse($courrier->id);
    }

    public function doGenerateStub(QuittanceStubGenerateCommand $command): void
    {
        $contrat = $this->contratRepository->find($command->contratId);

        if (!$contrat) {
            throw new \DomainException(\sprintf("Le contrat avec l'identifiant '%s' n'existe pas", $command->contratId));
        }
        \assert($contrat instanceof Contrat);

        $now = new \DateTimeImmutable();

        $year = (int)$contrat->dateStart->format('Y');
        $month = (int)$contrat->dateStart->format('m');
        $startDay = (int)$contrat->dateStart->format('d');

        $currentYear = (int)$now->format('Y');
        $currentMonth = (int)$now->format('m');

        // First month may be incomplete.
        if (1 < $startDay) {
            $this->generateStubFrom(
                $contrat,
                $year,
                $month,
                \DateTimeImmutable::createFromFormat(
                    'Y-m-d',
                    \sprintf('%04d-%02d-%02d', $year, $month, $startDay)
                )
            );
            $month = 12 === $month ? 1 : $month + 1;
        }

        for (; $year <= $currentYear; ++$year) {
            for (; $month <= 12; ++$month) {
                $this->generateStubFrom($contrat, $year, $month, null);

                if ($year >= $currentYear && $month >= $currentMonth) {
                    break 2; // We are done
                }
            }
            $month = 1; // Next year starts now.
        }
    }

    private function generateStubFrom(Contrat $contrat, int $year, int $month, ?\DateTimeInterface $dateStart): Quittance
    {
        $dateStart = $dateStart ?? $this->monthFirstDay($year, $month);

        return $this
            ->quittanceRepository
            ->findForPeriode(
                $contrat->id,
                $year,
                $month
            ) ?? $this
            ->quittanceRepository
            ->create(
                $contrat->id,
                $year,
                $month,
                $dateStart,
                $this->monthLastDay($year, $month),
                $contrat->loyer,
                $contrat->provisionCharges
            )
        ;
    }

    private function monthFirstDay(int $year, int $month): \DateTimeInterface
    {
        return \DateTimeImmutable::createFromFormat('Y-m-d', \sprintf('%04d-%02d-01', $year, $month));
    }

    private function monthLastDay(int $year, int $month): \DateTimeInterface
    {
        return \DateTimeImmutable::createFromFormat('Y-m-d', $this->monthFirstDay($year, $month)->format('Y-m-t'));
    }
}
