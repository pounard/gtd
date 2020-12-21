<?php

declare (strict_types=1);

namespace Gtd\Persistence\Location;

use Goat\Query\SelectQuery;
use Gtd\Application\Location\Query\PaiementReadModel;
use Gtd\Domain\Location\Model\Paiement;
use Gtd\Domain\Location\Repository\PaiementRepository;
use Gtd\Shared\Application\Query\ListQuery;
use Gtd\Shared\Domain\Model\Identifier;
use Gtd\Shared\Domain\Model\UuidIdentifier;
use Gtd\Shared\Persistence\AbstractGoatRepository;
use Gtd\Shared\Persistence\ListSort;

final class GoatPaiementRepository extends AbstractGoatRepository implements PaiementRepository, PaiementReadModel
{
    /**
     * {@inheritdoc}
     */
    public function create(
        Identifier $personneId,
        float $montant, // @todo monetary
        \DateTimeInterface $date,
        ?string $typePaiement
    ): Paiement {
        return $this
            ->runner
            ->getQueryBuilder()
            ->insert($this->relation())
            ->values([
                'date' => $date,
                'id' => UuidIdentifier::random(),
                'id_personne' => $personneId,
                'montant' => $montant,
                'type_paiement' => $typePaiement,
            ])
            ->returning()
            ->execute()
            ->setHydrator($this->hydrator())
            ->fetch()
        ;
    }

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
        return 'paiement';
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrator(): callable
    {
        return static function (array $row): Paiement {
            $ret = new Paiement(
                $row['date'],
                $row['montant']
            );
            $ret->id = new UuidIdentifier($row['id']);
            $ret->personneId = new UuidIdentifier($row['id_personne']);
            $ret->typePaiement = $row['type_paiement'];

            return $ret;
        };
    }

    /**
     * {@inheritdoc}
     */
    protected function applyListConditions(ListQuery $query, SelectQuery $select): void
    {
        foreach ($query->query as $column => $values) {
            switch ($column) {

                case 'personne':
                    $select->where('id_personne', $values);
                    break;

                default:
                    throw new \DomainException(\sprintf("Filtrer sur la colonne '%s' n'est pas possible.", $column));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function applyListSort(ListQuery $query, SelectQuery $select, int $sqlSortOrder): ListSort
    {
        switch ($query->sortOrder ?? 'none') {
            default:
                $select->orderBy('date', $sqlSortOrder);

                return new ListSort('date', $query->sortOrder);
        }
    }
}

