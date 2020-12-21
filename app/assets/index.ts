import "./dist/foodget/skin/default.css";

import { App } from "foodget/app";
import { ActionBar } from "foodget/container";
import { Button } from "foodget/form";
import { Signal } from "foodget/core";

import { createPersonneTable } from "pages/personnes";

const element = document.querySelector("#app") as HTMLElement|null;
if (element) {
    const app = new App();
    const mainWindow = app.stack("Getting Things Done");

    // const sidebar = mainWindow.createSidebar();
    // sidebar.addChild(new Label("This is a SideBar!"));

    const actionBar = new ActionBar();

    const openPersonnes = new Button("Personnes");
    openPersonnes.connect(Signal.Clicked, () => createPersonneTable(app));
    actionBar.addChild(openPersonnes);
    mainWindow.addChild(actionBar);

    app.start(element);
}
