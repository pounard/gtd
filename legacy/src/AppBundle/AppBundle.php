<?php

namespace AppBundle;

use AppBundle\Command\UnhideCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\HttpKernel\Bundle\Bundle;

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
