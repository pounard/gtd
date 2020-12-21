<?php

declare(strict_types=1);

namespace Gtd\Symfony\Shared\Controller;

use Goat\Normalization\NameMap;
use Goat\Normalization\Serializer;
use MakinaCorpus\CoreBus\CommandBus\SynchronousCommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ApiEndpointController extends AbstractController
{
    /**
     * Receive and consume command.
     */
    public function dispatch(Request $request, Serializer $serializer, SynchronousCommandBus $commandBus, NameMap $nameMap): Response
    {
        if (!($type = $request->get('type')) || !$type = \trim($type)) {
            throw $this->createNotFoundException();
        }
        $type = $nameMap->logicalNameToPhpType(NameMap::CONTEXT_COMMAND, $type);

        if (!($data = $request->getContent()) || !$data = \trim($data)) {
            $data = \trim($data);
        }

        $command = $serializer->unserialize($type, $request->getContentType() ?? 'application/json', $data);
        $response = $commandBus->dispatchCommand($command);

        if ($response && $response->isReady()) {
            return $this->json([
                'success' => true,
                'response' => $serializer->serialize(
                    $response->get(),
                    'application/json'
                ),
            ]);
        }

        return $this->json(['success' => true]);
    }
}
