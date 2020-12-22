<?php

declare (strict_types=1);

namespace Gtd\Persistence\Location;

use Goat\Query\SelectQuery;
use Gtd\Application\Location\Query\QuittanceReadModel;
use Gtd\Domain\Location\Model\Quittance;
use Gtd\Domain\Location\Repository\QuittanceRepository;
use Gtd\Shared\Application\Query\ListQuery;
use Gtd\Shared\Domain\Model\Identifier;
use Gtd\Shared\Domain\Model\UuidIdentifier;
use Gtd\Shared\Persistence\AbstractGoatRepository;
use Gtd\Shared\Persistence\ListSort;

final class GoatQuittanceRepository extends AbstractGoatRepository implements QuittanceRepository, QuittanceReadModel
{
    /**
     * {@inheritdoc}
     */
    public function create(
        Identifier $contratId,
        int $year,
        int $month,
        \DateTimeInterface $dateStart,
        \DateTimeInterface $dateStop,
        float $loyer, // @todo Monetary
        float $provisionCharges // @todo Monetary
    ): Quittance {
        if ($this->findForPeriode($contratId, $year, $month)) {
            throw new \DomainException("Une quittance existe déjà pour le contrat et la période données.");
        }

        return $this
            ->runner
            ->getQueryBuilder()
            ->insert($this->relation())
            ->values([
                'date_start' => $dateStart,
                'date_stop' => $dateStop,
                'id' => UuidIdentifier::random(),
                'id_contrat' => $contratId,
                'loyer' => $loyer,
                'month' => $month,
                'provision_charges' => $provisionCharges,
                'year' => $year,
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
    public function findForPeriode(
        Identifier $contratId,
        int $year,
        int $month
    ): ?Quittance {
        return $this
            ->select()
            ->where('id_contrat', $contratId)
            ->where('year', $year)
            ->where('month', $month)
            ->range(1)
            ->execute()
            ->setHydrator($this->hydrator())
            ->fetch()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function acquitte(
        Identifier $id,
        bool $gracieux,
        ?\DateTimeInterface $dateAcquittement
    ): Quittance {
        return $this
            ->runner
            ->getQueryBuilder()
            ->update($this->relation())
            ->sets([
                'acquitte' => true,
                'gracieux' => $gracieux,
                'date_acquittement' => $dateAcquittement,
            ])
            ->where('id', $id)
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
        return 'quittance';
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrator(): callable
    {
        return static function (array $row): Quittance {
            $ret = new Quittance(
                $row['year'],
                $row['month'],
                $row['date_start'],
                $row['date_stop'],
                $row['loyer'],
                $row['provision_charges']
            );
            $ret->contratId = new UuidIdentifier($row['id_contrat']);
            $ret->dateAcquittement = $row['date_acquittement'];
            $ret->id = new UuidIdentifier($row['id']);
            $ret->paiementId = $row['id_paiement'] ? new UuidIdentifier($row['id_paiement']) : null;
            $ret->acquitte = $row['acquitte'];
            $ret->gracieux = $row['gracieux'];

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
                case 'acquitte':
                    $select->where('acquitte', (bool) $values);
                    break;

                case 'contrat':
                    $select->where('id_contrat', $values);
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
                $select->orderBy('date_start', $sqlSortOrder);

                return new ListSort('date_start', $query->sortOrder);
        }
    }
}
