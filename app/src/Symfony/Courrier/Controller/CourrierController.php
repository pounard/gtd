<?php

declare(strict_types=1);

namespace Gtd\Symfony\Courrier\Controller;

use Gtd\Application\Courrier\Query\CourrierReadModel;
use Gtd\Domain\Courrier\Model\Courrier;
use Gtd\Shared\Domain\Model\UuidIdentifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class CourrierController extends AbstractController
{
    /**
     * Letter template display.
     */
    public function template(): Response
    {
        return $this->render('@Shared/Letter/template.html.twig');
    }

    /**
     * Return one courrier.
     */
    public function courrier(string $id, CourrierReadModel $courrierReadModel): Response
    {
        $courrier = $courrierReadModel->find(new UuidIdentifier($id));

        if (!$courrier) {
            throw $this->createNotFoundException();
        }

        \assert($courrier instanceof Courrier);

        return new Response($courrier->text);
    }
}
