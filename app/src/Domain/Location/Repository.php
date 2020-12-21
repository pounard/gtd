<?php

declare(strict_types=1);

namespace Gtd\Domain\Location\Repository;

use Gtd\Domain\Location\Model\Quittance;
use Gtd\Shared\Domain\Model\Identifier;
use Gtd\Shared\Domain\Repository\Repository;
use Gtd\Domain\Location\Model\Paiement;

interface PersonneRepository extends Repository
{
}

interface LogementRepository extends Repository
{
}

interface ContratRepository extends Repository
{
}

interface PaiementRepository extends Repository
{
    public function create(
        Identifier $personneId,
        float $montant, // @todo monetary
        \DateTimeInterface $date,
        ?string $typePaiement
    ): Paiement;
}

interface QuittanceRepository extends Repository
{
    public function create(
        Identifier $contratId,
        int $year,
        int $month,
        \DateTimeInterface $dateStart,
        \DateTimeInterface $dateStop,
        float $loyer, // @todo Monetary
        float $provisionCharges // @todo Monetary
    ): Quittance;

    public function findForPeriode(Identifier $contratId, int $year, int $month): ?Quittance;

    public function acquitte(Identifier $id, bool $gracieux): Quittance;
}
