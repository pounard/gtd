<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;

/**
 * Allow redirections following referer
 */
trait RefererControllerTrait
{
    /**
     * Should be defined in default Symfony controller
     *
     * @param string $route
     * @param array $parameters
     * @param int $status
     */
    abstract protected function redirectToRoute($route, array $parameters = [], $status = 302);

    /**
     * Should be defined in default Symfony controller
     *
     * @param string $id
     */
    abstract protected function get($id);

    /**
     * @param Request $request
     *
     * @return Router
     */
    protected function getRouter(Request $request) : Router
    {
        return $this->get('router');
    }

    /**
     * Get action URL from request
     *
     * @param Request $request
     *
     * @return string
     */
    protected function getFormActionUrl(Request $request) : string
    {
        list($route, $arguments) = $this->getRequestRouteAndParams($request);

        $destination = $request->query->get('_from');
        if ($destination) {
            $arguments['_from'] = $destination;
        }

        return $this->generateUrl($route, $arguments);
    }

    /**
     * Get given request route and route parameters
     *
     * @param Request $request
     *
     * @return array
     */
    private function getRequestRouteAndParams(Request $request) : array
    {
        $route = $request->attributes->get('_route');

        if ($route) {
            return [
                $route,
                $request->attributes->get('_route_params'),
            ];
        }

        $forwarded = $request->attributes->get('_forwarded');
        if ($forwarded instanceof ParameterBag) {
            return [
                $forwarded->get('_route'),
                $forwarded->get('_route_params', []),
            ];
        }
    }

    /**
     * Get referer parameters from request
     *
     * @param Request $request
     *
     * @return array
     */
    private function getRefererParams(Request $request) : array
    {
        $referer = $request->headers->get('referer');
        if (empty($referer)) {
            throw new \RuntimeException("Client browser provided no referer");
        }

        $baseUrl = $request->getBaseUrl();

        if ($baseUrl) {
            $pos = strpos($referer, $baseUrl);
        } else {
            $pos = 0;
        }

        $lastPath = substr($referer, $pos + strlen($baseUrl));

        return $this->getRouter($request)->match($lastPath);
    }

    /**
     * Attempt to find either a _from parameter or a valid referer on the given
     * request then generate a redirect response toward this target; fallbacks
     * on default route given if nothing was found.
     *
     * @param Request $request
     * @param string $defaultRoute
     * @param array $defaultParameters
     * @param int $status
     * @param bool $useDestination
     *
     * @return Response
     */
    protected function redirectToReferer(Request $request, string $defaultRoute = null, array $defaultParameters = [], int $status = 302, bool $useDestination = true) : Response
    {
        if ($useDestination && $request->query->has('_from')) {
            return new RedirectResponse($request->getBaseUrl() . '/' . trim($request->query->get('_from'), '/'));
        }

        try {
            $params = $this->getRefererParams($request);

            return new RedirectResponse($this->generateUrl($params['_route'], ['slug' => $params['slug']]), $status);

        } catch (\RuntimeException $e) {

            // Anything could go wrong, ensure at least fallback will work
            if ($defaultRoute) {
                return new RedirectResponse($this->generateUrl($defaultRoute, $defaultParameters), $status);
            }

            list($route, $arguments) = $this->getRequestRouteAndParams($request);

            return new RedirectResponse($this->generateUrl($route, $arguments), $status);
        }
    }
}
