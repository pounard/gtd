import { App } from "foodget/app";
import { ActionBar } from "foodget/container";
import { CellSizing, Container, Signal } from "foodget/core";
import { DataQuery, TableDataProvider } from "foodget/data";
import { Label } from "foodget/display";
import { Button } from "foodget/form";
import { TableView } from "foodget/table";

import { Contrat, ContratDataProvider } from "data/location";
import { createQuittanceTable } from "pages/quittance";
import { formatCurrency, formatDate } from "utils";

class ContratTableDataProvider extends ContratDataProvider implements TableDataProvider<Contrat> {
    private app: App;

    constructor(app: App) {
        super();
        this.app = app;
    }

    /**
     * @inheritdoc
     */
    createRow(row: Container, item: Contrat) {
        row.addChild(new Label(item.logementId));
        row.addChild(new Label(item.locataireId));
        row.addChild(new Label(formatDate(item.dateStart)));
        row.addChild(new Label(formatDate(item.dateStop)));
        row.addChild(new Label(formatCurrency(item.loyer)));
        row.addChild(new Label(formatCurrency(item.provisionCharges)));

        const viewQuittantesButton = new Button("Quittances");
        viewQuittantesButton.connect(Signal.Clicked, () => {
            createQuittanceTable(this.app, item);
        });
        row.addChild(viewQuittantesButton, CellSizing.Shrink);
    }
}

export function createContratTable(app: App, title?: string, query?: DataQuery<Contrat>): void {
    const window = app.stack("Contrats");
    const table = new TableView<Contrat>(new ContratTableDataProvider(app), query);

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
