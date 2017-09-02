<?php

namespace GestionBundle\Datasource;

use GestionBundle\Entity\ContractListDisplay;
use Goat\Query\Query as DatabaseQuery;
use Goat\Query\SelectQuery;
use Goat\Query\Where;
use Goat\Runner\RunnerInterface;
use MakinaCorpus\Calista\Datasource\AbstractDatasource;
use MakinaCorpus\Calista\Datasource\Query;

class ContractDatasource extends AbstractDatasource
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
        return ContractListDisplay::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getSorts()
    {
        return [
            'city'        => "Logement city",
            'date_start'  => "Start date",
            'date_stop'   => "End date",
            'id'          => "Identifier",
            'locataire'   => "Locataire name",
            'loyer'       => "Loyer",
            'provisions'  => "Provisions sur charges",
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
        return true;
    }

    protected function applyFilters(SelectQuery $select, Query $query)
    {
        if ($searchString = $query->getSearchString()) {
            $escapedSearchString = $this->database->getEscaper()->escapeLike($searchString);

            $select
                ->getWhere()
                ->open(Where::OR)
                ->isLike('l.descriptif', '%' . $escapedSearchString . '%')
                ->isLike('p.nom', '%'. $escapedSearchString . '%')
            ;
        }
    }

    protected function processRange(SelectQuery $select, Query $query)
    {
        $select->range($query->getLimit(), $query->getOffset());

        if ($query->hasSortField()) {

            switch ($query->getSortField()) {
                case 'city':
                    $column = 'l.addr_line1';
                    break;
                case 'date_start':
                    $column = 'c.date_start';
                    break;
                case 'date_stop':
                    $column = 'c.date_stop';
                    break;
                case 'id':
                    $column = 'c.id';
                    break;
                case 'locataire':
                    $column = 'p.nom';
                    break;
                case 'loyer':
                    $column = 'c.loyer';
                    break;
                case 'provisions':
                    $column = 'c.provision_charges';
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
            ->select('contrat', 'c')
            ->columns([
                'id_contrat'            => 'c.id',
                'id_logement'           => 'c.id_logement',
                'id_locataire'          => 'c.id_locataire',
                'date_start'            => 'c.date_start',
                'date_stop'             => 'c.date_stop',
                'locataire_nom'         => 'p.nom',
                'locataire_prenom'      => 'p.prenom',
                'logement_description'  => 'l.descriptif',
                'addr_complement'       => 'l.addr_complement',
                'addr_line1'            => 'l.addr_line1',
                'addr_line2'            => 'l.addr_line2',
                'addr_city'             => 'l.addr_city',
                'addr_postcode'         => 'l.addr_postcode',
            ])
            ->innerJoin('logement', "l.id = c.id_logement", 'l')
            ->innerJoin('personne', "p.id = c.id_locataire", 'p')
        ;

        $this->applyFilters($select, $query);
        $this->processRange($select, $query);

        $select->orderBy('c.id', DatabaseQuery::ORDER_ASC);

        $total = $select->getCountQuery()->execute()->fetchField();
        $result = $select->execute([], ['class' => $this->getItemClass()]);

        return $this->createResult($result, $total);
    }
}
