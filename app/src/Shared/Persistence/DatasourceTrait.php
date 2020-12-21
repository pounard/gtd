<?php

namespace IrpAuto\SoliPre\Infrastructure\GoatQuery\Persistence;

use Goat\Query\ExpressionLike;
use IrpAuto\Common\Domain\Normalize;
use IrpAuto\Common\Muna;

/**
 * @codeCoverageIgnore
 *   This should be tested functionnaly as controller tests.
 */
trait DatasourceTrait
{
    /**
     * Parse date if possible.
     */
    protected function fixDate(string $value, bool $endOfDay = false): ?\DateTimeImmutable
    {
        if (empty($value)) {
            return null;
        }
        if (!$date = \DateTimeImmutable::createFromFormat('d/m/Y H:i:s', \sprintf("%s 00:00:00", $value), new \DateTimeZone('UTC'))) {
            $date = Normalize::date($value, true);
        }

        /** @var \DateTimeImmutable $date */
        if ($date) {
            if (!$endOfDay) {
                return $date;
            }

            // Add one day, then go to midnight.
            $date = $date->add(new \DateInterval('P1D'));

            return \DateTimeImmutable::createFromFormat(
                "Y-m-d H:i:s",
                \sprintf("%s 00:00:00", $date->format('Y-m-d'))
            );
        }
    }

    /**
     * Trim string.
     */
    protected function fixString(string $value): string
    {
        return \trim($value);
    }

    /**
     * Parse et valide le MUNA fourni, et retourne null s'il est vraiment très
     * invalide ou incomplet, si null est retourné, c'est à la charge du code
     * appelant de tenter une requête avec un LIKE.
     */
    protected function fixMuna(string $value): ?Muna
    {
        if ($value) {
            try {
                $muna = Muna::fromString($value, true);
                if (!$muna->isComplete()) {
                    if (\strlen($muna) < 8) {
                        return null;
                    }

                    return $muna->toValid();
                }

                return $muna;
            } catch (\Throwable $e) {
            }
        }

        return null;
    }

    /**
     * Gère une expression LIKE.
     *
     * @return mixed
     *   Peut être utilisé par Query::expression().
     */
    protected function handleContains(string $column, string $value)
    {
        return ExpressionLike::iLike($column, '%?%', $this->fixString($value));
    }

    /**
     * Gère une expression LIKE.
     *
     * @return mixed
     *   Peut être utilisé par Query::expression().
     */
    protected function handleStartsWith(string $column, string $value)
    {
        return ExpressionLike::iLike($column, '?%', $this->fixString($value));
    }

    /**
     * Gère un code MUNA avec une expression LIKE si incomplet.
     *
     * @return mixed
     *   Peut être utilisé par Query::expression().
     */
    protected function handleMuna(string $column, string $value)
    {
        $value = $this->fixString($value);

        if ($muna = $this->fixMuna($value)) {
            // SQL "LIKE 'value'" without wildcards behave like =.
            // That's enough for us, we'll see if it cause perf. problems.
            return ExpressionLike::like($column, '?', $muna->toString());
        }

        return $this->handleStartsWith($column, $value);
    }
}
