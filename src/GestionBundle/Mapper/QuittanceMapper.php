<?php

declare(strict_types=1);

namespace GestionBundle\Mapper;

use GestionBundle\Entity\Quittance;
use Goat\Mapper\WritableSelectMapper;
use Goat\Query\Query;
use Goat\Runner\PagerResultIterator;
use Goat\Runner\RunnerInterface;

class QuittanceMapper extends WritableSelectMapper
{
    /**
     * Default contructor
     *
     * @param RunnerInterface $runner
     */
    public function __construct(RunnerInterface $runner)
    {
        parent::__construct($runner, Quittance::class, ['q.id'], $runner->select('quitance', 'q'));
    }

    /**
     * Get notes for task
     *
     * @return PagerResultIterator|Quittance[]
     */
    public function paginateForContract(int $contractId, int $limit = 0, int $page = 1) : PagerResultIterator
    {
        $select = $this
            ->createSelect()
            ->condition('q.id_contrat', $contractId)
            ->range($limit, ($page - 1) * $limit)
            ->orderBy('q.id', Query::ORDER_ASC)
        ;

        $total = $select->getCountQuery()->execute()->fetchField();
        $result = $select->execute([], ['class' => $this->getClassName()]);

        return new PagerResultIterator($result, $total, $limit, $page);
    }
}
