<?php

declare(strict_types=1);

namespace AppBundle\Mapper;

use AppBundle\Entity\Task;
use Goat\Core\Client\ConnectionInterface;
use Goat\Core\Query\ExpressionRaw;
use Goat\Mapper\WritableSelectMapper;

/**
 * Task mapper
 */
class TaskMapper extends WritableSelectMapper
{
    /**
     * Default contructor
     *
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $select = $connection
            ->select('task', 't')
            ->column('t.*')
             ->columnExpression('count(c.id)', 'note_count')
             ->leftJoin('task_comment', 'c.id_task = t.id',  'c')
             ->groupBy('t.id')
        ;

        parent::__construct($connection, Task::class, ['t.id'], $select);
    }

    /**
     * {@inheritdoc}
     */
    public function exists($criteria) : bool
    {
        return (bool)$this
            ->connection
            ->select('task', 't')
            ->column(new ExpressionRaw('1'))
            ->expression($this->createWhereWith($criteria))
            ->range(1)
            ->execute()
            ->fetchField()
        ;
    }
}
