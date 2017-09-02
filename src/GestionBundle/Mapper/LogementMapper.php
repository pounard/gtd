<?php

declare(strict_types=1);

namespace GestionBundle\Mapper;

use GestionBundle\Entity\Logement;
use Goat\Mapper\WritableSelectMapper;
use Goat\Runner\RunnerInterface;

class LogementMapper extends WritableSelectMapper
{
    /**
     * Default contructor
     *
     * @param RunnerInterface $runner
     */
    public function __construct(RunnerInterface $runner)
    {
        parent::__construct($runner, Logement::class, ['l.id'], $runner->select('logement', 'l'));
    }
}
