<?php

declare(strict_types=1);

namespace Gtd\Domain\Location\Service;

use Gtd\Domain\Location\Model\Logement;
use Gtd\Domain\Location\Model\Personne;
use Gtd\Domain\Location\Model\Quittance;
use Twig\Environment;

final class QuittanceCourrierGenerator
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Generate courrier from quittances and return raw contents.
     */
    public function generateQuittancesCourrier(
        Logement $logement,
        Personne $locataire,
        Personne $proprietaire,
        array $quittances
    ): string {
        if (empty($quittances)) {
            throw new \DomainException("Impossible de générer un courrier de quittances acquittées sans quittances.");
        }

        $from = $to = null;
        $total = 0.0;

        \uasort($quittances, fn (Quittance $a, Quittance $b) => $a->dateStart > $b->dateStart ? 1 : -1);

        foreach ($quittances as $quittance) {
            \assert($quittance instanceof Quittance);

            $total += $quittance->loyer + $quittance->provisionCharges;

            if (!$from || $from > $quittance->dateStart) {
                $from = $quittance->dateStart;
            }
            if (!$to || $to < $quittance->dateStop) {
                $to = $quittance->dateStop;
            }
        }

        return $this->twig->render('@Location/Courrier/quittances-acquittes.html.twig', [
            'logement' => $logement,
            'locataire' => $locataire,
            'quittances' => $quittances,
            'proprietaire' => $proprietaire,
            'from' => $from,
            'to' => $to,
            'total' => $total,
        ]);
    }
}
