<?php

declare(strict_types=1);

namespace GestionBundle\Entity;

/**
 * Generated code, please do not modify.
 *
 * bin/console goat:generate-entity --source=src/GestionBundle/Resources/meta/entity/quittance.yml foo foo
 */
final class Quittance
{
    private $id;
    private $id_contrat;
    private $serial;
    private $date_start;
    private $date_stop;
    private $date_paiement;
    private $type_paiement;
    private $periode;
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
     * Get id_contrat
     *
     * @return int
     */
    public function getContractId() : int
    {
        return (int)$this->id_contrat;
    }

    /**
     * Get serial
     *
     * @return int
     */
    public function getSerial() : int
    {
        return (int)$this->serial;
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
     * @return \DateTimeInterface
     */
    public function getDateStop() : \DateTimeInterface
    {
        return $this->date_stop;
    }

    /**
     * Get date_paiement
     *
     * @return null|\DateTimeInterface
     */
    public function getDatePaiement()
    {
        if ($this->date_paiement) {
            return $this->date_paiement;
        }
    }

    /**
     * Get type_paiement
     *
     * @return null|string
     */
    public function getTypePaiement()
    {
        if (isset($this->type_paiement)) {
            return (string)$this->type_paiement;
        }
    }

    /**
     * Get periode
     *
     * @return string
     */
    public function getPeriode() : string
    {
        return (string)$this->periode;
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
