<?php

declare(strict_types=1);

namespace GestionBundle\Installer;

use Goat\Bundle\Installer\Updater;
use Goat\Runner\RunnerInterface;
use Goat\Runner\Transaction;

/**
 * Self installer.
 */
class GestionUpdater extends Updater
{
    /**
     * {@inheritdoc}
     */
    public function installSchema(RunnerInterface $runner, Transaction $transaction)
    {
        $runner->query(<<<EOT
CREATE TYPE type_paiement AS ENUM ('virement', 'cheque', 'espece', 'autre');
EOT
        );

        $runner->query(<<<EOT
CREATE TYPE civilite AS ENUM ('monsieur', 'madame', 'mademoiselle');
EOT
        );

        $runner->query(<<<EOT
CREATE TYPE periode AS ENUM ('mensuel');
EOT
        );

        $runner->query(<<<EOT
CREATE TABLE personne (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    prenom VARCHAR(255) NOT NULL,
    civilite civilite DEFAULT NULL,
    date_naissance DATE NOT NULL,
    ville_naissance VARCHAR(255) NOT NULL,
    telephone VARCHAR(255) DEFAULT NULL,
    mail VARCHAR(1024) DEFAULT NULL,
    addr_complement VARCHAR(255),
    addr_line1 VARCHAR(255) NOT NULL,
    addr_line2 VARCHAR(255) DEFAULT NULL,
    addr_city VARCHAR(255)  NOT NULL,
    addr_postcode VARCHAR(255) NOT NULL
);
EOT
        );

        $runner->query(<<<EOT
CREATE TABLE logement (
    id SERIAL PRIMARY KEY,
    id_mandataire INTEGER NOT NULL,
    id_proprietaire INTEGER DEFAULT NULL,
    descriptif VARCHAR(255) NOT NULL,
    addr_complement VARCHAR(255),
    addr_line1 VARCHAR(255) NOT NULL,
    addr_line2 VARCHAR(255) DEFAULT NULL,
    addr_city VARCHAR(255)  NOT NULL,
    addr_postcode VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_mandataire) REFERENCES personne (id),
    FOREIGN KEY (id_proprietaire) REFERENCES personne (id)
);
EOT
        );

        $runner->query(<<<EOT
CREATE TABLE contrat (
    id SERIAL PRIMARY KEY,
    id_logement INTEGER NOT NULL,
    id_locataire INTEGER NOT NULL,
    date_start DATE NOT NULL,
    date_stop DATE DEFAULT NULL,
    loyer INTEGER NOT NULL,
    provision_charges INTEGER NOT NULL DEFAULT 0,
    FOREIGN KEY (id_logement) REFERENCES logement (id),
    FOREIGN KEY (id_locataire) REFERENCES personne (id)
);
EOT
        );

        $runner->query(<<<EOT
CREATE TABLE quitance (
    id SERIAL PRIMARY KEY,
    id_contrat INTEGER NOT NULL,
    serial INTEGER NOT NULL,
    date_start DATE NOT NULL,
    date_stop DATE NOT NULL,
    date_paiement DATE DEFAULT NULL,
    type_paiement type_paiement DEFAULT NULL,
    periode periode NOT NULL DEFAULT 'mensuel',
    loyer INTEGER NOT NULL,
    provision_charges INTEGER NOT NULL DEFAULT 0,
    CHECK (serial > 0),
    UNIQUE (id_contrat, serial),
    FOREIGN KEY (id_contrat) REFERENCES contrat (id)
);
EOT
        );
    }
}
