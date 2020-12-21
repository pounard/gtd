<?php

declare (strict_types=1);

namespace Gtd\Infrastructure\Goat\Normalization;

final class EventNameMappingStrategy extends AbstractNameMappingStrategy
{
    /**
     * {@inheritdoc}
     */
    protected function getInfix(): string
    {
        return "Event";
    }
}
