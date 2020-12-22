<?php

declare (strict_types=1);

namespace Gtd\Persistence\Courrier;

use Goat\Query\SelectQuery;
use Gtd\Application\Courrier\Query\CourrierReadModel;
use Gtd\Domain\Courrier\Model\Courrier;
use Gtd\Domain\Courrier\Repository\CourrierRepository;
use Gtd\Shared\Application\Query\ListQuery;
use Gtd\Shared\Domain\Model\UuidIdentifier;
use Gtd\Shared\Persistence\AbstractGoatRepository;
use Gtd\Shared\Persistence\ListSort;

final class GoatCourrierRepository extends AbstractGoatRepository implements CourrierRepository, CourrierReadModel
{
    /**
     * {@inheritdoc}
     */
    public function create(string $text, string $titre): Courrier
    {
        return $this
            ->runner
            ->getQueryBuilder()
            ->insert($this->relation())
            ->values([
                'date' => new \DateTimeImmutable(),
                'id' => UuidIdentifier::random(),
                'text' => $text,
                'titre' => $titre,
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
        return 'courrier';
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrator(): callable
    {
        return static function (array $row): Courrier {
            $ret = new Courrier(
                $row['text'],
                $row['titre']
            );
            $ret->id = new UuidIdentifier($row['id']);
            $ret->date = $row['date'];

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

