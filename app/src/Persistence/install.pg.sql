CREATE TYPE "type_paiement" AS ENUM ('virement', 'cheque', 'espece', 'autre');
CREATE TYPE "civilite" AS ENUM ('monsieur', 'madame', 'mademoiselle');
CREATE TYPE "periode" AS ENUM ('mensuel');

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
    "loyer" bigint NOT NULL,
    "provision_charges" bigint NOT NULL DEFAULT 0,
    FOREIGN KEY ("id_logement") REFERENCES "logement" ("id")
        ON DELETE NO ACTION
        DEFERRABLE INITIALLY DEFERRED,
    FOREIGN KEY ("id_locataire") REFERENCES "personne" ("id")
        ON DELETE NO ACTION
        DEFERRABLE INITIALLY DEFERRED
);

CREATE TABLE "quitance" (
    "id" uuid PRIMARY KEY,
    "id_contrat" uuid NOT NULL,
    "serial" bigint NOT NULL,
    "date_start" date NOT NULL,
    "date_stop" date NOT NULL,
    "date_paiement" date DEFAULT NULL,
    "type_paiement" type_paiement DEFAULT NULL,
    "periode" periode NOT NULL DEFAULT 'mensuel',
    "loyer" bigint NOT NULL,
    "provision_charges" bigint NOT NULL DEFAULT 0,
    CHECK ("serial" > 0),
    UNIQUE ("id_contrat", "serial"),
    FOREIGN KEY ("id_contrat") REFERENCES "contrat" ("id")
        ON DELETE NO ACTION
        DEFERRABLE INITIALLY DEFERRED
);
