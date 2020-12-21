<?php

declare (strict_types=1);

namespace Gtd\Infrastructure\Goat\Normalization;

use Goat\Normalization\NameMappingStrategy;

/**
 * This class will convert things such as:
 *  - Gtd\Application\Location\Command\PaiementAddCommand
 *  - Gtd\Application\Location\Event\PaiementAddedEvent
 *  - Gtd\Domain\Location\Model\Paiement
 *
 * To respectively those:
 *  - G.App.Location.PaiementAddCommand
 *  - G.Dom.Location.PaiementAddedEvent
 *  - G.Dom.Location.Paiement
 *
 * Those are not *that* reduced in size, but it's enough to make them human
 * readable and suitable for potential heterogeneous applications exchange.
 *
 * Keeping "App" and "Domain" is a deliberate choice to deambiguate possible
 * conflicts between event, commands and model class names, especially if
 * someone forgot to suffix Command and Event classes.
 */
abstract class AbstractNameMappingStrategy implements NameMappingStrategy
{
    /**
     * {@inheritdoc}
     */
    public function logicalNameToPhpType(string $logicalName): string
    {
        $pieces = \explode('.', $logicalName);
        if (\count($pieces) < 3 || 'G' !== $pieces[0]) {
            return $logicalName; // Name does not belong to us.
        }

        switch ($pieces[1]) {
            case 'App':
                return 'Gtd\\Application\\' . $pieces[2] . '\\' . $this->getInfix() . '\\' . \implode('\\', \array_slice($pieces, 3));

            case 'Dom':
                return 'Gtd\\Domain\\' . $pieces[2] . '\\' . $this->getInfix() . '\\' . \implode('\\', \array_slice($pieces, 3));

            default:
                return $logicalName;  // Name does not belong to us.
        }
    }

    /**
     * {@inheritdoc}
     */
    public function phpTypeToLogicalName(string $phpType): string
    {
        $pieces = \explode('\\', $phpType);
        if (\count($pieces) < 5 || 'Gtd' !== $pieces[0]) {
            return $phpType; // Name does not belong to us.
        }

        // Note that $pieces[4] is supposedly $this->getInfix() so we just skip it.
        switch ($pieces[1]) {
            case 'Application':
                return 'G.App.' . $pieces[2] . '.' . \implode('.', \array_slice($pieces, 4));

            case 'Domain':
                return 'G.Dom.' . $pieces[2] . '.' . \implode('.', \array_slice($pieces, 4));

            default:
                return $phpType;  // Name does not belong to us.
        }
    }

    /**
     * Get infix to be replaced, such as "Command" or "Event".
     */
    abstract protected function getInfix(): string;
}
