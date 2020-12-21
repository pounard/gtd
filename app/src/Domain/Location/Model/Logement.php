<?php

declare(strict_types=1);

namespace Gtd\Domain\Location\Model;

use Gtd\Shared\Domain\Model\Identifier;
use Gtd\Shared\Domain\Model\UuidIdentifier;

final class Logement
{
    use AddressAwareTrait;

    public Identifier $id;
    public string $descriptif;
    public Identifier $mandataireId;
    public ?Identifier $proprietaireId = null;

    public function __construct(string $descriptif)
    {
        $this->descriptif = $descriptif;
        $this->id = UuidIdentifier::empty();
        $this->proprietaireId = UuidIdentifier::empty();
    }
}
