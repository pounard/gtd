<?php

declare (strict_types=1);

namespace Gtd\Persistence\Location;

use Goat\Query\SelectQuery;
use Gtd\Application\Location\Query\PersonneReadModel;
use Gtd\Domain\Location\Model\Personne;
use Gtd\Domain\Location\Repository\PersonneRepository;
use Gtd\Shared\Application\Query\ListQuery;
use Gtd\Shared\Domain\Model\UuidIdentifier;
use Gtd\Shared\Persistence\AbstractGoatRepository;
use Gtd\Shared\Persistence\ListSort;

final class GoatPersonneRepository extends AbstractGoatRepository implements PersonneRepository, PersonneReadModel
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
        return 'personne';
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrator(): callable
    {
        return static function (array $row): Personne {
            $ret = new Personne($row['nom'], $row['prenom']);
            $ret->addrCity = $row['addr_city'];
            $ret->addrComplement = $row['addr_complement'];
            $ret->addrLine1 = $row['addr_line1'];
            $ret->addrLine2 = $row['addr_line2'];
            $ret->addrPostcode = $row['addr_postcode'];
            $ret->civilite = $row['civilite'];
            // $ret->dateNaissance = $row['date_naissance'];
            $ret->emailAddress = $row['mail'];
            $ret->id = new UuidIdentifier($row['id']);
            $ret->telephone = $row['telephone'];
            $ret->villeNaissance = $row['ville_naissance'];

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

            case 'nom':
                $select->orderBy('nom', $sqlSortOrder);

                return new ListSort('nom', $query->sortOrder);

            case 'prenom':
                $select->orderBy('prenom', $sqlSortOrder);

                return new ListSort('prenom', $query->sortOrder);

            default:
                $select->orderBy('nom', $sqlSortOrder);

                return new ListSort('nom', $query->sortOrder);
        }
    }
}
