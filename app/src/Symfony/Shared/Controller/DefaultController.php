<?php

declare(strict_types=1);

namespace Gtd\Symfony\Shared\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class DefaultController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('@Shared/index.html.twig');
    }
}
