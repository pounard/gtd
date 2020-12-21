<?php

declare(strict_types=1);

namespace Gtd\Symfony\Shared\Controller;

use Goat\Normalization\Serializer;
use Gtd\Shared\Application\Query\ListQuery;
use Gtd\Shared\Application\Query\ReadModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractApiController extends AbstractController
{
    /**
     * Get default read model.
     */
    protected abstract function getReadModel(): ReadModel;

    /**
     * List controller.
     */
    public function list(Request $request, Serializer $serializer): Response
    {
        if ('' === ($body = $request->getContent())) {
            $body = '{}';
        }

        $query = $serializer
            ->unserialize(
                ListQuery::class,
                $request->getContentType() ?? 'application/json',
                $body
            )
        ;

        $response = $this->getReadModel()->list($query);

        foreach ($request->getAcceptableContentTypes() as $contentType) {
            $contentType = 'application/json';

            return new Response(
                $serializer->serialize($response, $contentType),
                200,
                ['Content-Type' => $contentType],
            );
        }

        throw $this->createNotFoundException();
    }
}
