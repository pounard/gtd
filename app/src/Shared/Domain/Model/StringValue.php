<?php

declare (strict_types=1);

namespace Gtd\Shared\Domain\Model;

interface StringValue extends Comparable
{
    public function toString(): string;

    public function __toString(): string;
}
