<?php

declare(strict_types=1);

namespace GestionBundle\Entity;

/**
 * Generated code, please do not modify.
 *
 * bin/console goat:generate-entity --source=src/GestionBundle/Resources/meta/entity/personne.yml foo foo
 */
final class Personne
{
    use AddressAwareTrait;

    private $id;
    private $nom;
    private $prenom;
    private $civilite;
    private $date_naissance;
    private $ville_naissance;
    private $telephone;
    private $mail;

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
     * Get nom
     *
     * @return string
     */
    public function getNom() : string
    {
        return (string)$this->nom;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom() : string
    {
        return (string)$this->prenom;
    }

    /**
     * Get civilite
     *
     * @return null|string
     */
    public function getCivilite()
    {
        if (isset($this->civilite)) {
            return (string)$this->civilite;
        }
    }

    /**
     * Get date_naissance
     *
     * @return string
     */
    public function getDateNaissance() : string
    {
        return (string)$this->date_naissance;
    }

    /**
     * Get ville_naissance
     *
     * @return string
     */
    public function getVilleNaissance() : string
    {
        return (string)$this->ville_naissance;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone() : string
    {
        return (string)$this->telephone;
    }

    /**
     * Get mail
     *
     * @return string
     */
    public function getMail() : string
    {
        return (string)$this->mail;
    }
}
