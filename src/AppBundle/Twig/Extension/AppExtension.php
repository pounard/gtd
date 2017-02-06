<?php

namespace AppBundle\Twig\Extension;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouterInterface;

/**
 * Various helpers
 */
class AppExtension extends \Twig_Extension
{
    private $requestStack;
    private $router;

    /**
     * Default constructor
     *
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack, RouterInterface $router)
    {
        $this->requestStack = $requestStack;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('app_back_to_url', [$this, 'backToUrl'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('app_back_to_text', [$this, 'backToText']),
            new \Twig_SimpleFunction('app_back_to_icon', [$this, 'backToIcon']),
        ];
    }

    /**
     * Generate back to URL
     *
     * @param string $defaultRoute
     * @param bool $withQuery
     *
     * @return string
     */
    public function backToUrl(string $defaultRoute, bool $withQuery = true) : string
    {
        $request  = $this->requestStack->getMasterRequest();
        $query    = [];
        $from     = $request->get('_from');

        if ($withQuery) {
            $query = $request->query->all();

            // Loosing this if we are generating URL from it
            unset($query['_from']);
        }


        if ($from) {
            try {
                $attributes = $this->router->match($from);
                $query = array_merge($query, array_diff_key($attributes, ['_controller' => '', '_route' => '']));

                return $this->router->generate($attributes['_route'], $query);

            } catch (ResourceNotFoundException $e) {
                // Fallback silently on default route
            }
        }

        return $this->router->generate($defaultRoute, $query);
    }

    /**
     * Generate back to text
     *
     * @param string $defaultText
     *
     * @return string
     */
    public function backToText(string $defaultText) : string
    {
        // @todo find text from url
        return $defaultText;
    }

    /**
     * Generate back to icon
     *
     * @param string $defaultIcon
     *
     * @return string
     */
    public function backToIcon(string $defaultIcon) : string
    {
        $request  = $this->requestStack->getMasterRequest();
        $from     = $request->get('_from');

        if ($from) {
            return 'remove';
        }

        return $defaultIcon;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_bundle';
    }
}
