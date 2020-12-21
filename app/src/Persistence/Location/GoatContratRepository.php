<?php

declare (strict_types=1);

namespace Gtd\Persistence\Location;

use Goat\Query\SelectQuery;
use Gtd\Application\Location\Query\ContratReadModel;
use Gtd\Domain\Location\Model\Contrat;
use Gtd\Domain\Location\Repository\ContratRepository;
use Gtd\Shared\Application\Query\ListQuery;
use Gtd\Shared\Domain\Model\UuidIdentifier;
use Gtd\Shared\Persistence\AbstractGoatRepository;
use Gtd\Shared\Persistence\ListSort;

final class GoatContratRepository extends AbstractGoatRepository implements ContratRepository, ContratReadModel
{
    /**
     * {@inheritdoc}
     */
    protected function columnId()
    {
        return 'id';
    }

    /**
     * {@inheritdoc}
     */
    protected function relation()
    {
        return 'contrat';
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrator(): callable
    {
        return static function (array $row): Contrat {
            $ret = new Contrat(
                $row['date_start'],
                $row['loyer'],
                $row['provision_charges']
            );
            $ret->dateStop = $row['date_stop'];
            $ret->id = new UuidIdentifier($row['id']);
            $ret->locataireId = new UuidIdentifier($row['id_locataire']);
            $ret->logementId = new UuidIdentifier($row['id_logement']);

            return $ret;
        };
    }

    /**
     * {@inheritdoc}
     */
    protected function applyListConditions(ListQuery $query, SelectQuery $select): void
    {
        // Nothing here yet.
    }

    /**
     * {@inheritdoc}
     */
    protected function applyListSort(ListQuery $query, SelectQuery $select, int $sqlSortOrder): ListSort
    {
        switch ($query->sortOrder ?? 'none') {
            default:
                $select->orderBy('date_start', $sqlSortOrder);

                return new ListSort('date_start', $query->sortOrder);
        }
    }
}

