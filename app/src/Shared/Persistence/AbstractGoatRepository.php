<?php

declare(strict_types=1);

namespace Gtd\Shared\Persistence;

use Goat\Query\ExpressionColumn;
use Goat\Query\ExpressionRelation;
use Goat\Query\Query;
use Goat\Query\SelectQuery;
use Goat\Runner\Runner;
use Gtd\Shared\Application\Query\ListQuery;
use Gtd\Shared\Application\Query\ListResponse;
use Gtd\Shared\Application\Query\ReadModel;
use Gtd\Shared\Domain\Repository\Repository;

abstract class AbstractGoatRepository implements Repository, ReadModel
{
    protected Runner $runner;

    public function __construct(Runner $runner)
    {
        $this->runner = $runner;
    }

    /**
     * Return primary key of this table (doesn't support multiple column keys yet).
     *
     * @return string|ExpressionColumn
     */
    protected abstract function columnId() /* : string|ExpressionColumn */;

    /**
     * Get table name or expression.
     *
     * @return string|ExpressionRelation
     */
    protected abstract function relation() /* : string|ExpressionRelation */;

    /**
     * Row hydrator.
     *
     * @return callable
     *   Takes only argument: row array from database.
     */
    protected abstract function hydrator(): callable;

    /**
     * Apply list filters from incomming query. 
     */
    protected abstract function applyListConditions(ListQuery $query, SelectQuery $select): void;

    /**
     * Apply sort from incomming query.
     */
    protected abstract function applyListSort(ListQuery $query, SelectQuery $select, int $sqlSortOrder): ListSort;

    /**
     * {@inheritdoc}
     */
    public function find($id): ?object
    {
        return $this
            ->select(true)
            ->where($this->columnId(), $id)
            ->range(1)
            ->setOption('hydrator', $this->hydrator())
            ->execute()
            ->fetch()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($id): bool
    {
        return (bool)$this
            ->select(false)
            ->columnExpression('true')
            ->where($this->columnId(), $id)
            ->range(1)
            ->execute()
            ->fetchField()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function list(ListQuery $query): ListResponse
    {
        $select = $this->select(true)->range($query->limit, $query->computeOffset());

        $this->applyListConditions($query, $select);

        $total = $select->getCountQuery()->execute()->fetchField();

        $order = ListQuery::ORDER_DESC === $query->sortOrder ? Query::ORDER_DESC : Query::ORDER_ASC;
        $appliedSort = $this->applyListSort($query, $select, $order);

        $result = $select->setOption('hydrator', $this->hydrator())->execute();

        return new ListResponse(
            $result,
            $result->count(),
            $query->limit,
            $query->page,
            $total,
            $appliedSort->column,
            $appliedSort->order
        );
    }

    /**
     * find() method implementation for use case where there is a single table.
     *
     * Raise error when there is no object matching returned.
     */
    protected function findOneOrDie($id) /* : object */
    {
        $ret = $this->doFindOne($id);

        if (!$ret) {
            $this->objectDoesNotExistError($id);
        }

        return $ret;
    }

    /**
     * Construit la requête SELECT de base, vous pouvez surcharger.
     */
    protected function select(bool $withColumns = true): SelectQuery
    {
        $ret = $this->runner->getQueryBuilder()->select($this->relation());

        if ($withColumns) {
            $ret->column('*');
        }

        return $ret;
    }

    /**
     * L'objet existe déjà message.
     */
    protected function objectAlreadyExistsMessage($id): string
    {
        return \sprintf("L'objet '%s' existe déjà.", (string) $id);
    }

    /**
     * Jette une exception pour dire que objet existe déjà.
     */
    protected function objectAlreadyExistsError($id, ?\Throwable $error = null)
    {
        $message = $this->objectAlreadyExistsMessage($id);

        if ($error) {
            throw new \InvalidArgumentException($message, $error->getCode(), $error);
        }

        throw new \InvalidArgumentException($message);
    }

    /**
     * L'objet n'existe pas message.
     */
    protected function objectDoesNotExistMessage($id): string
    {
        return \sprintf("L'objet '%s' n'existe pas.", (string) $id);
    }

    /**
     * Jette une exception pour dire que objet n'existe pas.
     */
    protected function objectDoesNotExistError($id, ?\Throwable $error = null)
    {
        $message = $this->objectDoesNotExistMessage($id);

        if ($error) {
            throw new \InvalidArgumentException($message, $error->getCode(), $error);
        }

        throw new \InvalidArgumentException($message);
    }
}
