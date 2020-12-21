<?php

declare(strict_types=1);

namespace Gtd\Domain\Location\Model;

use Gtd\Shared\Domain\Model\Identifier;
use Gtd\Shared\Domain\Model\UuidIdentifier;

final class Contrat
{
    public Identifier $id;
    public Identifier $logementId;
    public Identifier $locataireId;
    public \DateTimeInterface $dateStart;
    public ?\DateTimeInterface $dateStop;
    public int $loyer;
    public int $provisionCharges;

    public function __construct(
        \DateTimeInterface $dateStart,
        int $loyer,
        int $provisionCharges
    ) {
        $this->dateStart = $dateStart;
        $this->id = UuidIdentifier::empty();
        $this->locataireId = UuidIdentifier::empty();
        $this->logementId = UuidIdentifier::empty();
        $this->loyer = $loyer;
        $this->provisionCharges = $provisionCharges;
    }
}
