<?php

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    MakinaCorpus\CoreBus\Bridge\Symfony\CoreBusBundle::class => ['all' => true],
    Goat\Query\Symfony\GoatQueryBundle::class => ['all' => true],
    Goat\Bridge\Symfony\GoatBundle::class => ['all' => true],
    MakinaCorpus\Calista\Bridge\Symfony\CalistaBundle::class => ['all' => true],
    Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class => ['dev' => true, 'test' => true],
    // Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    // Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
    Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],
    Symfony\WebpackEncoreBundle\WebpackEncoreBundle::class => ['all' => true],
    Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
];
