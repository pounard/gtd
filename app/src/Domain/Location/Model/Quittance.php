<?php

declare(strict_types=1);

namespace Gtd\Domain\Location\Model;

use Gtd\Shared\Domain\Model\Identifier;
use Gtd\Shared\Domain\Model\UuidIdentifier;

final class Quittance
{
    public Identifier $id;
    public Identifier $contratId;
    public ?Identifier $paiementId = null;
    public int $year;
    public int $month;
    public ?\DateTimeInterface $dateStart;
    public ?\DateTimeInterface $dateStop;
    public float $loyer; // @todo Monetary
    public float $provisionCharges; // @todo Monetary

    public function __construct(
        int $year,
        int $month,
        \DateTimeInterface $dateStart,
        \DateTimeInterface $dateStop,
        float $loyer,
        float $provisionCharges
    ) {
        $this->contratId = UuidIdentifier::empty();
        $this->dateStart = $dateStart;
        $this->dateStop = $dateStop;
        $this->id = UuidIdentifier::empty();
        $this->loyer = $loyer;
        $this->loyer = $loyer;
        $this->month = $month;
        $this->provisionCharges = $provisionCharges;
    }
}
