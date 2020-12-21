<?php

declare(strict_types=1);

namespace AppBundle\Mapper;

use AppBundle\Entity\Task;
use Goat\Mapper\WritableSelectMapper;
use Goat\Query\ExpressionRaw;
use Goat\Runner\RunnerInterface;

/**
 * Task mapper
 */
class TaskMapper extends WritableSelectMapper
{
    /**
     * Default contructor
     *
     * @param RunnerInterface $runner
     */
    public function __construct(RunnerInterface $runner)
    {
        $select = $runner
            ->select('task', 't')
            ->column('t.*')
            ->columnExpression('count(c.id)', 'note_count')
            ->columnExpression('count(a.id)', 'has_alarm')
            ->leftJoin('task_comment', 'c.id_task = t.id',  'c')
            ->leftJoin('task_alarm', 'a.id_task = t.id',  'a')
            ->groupBy('t.id')
        ;

        parent::__construct($runner, Task::class, ['t.id'], $select);
    }

    /**
     * {@inheritdoc}
     */
    public function exists($criteria) : bool
    {
        return (bool)$this
            ->runner
            ->select('task', 't')
            ->column(new ExpressionRaw('1'))
            ->expression($this->createWhereWith($criteria))
            ->range(1)
            ->execute()
            ->fetchField()
        ;
    }
}
