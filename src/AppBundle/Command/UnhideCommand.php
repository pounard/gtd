<?php

namespace AppBundle\Command;

use Goat\Core\Query\Where;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Unhide tasks for which the unhide timestamp has passed.
 */
class UnhideCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('gtd:unhide')
            ->setDescription('Unhide tasks for which the unhide timestamp has passed')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Goat\Core\Client\ConnectionInterface $database */
        $database = $this->getContainer()->get('goat.session');

        $affectedRows = $database
            ->update('task')
            ->set('is_hidden', 0)
            ->condition('is_hidden', 1)
            ->expression('ts_unhide is not null')
            ->condition('ts_unhide', new \DateTime(), Where::LESS_OR_EQUAL)
            ->execute()
            ->countRows()
        ;

        if (!$affectedRows) {
            $output->writeln("no task to unhide");
        } else {
            $output->writeln(sprintf("%d tasks were unhidden", $affectedRows));
        }
    }
}
