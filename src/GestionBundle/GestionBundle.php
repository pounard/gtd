<?php

namespace GestionBundle;

use GestionBundle\DependencyInjection\GestionBundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GestionBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new GestionBundleExtension();
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        setlocale(LC_TIME, 'fr_FR.UTF8', 'fr.UTF8', 'fr_FR.UTF-8', 'fr.UTF-8');
    }
}
