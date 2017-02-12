<?php

namespace AppBundle;

use Symfony\Component\Console\Application;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use AppBundle\Command\UnhideCommand;

class AppBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function registerCommands(Application $application)
    {
        $application->add(new UnhideCommand());
    }
}
