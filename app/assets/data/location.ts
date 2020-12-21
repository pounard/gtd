import { DataColumnSpec, DataProvider, DataQuery, TableDataProvider } from "foodget/data";
import { Container } from "foodget/core";
import { Label } from "foodget/display";

import { CurrencyAmount, fetchList, Uuid } from "utils";

export enum Civilite {
    Monsieur = "monsieur",
    Madame = "madame",
    Mademoiselle = "mademoiselle",
}

export enum TypePaiment {
    Virement = "virement",
    Cheque = "cheque",
    Espece = "espece",
    Autre = "autre",
}

export interface WithAddress {
    readonly addrComplement?: string;
    readonly addrLine1?: string;
    readonly addrLine2?: string;
    readonly addrCity?: string;
    readonly addrPostcode?: string;
}

export interface Personne extends WithAddress {
    readonly id: Uuid;
    readonly emailAddress: string;
    readonly nom: string;
    readonly prenom: string;
    readonly civilite?: Civilite;
    readonly dateNaissance?: string; // date
    readonly villeNaissance?: string;
    readonly telephone?: string;
}

export interface Logement extends WithAddress {
    readonly id: Uuid;
    readonly descriptif: string;
    readonly mandataireId: Uuid;
    readonly proprietaireId?: Uuid;
}

export interface Contrat {
    readonly id: Uuid;
    readonly logementId: Uuid;
    readonly locataireId: Uuid;
    readonly dateStart: string; // date
    readonly dateStop?: string; // date
    readonly loyer: CurrencyAmount;
    readonly provisionCharges: CurrencyAmount;
}

export interface Paiement {
    readonly id: Uuid;
    readonly personneId: Uuid;
    readonly montant: CurrencyAmount;
    readonly typePaiement: TypePaiment;
    readonly date: string; // date
}

export interface Quittance {
    readonly id: Uuid;
    readonly contratId: Uuid;
    readonly paiementId?: Uuid;
    readonly year: number;
    readonly month: number;
    readonly dateStart: string; // date
    readonly dateStop: string; // date
    readonly loyer: CurrencyAmount;
    readonly provisionCharges: CurrencyAmount;
}

export class PersonneDataProvider implements DataProvider<Personne> {
    /**
     * @inheritdoc
     */
    createRow(row: Container, item: Personne) {
        row.addChild(new Label(item.civilite));
        row.addChild(new Label(item.nom));
        row.addChild(new Label(item.prenom));
        row.addChild(new Label(item.telephone));
        row.addChild(new Label(item.emailAddress));
    }

    /**
     * @inheritdoc
     */
    query(query: DataQuery<Personne>) {
        // You could use fetch here.
        return fetchList<Personne>("location/personne/list", query);
    }

    /**
     * @inheritdoc
     */
    getColumnSpec(): DataColumnSpec<Personne>[] {
        return [
            { field: "civilite", label: "Civilite" },
            { field: "nom", label: "Nom", sortable: true },
            { field: "prenom", label: "Prénom", sortable: true },
            { field: "telephone", label: "Téléphone", sortable: true },
            { field: "emailAddress", label: "Adresse e-mail", sortable: true },
            // @todo Adresse
            { field: "actions", label: "", sortable: false }
        ];
    }
}

export class LogementDataProvider implements TableDataProvider<Logement> {
    /**
     * @inheritdoc
     */
    createRow(row: Container, item: Logement) {
        row.addChild(new Label(item.descriptif));
        row.addChild(new Label(item.mandataireId));
        row.addChild(new Label(item.proprietaireId));
    }

    /**
     * @inheritdoc
     */
    query(query: DataQuery<Logement>) {
        // You could use fetch here.
        return fetchList<Logement>("location/logement/list", query);
    }

    /**
     * @inheritdoc
     */
    getColumnSpec(): DataColumnSpec<Logement>[] {
        return [
            { field: "descriptif", label: "Descriptif", sortable: true },
            { field: "mandataire", label: "Mandataire" },
            { field: "proprietaire", label: "Propriétaire" },
            // @todo Adresse
        ];
    }
}

export class ContratDataProvider implements TableDataProvider<Contrat> {
    /**
     * @inheritdoc
     */
    createRow(row: Container, item: Contrat) {
        row.addChild(new Label(item.logementId));
        row.addChild(new Label(item.locataireId));
        row.addChild(new Label(item.dateStart));
        row.addChild(new Label(item.dateStop));
        row.addChild(new Label(item.loyer?.toString() ?? ''));
        row.addChild(new Label(item.provisionCharges?.toString() ?? ''));
    }

    /**
     * @inheritdoc
     */
    query(query: DataQuery<Contrat>) {
        // You could use fetch here.
        return fetchList<Contrat>("location/contrat/list", query);
    }

    /**
     * @inheritdoc
     */
    getColumnSpec(): DataColumnSpec<Contrat>[] {
        return [
            { field: "logemment", label: "Logement" },
            { field: "locataire", label: "Locataire" },
            { field: "date_start", label: "Date d'arrivée", sortable: true },
            { field: "date_stop", label: "Date de départ", sortable: true },
            { field: "loyer", label: "Loyer", sortable: true },
            { field: "charges", label: "Charges", sortable: true },
        ];
    }
}

export class PaiementDataProvider implements TableDataProvider<Paiement> {
    /**
     * @inheritdoc
     */
    createRow(row: Container, item: Paiement) {
        row.addChild(new Label(item.date));
        row.addChild(new Label(item.montant?.toString() ?? ''));
        row.addChild(new Label(item.typePaiement));
        row.addChild(new Label(item.personneId));
    }

    /**
     * @inheritdoc
     */
    query(query: DataQuery<Paiement>) {
        // You could use fetch here.
        return fetchList<Paiement>("location/paiement/list", query);
    }

    /**
     * @inheritdoc
     */
    getColumnSpec(): DataColumnSpec<Paiement>[] {
        return [
            { field: "date", label: "Date", sortable: true },
            { field: "montant", label: "Montant" },
            { field: "type", label: "Type de paiement", sortable: true },
            { field: "personne", label: "Reçu de" },
        ];
    }
}
