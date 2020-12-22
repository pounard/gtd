CREATE TYPE "type_paiement" AS ENUM ('virement', 'cheque', 'espece', 'autre');
CREATE TYPE "civilite" AS ENUM ('monsieur', 'madame', 'mademoiselle');
CREATE TYPE "periode" AS ENUM ('mensuel');

CREATE TABLE courrier (
    "id" uuid PRIMARY KEY,
    "date" timestamp with time zone NOT NULL DEFAULT current_timestamp,
    "titre" varchar(500) DEFAULT 'Lettre sans nom',
    "text" text NOT NULL
);

CREATE TABLE personne (
    "id" uuid PRIMARY KEY,
    "nom" varchar(255) NOT NULL,
    "prenom" varchar(255) NOT NULL,
    "civilite" civilite DEFAULT NULL,
    "date_naissance" date NOT NULL,
    "ville_naissance" varchar(255) NOT NULL,
    "telephone" varchar(255) DEFAULT NULL,
    "mail" varchar(1024) DEFAULT NULL,
    "addr_complement" varchar(255),
    "addr_line1" varchar(255) NOT NULL,
    "addr_line2" varchar(255) DEFAULT NULL,
    "addr_city" varchar(255)  NOT NULL,
    "addr_postcode" varchar(255) NOT NULL
);

CREATE TABLE logement (
    "id" uuid PRIMARY KEY,
    "id_mandataire" uuid NOT NULL,
    "id_proprietaire" uuid DEFAULT NULL,
    "descriptif" varchar(255) NOT NULL,
    "addr_complement" varchar(255),
    "addr_line1" varchar(255) NOT NULL,
    "addr_line2" varchar(255) DEFAULT NULL,
    "addr_city" varchar(255)  NOT NULL,
    "addr_postcode" varchar(255) NOT NULL,
    FOREIGN KEY ("id_mandataire") REFERENCES "personne" ("id")
        ON DELETE NO ACTION
        DEFERRABLE INITIALLY DEFERRED,
    FOREIGN KEY ("id_proprietaire") REFERENCES "personne" ("id")
        ON DELETE NO ACTION
        DEFERRABLE INITIALLY DEFERRED
);

CREATE TABLE "contrat" (
    "id" uuid PRIMARY KEY,
    "id_logement" uuid NOT NULL,
    "id_locataire" uuid NOT NULL,
    "date_start" date NOT NULL,
    "date_stop" date DEFAULT NULL,
    "loyer" decimal(10, 2) NOT NULL,
    "provision_charges" decimal(10, 2) NOT NULL DEFAULT 0,
    FOREIGN KEY ("id_logement") REFERENCES "logement" ("id")
        ON DELETE NO ACTION
        DEFERRABLE INITIALLY DEFERRED,
    FOREIGN KEY ("id_locataire") REFERENCES "personne" ("id")
        ON DELETE NO ACTION
        DEFERRABLE INITIALLY DEFERRED
);

CREATE TABLE "paiement" (
    "id" uuid PRIMARY KEY,
    "id_personne" uuid NOT NULL,
    "date" date NOT NULL,
    "montant" decimal(10, 2),
    "type_paiement" type_paiement DEFAULT NULL,
    FOREIGN KEY ("id_personne") REFERENCES "personne" ("id")
        ON DELETE NO ACTION
        DEFERRABLE INITIALLY DEFERRED
);

CREATE TABLE "quittance" (
    "id" uuid PRIMARY KEY,
    "id_contrat" uuid NOT NULL,
    "id_paiement" uuid DEFAULT NULL,
    "year" smallint NOT NULL,
    "month" smallint NOT NULL,
    "date_start" date NOT NULL,
    "date_stop" date NOT NULL,
    "loyer" decimal(10, 2) NOT NULL,
    "provision_charges" decimal(10, 2) NOT NULL DEFAULT 0,
    "acquitte" boolean DEFAULT false,
    "gracieux" boolean DEFAULT false,
    "date_acquittement" date DEFAULT NULL,
    UNIQUE ("id_contrat", "year", "month"),
    FOREIGN KEY ("id_contrat") REFERENCES "contrat" ("id")
        ON DELETE NO ACTION
        DEFERRABLE INITIALLY DEFERRED,
    FOREIGN KEY ("id_paiement") REFERENCES "paiement" ("id")
        ON DELETE NO ACTION
        DEFERRABLE INITIALLY DEFERRED
);
