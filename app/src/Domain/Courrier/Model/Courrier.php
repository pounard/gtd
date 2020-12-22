<?php

declare(strict_types=1);

namespace Gtd\Domain\Courrier\Model;

use Gtd\Shared\Domain\Model\Identifier;
use Gtd\Shared\Domain\Model\UuidIdentifier;

final class Courrier
{
    public Identifier $id;
    public string $titre;
    public \DateTimeInterface $date;
    public string $text;

    public function __construct(string $text, string $titre)
    {
        $this->date = new \DateTimeImmutable();
        $this->id = UuidIdentifier::empty();
        $this->text = $text;
        $this->titre = $titre;
    }
}
