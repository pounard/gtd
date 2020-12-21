<?php

declare(strict_types=1);

namespace AppBundle\Mapper;

use AppBundle\Entity\Note;
use Goat\Mapper\WritableSelectMapper;
use Goat\Query\Query;
use Goat\Runner\PagerResultIterator;
use Goat\Runner\RunnerInterface;

/**
 * Task notes mapper
 */
class NoteMapper extends WritableSelectMapper
{
    /**
     * Default contructor
     *
     * @param RunnerInterface $runner
     */
    public function __construct(RunnerInterface $runner)
    {
        parent::__construct($runner, Note::class, ['n.id'], $runner->select('task_comment', 'n'));
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
