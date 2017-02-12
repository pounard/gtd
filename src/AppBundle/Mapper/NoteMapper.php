<?php

declare(strict_types=1);

namespace AppBundle\Mapper;

use AppBundle\Entity\Note;
use Goat\Core\Client\ConnectionInterface;
use Goat\Mapper\WritableSelectMapper;

/**
 * Task notes mapper
 */
class NoteMapper extends WritableSelectMapper
{
    /**
     * Default contructor
     *
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        parent::__construct($connection, Note::class, ['n.id'], $connection->select('task_comment', 'n'));
    }
}
