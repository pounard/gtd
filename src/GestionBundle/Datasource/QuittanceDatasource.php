<?php

namespace GestionBundle\Datasource;

use GestionBundle\Entity\Quittance;
use Goat\Query\Query as DatabaseQuery;
use Goat\Query\SelectQuery;
use Goat\Runner\RunnerInterface;
use MakinaCorpus\Calista\Datasource\AbstractDatasource;
use MakinaCorpus\Calista\Datasource\Filter;
use MakinaCorpus\Calista\Datasource\Query;

class QuittanceDatasource extends AbstractDatasource
{
    private $database;

    /**
     * Default constructor
     *
     * @param RunnerInterface $database
     */
    public function __construct(RunnerInterface $database)
    {
        $this->database = $database;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemClass()
    {
        return Quittance::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new Filter('contract', "Contract identifier"),
            new Filter('locataire', "Locataire identifier"),
            new Filter('logement', "Logement identifier"),
            (new Filter('settled', "Settled"))->setChoicesMap([1 => "Yes", 0 => "No"]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getSorts()
    {
        return [
            'date_paiement' => 'Paiement date',
            'date_start'    => "Start date",
            'date_stop'     => "End date",
            'loyer'         => "Loyer",
            'provisions'    => "Provisions sur charges",
            'serial'        => 'Number',
            'type_paiement' => 'Paiment type',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsStreaming()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsPagination()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsFulltextSearch()
    {
        return false;
    }

    protected function applyFilters(SelectQuery $select, Query $query)
    {
        if ($query->has('contract')) {
            $select->condition('q.id_contrat', $query->get('contract'));
        }

        if ($query->has('locataire')) {
            $select->condition('q.id_locataire', $query->get('locataire'));
        }

        if ($query->has('logement')) {
            $select->condition('q.id_logement', $query->get('logement'));
        }

        if ($query->has('settled')) {
            if ($query->get('settled')) {
                $select->getWhere()->isNotNull('q.date_paiement');
            } else {
                $select->getWhere()->isNull('q.date_paiement');
            }
        }
    }

    protected function processRange(SelectQuery $select, Query $query)
    {
        $select->range($query->getLimit(), $query->getOffset());

        if ($query->hasSortField()) {

            switch ($query->getSortField()) {
                case 'date_paiement':
                    $column = 'q.date_paiement';
                    break;
                case 'date_start':
                    $column = 'q.date_start';
                    break;
                case 'date_stop':
                    $column = 'q.date_stop';
                    break;
                case 'loyer':
                    $column = 'q.loyer';
                    break;
                case 'provisions':
                    $column = 'q.provision_charges';
                    break;
                case 'serial':
                    $column = 'q.serial';
                    break;
                case 'type_paiement':
                    $column = 'q.type_paiemet';
                    break;
            }

            $select->orderBy($column, $query->getSortOrder() === Query::SORT_ASC ? DatabaseQuery::ORDER_ASC : DatabaseQuery::ORDER_DESC);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(Query $query)
    {
        $select = $this
            ->database
            ->select('quitance', 'q')
        ;

        $this->applyFilters($select, $query);
        $this->processRange($select, $query);

        $select->orderBy('q.id_contrat', DatabaseQuery::ORDER_ASC);
        $select->orderBy('q.serial', DatabaseQuery::ORDER_ASC);

        $total = $select->getCountQuery()->execute()->fetchField();
        $result = $select->execute([], ['class' => $this->getItemClass()]);

        return $this->createResult($result, $total);
    }
}
