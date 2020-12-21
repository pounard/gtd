<?php

declare (strict_types=1);

namespace Gtd\Persistence\Location;

use Goat\Query\SelectQuery;
use Gtd\Application\Location\Query\QuittanceReadModel;
use Gtd\Domain\Location\Model\Quittance;
use Gtd\Domain\Location\Repository\QuittanceRepository;
use Gtd\Shared\Application\Query\ListQuery;
use Gtd\Shared\Domain\Model\UuidIdentifier;
use Gtd\Shared\Persistence\AbstractGoatRepository;
use Gtd\Shared\Persistence\ListSort;

final class GoatQuittanceRepository extends AbstractGoatRepository implements QuittanceRepository, QuittanceReadModel
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
        return 'quitance';
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrator(): callable
    {
        return static function (array $row): Quittance {
            $ret = new Quittance(
                $row['serial'],
                $row['date_start'],
                $row['date_stop'],
                $row['periode'],
                $row['loyer'],
                $row['provision_charges']
            );
            $ret->contratId = new UuidIdentifier($row['id_contrat']);
            $ret->datePaiement = $row['date_paiement'];
            $ret->id = new UuidIdentifier($row['id']);
            $ret->typePaiement = $row['type_paiement'];

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
