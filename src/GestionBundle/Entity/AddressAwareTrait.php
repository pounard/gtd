<?php

declare(strict_types=1);

namespace GestionBundle\Entity;

trait AddressAwareTrait
{
    private $addr_complement;
    private $addr_line1;
    private $addr_line2;
    private $addr_city;
    private $addr_postcode;

    /**
     * Get addr_complement
     *
     * @return null|string
     */
    public function getAddrComplement()
    {
        if (isset($this->addr_complement)) {
            return (string)$this->addr_complement;
        }
    }

    /**
     * Get addr_line1
     *
     * @return string
     */
    public function getAddrLine1() : string
    {
        return (string)$this->addr_line1;
    }

    /**
     * Get addr_line2
     *
     * @return null|string
     */
    public function getAddrLine2()
    {
        if (isset($this->addr_line2)) {
            return (string)$this->addr_line2;
        }
    }

    /**
     * Get addr_city
     *
     * @return string
     */
    public function getAddrCity() : string
    {
        return (string)$this->addr_city;
    }

    /**
     * Get addr_postcode
     *
     * @return string
     */
    public function getAddrPostcode() : string
    {
        return (string)$this->addr_postcode;
    }
}
