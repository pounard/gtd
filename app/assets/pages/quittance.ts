import { App } from "foodget/app";
import { ActionBar, HorizontalPane, StatusBar } from "foodget/container";
import { CellAlignment, CellSizing, Container, Signal } from "foodget/core";
import { DataQuery, TableDataProvider } from "foodget/data";
import { Label } from "foodget/display";
import { Button, CheckBox } from "foodget/form";
import { ListBox } from "foodget/list";
import { TableView } from "foodget/table";

import { Contrat, PaiementDataProvider, Quittance, QuittanceDataProvider } from "data/location";
import { CommandType, formatCurrency, formatDate, sendCommand, Uuid } from "utils";

function generateQuittanceFormWindow(app: App, contrat: Contrat): void {
    const window = app.stack("Génération des quittances");

    const actionBar = new ActionBar();
    window.addChild(actionBar);
    const closeButton = new Button("Close");
    closeButton.connect(Signal.Clicked, () => app.disposeCurrent())
    actionBar.addChild(closeButton)

    const checked = new Set<Uuid>();

    const listBox = new ListBox();
    window.addChild(listBox);
    listBox.setInitializer(container => {
        (new QuittanceDataProvider()).query({ query: { contrat: contrat.id.toString(), acquitte: "true" }}).then(response => {
            response.items.forEach(item => {
                container.addRow(row => {
                    let loyerEtCharges = item.loyer + item.provisionCharges;
                    row.addChild(new Label(formatDate(item.dateStart) + " au " + formatDate(item.dateStop)), CellSizing.Expand);
                    row.addChild(new Label(formatCurrency(item.loyer)));
                    row.addChild(new Label(formatCurrency(item.provisionCharges)));
                    row.addChild(new Label(formatCurrency(loyerEtCharges)));
                    const checkbox = new CheckBox();
                    row.addChild(checkbox, CellSizing.Shrink, CellAlignment.Right);
                    checkbox.connect(Signal.Clicked, input => {
                        if (input.isChecked()) {
                            checked.add(item.id);
                            console.log("Add " + item.id.toString());
                        } else {
                            checked.delete(item.id);
                            console.log("Delete " + item.id.toString());
                        }
                    })
                });
            });
        });
    });

    const generateButton = new Button("Générer la sélection");
    generateButton.connect(Signal.Clicked, () => {
        const message = { quittanceIdList: [] };
        for (let quittanceId of checked) {
            console.log(quittanceId);
            message.quittanceIdList.push(quittanceId as never);
        }
        sendCommand(CommandType.QuittanceAcquitteCourrier, message);
    })
    actionBar.addChild(generateButton)

    app.display(window);
}

function controlQuittanceLeftBox(contrat: Contrat, parent: Container): void {
    const listBox = new ListBox();
    parent.addChild(listBox);

    const statusBar = new StatusBar();
    const status = new Label();
    statusBar.addChild(status);
    parent.addChild(statusBar);

    (new PaiementDataProvider()).query({ query: { personne: contrat.locataireId.toString() }}).then(response => {
        let total = 0;
        response.items.forEach(item => {
            listBox.addRow(row => {
                row.addChild(new Label(formatDate(item.date)), CellSizing.Expand);
                row.addChild(new Label(formatCurrency(item.montant)), CellSizing.Shrink, CellAlignment.Right);
                total += item.montant;
            });
        });
        status.setLabel("Total : " + formatCurrency(total));
        listBox.repaint();
    });
}

function controlQuittanceRightBox(contrat: Contrat, parent: Container) {
    const listBox = new ListBox();
    parent.addChild(listBox);

    const statusBar = new StatusBar();
    const status = new Label();
    statusBar.addChild(status);
    parent.addChild(statusBar);

    listBox.setInitializer(container => {
        (new QuittanceDataProvider()).query({ query: { contrat: contrat.id.toString() }}).then(response => {
            let total = 0, restantDu = 0, acquitte = 0;
            response.items.forEach(item => {
                container.addRow(row => {
                    let loyerEtCharges = item.loyer + item.provisionCharges;
                    row.addChild(new Label(formatDate(item.dateStart) + " au " + formatDate(item.dateStop)), CellSizing.Expand);
                    row.addChild(new Label(formatCurrency(item.loyer)));
                    row.addChild(new Label(formatCurrency(item.provisionCharges)));
                    row.addChild(new Label(formatCurrency(loyerEtCharges)));
                    if (item.acquitte) {
                        row.addChild(new Label("Acquittée"), CellSizing.Shrink, CellAlignment.Right);
                        if (item.gracieux) {
                            row.addChild(new Label("À titre gracieux"), CellSizing.Shrink, CellAlignment.Right);
                        } else {
                            row.addChild(new Label(""));
                            acquitte += loyerEtCharges;
                            total += loyerEtCharges;
                        }
                    } else {
                        const aButton = new Button("Acquitter");
                        row.addChild(aButton, CellSizing.Shrink, CellAlignment.Right);
                        aButton.connect(Signal.Clicked, () => {
                            sendCommand(CommandType.QuittanceAcquitte, {
                                quittanceId: item.id,
                                gracieux: false
                            }).then(() => container.refresh());
                        })

                        const gButton = new Button("Titre gracieux");
                        row.addChild(gButton, CellSizing.Shrink, CellAlignment.Right);
                        gButton.connect(Signal.Clicked, () => {
                            sendCommand(CommandType.QuittanceAcquitte, {
                                quittanceId: item.id,
                                gracieux: false
                            }).then(() => container.refresh());
                        })

                        restantDu += loyerEtCharges;
                        total += loyerEtCharges;
                    }
                });
            });
            status.setLabel([
                "Total : " + formatCurrency(total),
                "Acquitté : " + formatCurrency(acquitte),
                "Restant dû : " + formatCurrency(restantDu),
            ].join(', '));
        });
    });
}

function controlQuittanceWindow(app: App, contrat: Contrat): void {
    const window = app.stack("Contrôle des quittances");

    const actionBar = new ActionBar();
    window.addChild(actionBar);
    const closeButton = new Button("Close");
    closeButton.connect(Signal.Clicked, () => app.disposeCurrent())
    actionBar.addChild(closeButton)

    const hPane = new HorizontalPane();
    window.addChild(hPane);

    controlQuittanceLeftBox(contrat, hPane.stack());
    controlQuittanceRightBox(contrat, hPane.stack());

    app.display(window);
}

export function createQuittanceTable(app: App, contrat?: Contrat): void {
    const window = app.stack("Quittances");

    let query: DataQuery<Quittance> | undefined;
    if (contrat) {
        query = { query: { contrat: contrat.id.toString() }};
    }

    const provider = new class extends QuittanceDataProvider implements TableDataProvider<Quittance> {
        /** @inheritdoc */
        createRow(row: Container, item: Quittance) {
            row.addChild(new Label(item.contratId));
            row.addChild(new Label(formatDate(item.dateStart)));
            row.addChild(new Label(formatDate(item.dateStop)));
            row.addChild(new Label(formatCurrency(item.loyer)), CellSizing.Shrink, CellAlignment.Right);
            row.addChild(new Label(formatCurrency(item.provisionCharges)), CellSizing.Shrink, CellAlignment.Right);
            if (item.acquitte) {
                if (item.gracieux) {
                    row.addChild(new Label("À titre gracieux"), CellSizing.Shrink, CellAlignment.Right);
                } else {
                    row.addChild(new Label("Acquittée"), CellSizing.Shrink, CellAlignment.Right);
                }
            } else {
                row.addChild(new Label("Non"), CellSizing.Shrink, CellAlignment.Right);
            }
            row.addChild(new Label(''), CellSizing.Shrink);
        }
    };

    const table = new TableView<Quittance>(provider, query);
    const actionBar = new ActionBar();
    const actionBarLabel = new Label(`En cours de chargement...`);
    actionBar.addChild(actionBarLabel);

    if (contrat) {
        const controleButton = new Button("Contrôle");
        controleButton.connect(Signal.Clicked, () => controlQuittanceWindow(app, contrat))
        actionBar.addChild(controleButton);

        const generateButton = new Button("Générer");
        generateButton.connect(Signal.Clicked, () => generateQuittanceFormWindow(app, contrat))
        actionBar.addChild(generateButton);
    }

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
