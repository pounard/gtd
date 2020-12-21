<?php

declare(strict_types=1);

namespace Gtd\Domain\Location\Model;

use Gtd\Shared\Domain\Model\Identifier;
use Gtd\Shared\Domain\Model\UuidIdentifier;

final class Paiement
{
    public Identifier $id;
    public Identifier $personneId;
    public \DateTimeInterface $date;
    public float $montant; // @todo monetary
    public ?string $typePaiement;

    public function __construct(
        \DateTimeInterface $date,
        float $montant
    ) {
        $this->date = $date;
        $this->id = UuidIdentifier::empty();
        $this->montant = $montant;
        $this->personneId = UuidIdentifier::empty();
    }
}
