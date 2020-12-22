<?php

declare(strict_types=1);

namespace Gtd\Domain\Courrier\Service;

use Twig\Environment;

/**
 * @tainted
 *   En théorie, on ne devrait pas avoir d'environnement Twig ici. Ce service
 *   devrait sûrement être une interface lui aussi.
 */
final class GenerateurCourrier
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }
}
