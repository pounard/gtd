<?php

declare(strict_types=1);

namespace GestionBundle\Entity;

/**
 * Generated code, please do not modify.
 *
 * bin/console goat:generate-entity --source=src/GestionBundle/Resources/meta/entity/logement.yml foo foo
 */
final class Logement
{
    use AddressAwareTrait;

    private $id;
    private $id_mandataire;
    private $id_proprietaire;
    private $descriptif;

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
     * Get id_mandataire
     *
     * @return int
     */
    public function getMandataireId() : int
    {
        return (int)$this->id_mandataire;
    }

    /**
     * Get id_proprietaire
     *
     * @return null|int
     */
    public function getProprietaireId()
    {
        if (isset($this->id_proprietaire)) {
            return (int)$this->id_proprietaire;
        }
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() : string
    {
        return (string)$this->descriptif;
    }
}
