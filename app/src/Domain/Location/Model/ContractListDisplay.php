<?php

declare(strict_types=1);

namespace Gtd\Domain\Location\Model;

final class ContractListDisplay
{
    use AddressAwareTrait;

    private $id_contrat;
    private $id_logement;
    private $id_locataire;
    private $date_start;
    private $date_stop;
    private $locataire_nom;
    private $locataire_prenom;
    private $logement_description;

    /**
     * Get id
     *
     * @return int
     */
    public function getContractId() : int
    {
        return (int)$this->id_contrat;
    }

    /**
     * Get id_logement
     *
     * @return int
     */
    public function getLogementId() : int
    {
        return (int)$this->id_logement;
    }

    /**
     * Get id_locataire
     *
     * @return int
     */
    public function getLocataireId() : int
    {
        return (int)$this->id_locataire;
    }

    /**
     * Get locataire_nom
     *
     * @return string
     */
    public function getLocataireNom() : string
    {
        return (string)$this->locataire_nom;
    }

    /**
     * Get locataire_prenom
     *
     * @return string
     */
    public function getLocatairePrenom() : string
    {
        return (string)$this->locataire_prenom;
    }

    /**
     * Get date_start
     *
     * @return \DateTimeInterface
     */
    public function getDateStart() : \DateTimeInterface
    {
        return $this->date_start;
    }

    /**
     * Get date_stop
     *
     * @return null|\DateTimeInterface
     */
    public function getDateStop()
    {
        if ($this->date_stop) {
            return $this->date_stop;
        }
    }
}
