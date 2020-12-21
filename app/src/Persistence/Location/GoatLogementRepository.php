<?php

declare (strict_types=1);

namespace Gtd\Persistence\Location;

use Goat\Query\SelectQuery;
use Gtd\Application\Location\Query\LogementReadModel;
use Gtd\Domain\Location\Model\Logement;
use Gtd\Domain\Location\Repository\LogementRepository;
use Gtd\Shared\Application\Query\ListQuery;
use Gtd\Shared\Domain\Model\UuidIdentifier;
use Gtd\Shared\Persistence\AbstractGoatRepository;
use Gtd\Shared\Persistence\ListSort;

final class GoatLogementRepository extends AbstractGoatRepository implements LogementRepository, LogementReadModel
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
        return 'logement';
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrator(): callable
    {
        return static function (array $row): Logement {
            $ret = new Logement(
                $row['descriptif']
            );
            $ret->mandataireId = new UuidIdentifier($row['id_mandataire']);
            $ret->proprietaireId = $row['id_proprietaire'] ? new UuidIdentifier($row['id_proprietaire']) : null;
            $ret->id = new UuidIdentifier($row['id']);

            $ret->addrCity = $row['addr_city'];
            $ret->addrComplement = $row['addr_complement'];
            $ret->addrLine1 = $row['addr_line1'];
            $ret->addrLine2 = $row['addr_line2'];
            $ret->addrPostcode = $row['addr_postcode'];

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
                $select->orderBy('descriptif', $sqlSortOrder);

                return new ListSort('descriptif', $query->sortOrder);
        }
    }
}
