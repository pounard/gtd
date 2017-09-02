<?php

declare(strict_types=1);

namespace GestionBundle\Mapper;

use GestionBundle\Entity\Contrat;
use GestionBundle\Entity\ContratListDisplay;
use Goat\Mapper\WritableSelectMapper;
use Goat\Query\Expression;
use Goat\Query\Query;
use Goat\Query\Where;
use Goat\Runner\PagerResultIterator;
use Goat\Runner\RunnerInterface;

class ContratMapper extends WritableSelectMapper
{
    /**
     * Default contructor
     *
     * @param RunnerInterface $runner
     */
    public function __construct(RunnerInterface $runner)
    {
        parent::__construct($runner, Contrat::class, ['c.id'], $runner->select('contrat', 'c'));
    }

    /**
     * Paginate list display for the given criteria
     *
     * @param array|Expression|Where $criteria
     * @param int $limit
     * @param int $page
     *
     * @return ContratListDisplay[]|PagerResultIterator
     */
    public function paginateListDisplayWhere($criteria, int $limit = 0, int $page = 1) : PagerResultIterator
    {
        $select = $this
            ->getRunner()
            ->select('contrat', 'c')
            ->innerJoin('logement', 'l.id = c.id_logement', 'l')
            ->innerJoin('personne', 'p.id = c.id_locataire', 'p')
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
        ;

        if ($criteria) {
            $select->expression($this->createWhereWith($criteria));
        }

        $select
            ->range($limit, ($page - 1) * $limit)
            ->orderBy('c.id', Query::ORDER_ASC)
        ;

        $total = $select->getCountQuery()->execute()->fetchField();
        $result = $select->execute([], ['class' => ContratListDisplay::class]);

        return new PagerResultIterator($result, $total, $limit, $page);
    }
}
