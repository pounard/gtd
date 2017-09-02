<?php

namespace GestionBundle\Controller;

use GestionBundle\Mapper\ContratMapper;
use GestionBundle\Mapper\LogementMapper;
use GestionBundle\Mapper\PersonneMapper;
use GestionBundle\Mapper\QuittanceMapper;
use Goat\AccountBundle\Controller\AccountMapperAwareController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class AbstractGestionController extends Controller
{
    use AccountMapperAwareController;

    /**
     * Get quittance mapper
     */
    final protected function getQuittanceMapper() : QuittanceMapper
    {
        return $this->getMapper('Gestion:Quittance');
    }

    /**
     * Get quittance mapper
     */
    final protected function getContratMapper() : ContratMapper
    {
        return $this->getMapper('Gestion:Contrat');
    }

    /**
     * Get quittance mapper
     */
    final protected function getLogementMapper() : LogementMapper
    {
        return $this->getMapper('Gestion:Logement');
    }

    /**
     * Get quittsance mapper
     */
    final protected function getPersonneMapper() : PersonneMapper
    {
        return $this->getMapper('Gestion:Personne');
    }
}
