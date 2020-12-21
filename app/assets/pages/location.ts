import { App } from "foodget/app";
import { ActionBar } from "foodget/container";
import { Signal } from "foodget/core";
import { DataQuery } from "foodget/data";
import { Label } from "foodget/display";
import { Button } from "foodget/form";
import { TableView } from "foodget/table";

import {
    Logement,
    LogementDataProvider,
    Paiement,
    PaiementDataProvider,
} from "data/location";

export function createLogementTable(app: App): void {
    const window = app.stack("Logements");
    const table = new TableView<Logement>(new LogementDataProvider());

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

export function createPaiementTable(app: App, title?: string, query?: DataQuery<Paiement>): void {
    const window = app.stack(title ?? "Paiements");
    const table = new TableView<Paiement>(new PaiementDataProvider(), query);

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
