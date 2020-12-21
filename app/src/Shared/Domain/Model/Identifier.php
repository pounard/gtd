<?php

declare (strict_types=1);

namespace Gtd\Shared\Domain\Model;

interface Identifier extends StringValue
{
    public function isEmpty(): bool;

    /**
     * Creates an empty instance.
     *
     * @return static
     */
    public static function empty() /* : static */;
}
