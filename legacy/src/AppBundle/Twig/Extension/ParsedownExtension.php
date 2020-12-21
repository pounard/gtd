<?php

namespace AppBundle\Twig\Extension;

/**
 * Parsedown twig extension
 */
class ParsedownExtension extends \Twig_Extension
{
    /**
     * Default constructor
     */
    public function __construct()
    {
        $this->markdownParser = new \Parsedown();
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            'markdown' => new \Twig_SimpleFilter('markdown',[$this, 'parsedown'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Apply markdown filter on text
     */
    public function parsedown($str)
    {
        return $this->markdownParser->text($str);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'parsedown';
    }
}
