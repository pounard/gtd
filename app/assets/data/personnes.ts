import { DataColumnSpec, DataQuery, TableDataProvider } from "foodget/data";
import { Container } from "foodget/core";
import { Label } from "foodget/display";
import { Civilite, fetchList, Uuid, WithAddress } from "utils";

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

export class PersonneDataProvider implements TableDataProvider<Personne> {
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
        ];
    }
}
