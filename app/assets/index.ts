import "./dist/foodget/skin/default.css";

import { App } from "foodget/app";
import { ActionBar } from "foodget/container";
import { Button } from "foodget/form";
import { Signal } from "foodget/core";

import {
    createContratTable,
    createLogementTable,
    createPaiementTable,
} from "pages/location";

import { createPersonneTable } from "pages/personne";

const element = document.querySelector("#app") as HTMLElement|null;
if (element) {
    const app = new App();
    const mainWindow = app.stack("Getting Things Done");

    // const sidebar = mainWindow.createSidebar();
    // sidebar.addChild(new Label("This is a SideBar!"));

    const actionBar = new ActionBar();
    mainWindow.addChild(actionBar);

    const openPersonnes = new Button("Personnes");
    openPersonnes.connect(Signal.Clicked, () => createPersonneTable(app));
    actionBar.addChild(openPersonnes);

    const openLogements = new Button("Logements");
    openLogements.connect(Signal.Clicked, () => createLogementTable(app));
    actionBar.addChild(openLogements);

    const openContrats = new Button("Contrats");
    openContrats.connect(Signal.Clicked, () => createContratTable(app));
    actionBar.addChild(openContrats);

    const openPaiements = new Button("Paiements");
    openPaiements.connect(Signal.Clicked, () => createPaiementTable(app));
    actionBar.addChild(openPaiements);

    app.start(element);
}
