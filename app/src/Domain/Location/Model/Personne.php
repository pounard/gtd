<?php

declare(strict_types=1);

namespace Gtd\Domain\Location\Model;

use Gtd\Shared\Domain\Model\Identifier;
use Gtd\Shared\Domain\Model\UuidIdentifier;

final class Personne
{
    use AddressAwareTrait;

    public Identifier $id;
    public ?string $civilite = null;
    public string $nom;
    public string $prenom;
    public ?string $emailAddress = null;
    public ?\DateTimeInterface $dateNaissance = null;
    public ?string $villeNaissance = null;
    public ?string $telephone = null;

    public function __construct(string $nom, string $prenom)
    {
        $this->id = UuidIdentifier::empty();
        $this->nom = $nom;
        $this->prenom = $prenom;
    }
}
