<?php

declare(strict_types=1);

namespace Gtd\Domain\Location\Model;

use Gtd\Shared\Domain\Model\Identifier;
use Gtd\Shared\Domain\Model\UuidIdentifier;

final class Quittance
{
    public Identifier $id;
    public Identifier $contratId;
    public int $serial;
    public \DateTimeInterface $dateStart;
    public \DateTimeInterface $dateStop;
    public ?\DateTimeInterface $datePaiement = null;
    public ?string $typePaiement;
    public string $periode;
    public int $loyer;
    public int $provisionCharges;

    public function __construct(
        int $serial,
        \DateTimeInterface $dateStart,
        \DateTimeInterface $dateStop,
        string $periode,
        int $loyer,
        int $provisionCharges
    ) {
        $this->contratId = UuidIdentifier::empty();
        $this->dateStart = $dateStart;
        $this->dateStop = $dateStop;
        $this->id = UuidIdentifier::empty();
        $this->loyer = $loyer;
        $this->periode = $periode;
        $this->provisionCharges = $provisionCharges;
    }
}
