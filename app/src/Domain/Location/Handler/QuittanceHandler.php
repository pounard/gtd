<?php

declare(strict_types=1);

namespace Gtd\Domain\Location\Handler;

use Gtd\Application\Location\Command\QuittanceAcquitteCommand;
use Gtd\Application\Location\Command\QuittanceStubGenerateCommand;
use Gtd\Domain\Location\Event\QuittanceAcquittedEvent;
use Gtd\Domain\Location\Model\Contrat;
use Gtd\Domain\Location\Model\Quittance;
use Gtd\Domain\Location\Repository\ContratRepository;
use Gtd\Domain\Location\Repository\QuittanceRepository;
use MakinaCorpus\CoreBus\CommandBus\AbstractCommandHandler;

final class QuittanceHandler extends AbstractCommandHandler
{
    private ContratRepository $contratRepository;
    private QuittanceRepository $quittanceRepository;

    public function __construct(
        ContratRepository $contratRepository,
        QuittanceRepository $quittanceRepository
    ) {
        $this->contratRepository = $contratRepository;
        $this->quittanceRepository = $quittanceRepository;
    }

    public function doAquittement(QuittanceAcquitteCommand $command): void
    {
        $quittance = $this->quittanceRepository->find($command->quittanceId);

        if (!$quittance) {
            throw new \DomainException(\sprintf("La quittance avec l'identifiant '%s' n'existe pas", $command->quittanceId));
        }
        \assert($quittance instanceof Quittance);

        $this->quittanceRepository->acquitte($command->quittanceId, $command->gracieux);

        $this->notifyEvent(new QuittanceAcquittedEvent($command->quittanceId, $command->gracieux));
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
