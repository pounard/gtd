<?php

declare(strict_types=1);

namespace Gtd\Domain\Courrier\Repository;

use Gtd\Domain\Courrier\Model\Courrier;
use Gtd\Shared\Domain\Repository\Repository;

interface CourrierRepository extends Repository
{
    public function create(string $text, string $titre): Courrier;
}
