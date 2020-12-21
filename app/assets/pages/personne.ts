import { App } from "foodget/app";
import { ActionBar } from "foodget/container";
import { CellSizing, Container, Signal } from "foodget/core";
import { TableDataProvider } from "foodget/data";
import { Label } from "foodget/display";
import { Button } from "foodget/form";
import { TableView } from "foodget/table";

import { createPaiementTable } from "pages/location";

import {
    Personne,
    PersonneDataProvider,
} from "data/location";

class PersonneTableDataProvider extends PersonneDataProvider implements TableDataProvider<Personne> {
    private app: App;

    constructor(app: App) {
        super();
        this.app = app;
    }

    /**
     * @inheritdoc
     */
    createRow(row: Container, item: Personne) {
        row.addChild(new Label(item.civilite));
        row.addChild(new Label(item.nom));
        row.addChild(new Label(item.prenom));
        row.addChild(new Label(item.telephone));
        row.addChild(new Label(item.emailAddress));

        const viewPaiementButton = new Button("Paiements");
        viewPaiementButton.connect(Signal.Clicked, () => {
            createPaiementTable(
                this.app,
                "Paiements de " + item.nom + " " + item.prenom,
                {
                    query: {
                        "personne": item.id.toString()
                    }
                }
            );
        });
        row.addChild(viewPaiementButton, CellSizing.Shrink);
    }
}

export function createPersonneTable(app: App): void {
    const window = app.stack("Personnes");
    const table = new TableView<Personne>(new PersonneTableDataProvider(app));

    const actionBar = new ActionBar();
    const actionBarLabel = new Label(`En cours de chargement...`);
    actionBar.addChild(actionBarLabel);

    const closeButton = new Button("Close");
    closeButton.connect(Signal.Clicked, () => app.disposeCurrent())
    actionBar.addChild(closeButton)

    window.addChild(actionBar);
    window.addChild(table);
    app.addChild(window);

    // Always trigger the initial load manually?
    table.connect(Signal.TableDataRefreshed, (table) => {
        const response = table.getCurrentResponse();
        const currentPage = response.page ?? 1;
        const totalPageCount = Math.ceil((response.total ?? 1) / (response.limit ?? response.count));
        actionBarLabel.setLabel(`Affichage de ${response.count} / ${response.total ?? response.count} éléments, page ${currentPage} / ${totalPageCount}`);
    });
    table.refresh();
    app.display(window);
}
