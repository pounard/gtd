<?php

declare (strict_types=1);

namespace Gtd\Shared\Domain\Model;

interface Comparable
{
    public function equals(Comparable $other): bool;
}
