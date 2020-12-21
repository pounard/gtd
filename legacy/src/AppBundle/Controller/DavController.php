<?php

namespace AppBundle\Controller;

use Sabre\DAV\Server as SabreServer;
use Sabre\HTTP\Request as SabreRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DavController extends AbstractAppController
{
    /**
     * Get *DAV server
     *
     * @param Request $request
     *
     * @return SabreServer
     */
    private function getDavServer(Request $request) : SabreServer
    {
        /** @var \Sabre\DAV\Server $server */
        $server = $this->get('app.dav.server');

        // Without this, Sabre give wrong URL to the outside world.
        $server->setBaseUri(rtrim($request->getBasePath(), '/') . '/dav/');

        return $server;
    }

    /**
     * Provide a task calendar
     */
    public function mainAction(Request $request, $incomingUrl)
    {
        $server = $this->getDavServer($request);

        $server->httpRequest = new SabreRequest(
            $request->getMethod(),
            $request->getPathInfo(),
            $request->headers->all(),
            $request->getContent()
        );

        return new StreamedResponse(function () use ($server) {
            $server->exec();
        });
    }
}
