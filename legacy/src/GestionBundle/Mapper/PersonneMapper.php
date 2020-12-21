<?php

declare(strict_types=1);

namespace GestionBundle\Mapper;

use GestionBundle\Entity\Personne;
use Goat\Mapper\WritableSelectMapper;
use Goat\Runner\RunnerInterface;

class PersonneMapper extends WritableSelectMapper
{
    /**
     * Default contructor
     *
     * @param RunnerInterface $runner
     */
    public function __construct(RunnerInterface $runner)
    {
        parent::__construct($runner, Personne::class, ['p.id'], $runner->select('personne', 'p'));
    }
}
