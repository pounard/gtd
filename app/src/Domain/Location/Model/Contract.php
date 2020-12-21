<?php

declare(strict_types=1);

namespace Gtd\Domain\Location\Model;

final class Contract
{
    private $id;
    private $id_logement;
    private $id_locataire;
    private $date_start;
    private $date_stop;
    private $loyer;
    private $provision_charges;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() : int
    {
        return (int)$this->id;
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
     * Get date_start
     *
     * @return \DateTimeInterface
     */
    public function getDateStart() : \DateTimeInterface
    {
        if ($this->date_start) {
            return new \DateTimeImmutable('@'.$this->date_start);
        }
    }

    /**
     * Get date_stop
     *
     * @return null|\DateTimeInterface
     */
    public function getDateStop()
    {
        if ($this->date_stop) {
            return new \DateTimeImmutable('@'.$this->date_stop);
        }
    }

    /**
     * Get loyer
     *
     * @return string
     */
    public function getLoyer() : string
    {
        return (string)$this->loyer;
    }

    /**
     * Get provision_charges
     *
     * @return string
     */
    public function getProvisionCharges() : string
    {
        return (string)$this->provision_charges;
    }
}
