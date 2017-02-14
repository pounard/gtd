<?php

declare(strict_types=1);

namespace AppBundle\Mapper;

use AppBundle\Entity\Note;
use Goat\Core\Client\ConnectionInterface;
use Goat\Core\Client\PagerResultIterator;
use Goat\Core\Query\Query;
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

    /**
     * Get notes for task
     *
     * @return PagerResultIterator|Note[]
     */
    public function paginateForTask(int $taskId, int $limit = 0, int $page = 1) : PagerResultIterator
    {
        $select = $this
            ->createSelect()
            ->condition('n.id_task', $taskId)
            ->range($limit, ($page - 1) * $limit)
            ->orderBy('n.ts_added', Query::ORDER_ASC)
            ->orderBy('n.id', Query::ORDER_ASC)
        ;

        $total = $select->getCountQuery()->execute()->fetchField();
        $result = $select->execute([], ['class' => $this->getClassName()]);

        return new PagerResultIterator($result, $total, $limit, $page);
    }
}
