<?php

declare (strict_types=1);

namespace Gtd\Infrastructure\Goat\Normalization;

final class ModelNameMappingStrategy extends AbstractNameMappingStrategy
{
    /**
     * {@inheritdoc}
     */
    protected function getInfix(): string
    {
        return "Model";
    }
}
