<?php

declare(strict_types=1);

namespace Gtd\Application\Courrier\Command;

use Gtd\Domain\Courrier\Model\Courrier;
use Gtd\Shared\Domain\Model\Identifier;
use MakinaCorpus\CoreBus\CommandBus\Command;
use MakinaCorpus\CoreBus\CommandBus\Response;

/**
 * Save a letter.
 */
final class CourrierAddCommand implements Command
{
    public string $text;
    public string $titre;

    public function __construct(Identifier $contratId)
    {
        $this->contratId = $contratId;
    }
}

/**
 * Save a letter response.
 */
final class CourrierAddResponse implements Response
{
    public Courrier $courrier;

    public function __construct(Courrier $courrier)
    {
        $this->courrier = $courrier;
    }
}

