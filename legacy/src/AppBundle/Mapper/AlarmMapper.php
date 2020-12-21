<?php

declare(strict_types=1);

namespace AppBundle\Mapper;

use AppBundle\Entity\Alarm;
use Goat\Mapper\WritableSelectMapper;
use Goat\Query\Query;
use Goat\Runner\PagerResultIterator;
use Goat\Runner\RunnerInterface;

/**
 * Task alarm mapper
 */
class AlarmMapper extends WritableSelectMapper
{
    /**
     * Default contructor
     *
     * @param RunnerInterface $runner
     */
    public function __construct(RunnerInterface $runner)
    {
        parent::__construct($runner, Alarm::class, ['a.id'], $runner->select('task_alarm', 'a'));
    }

    /**
     * Get notes for task
     *
     * @return PagerResultIterator|Alarm[]
     */
    public function paginateForTask(int $taskId, int $limit = 0, int $page = 1) : PagerResultIterator
    {
        $select = $this
            ->createSelect()
            ->condition('a.id_task', $taskId)
            ->range($limit, ($page - 1) * $limit)
            ->orderBy('a.id', Query::ORDER_ASC)
        ;

        $total = $select->getCountQuery()->execute()->fetchField();
        $result = $select->execute([], ['class' => $this->getClassName()]);

        return new PagerResultIterator($result, $total, $limit, $page);
    }
}
