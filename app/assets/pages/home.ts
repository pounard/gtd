import { App } from "foodget/app";
import { NoteBook, HorizontalBox } from "foodget/container";
import { Link } from "foodget/display";

import { url } from "utils";

export function createHome(app: App) {
    const mainWindow = app.findChild(0);
    if (!mainWindow) {
        return;
    }

    const noteBook = new NoteBook();
    mainWindow.item.addChild(noteBook);

    /* const page1 = */ noteBook.stack("Accueil");

    const page2 = noteBook.stack("Lettres et courriers");
    const hbox = new HorizontalBox();
    page2.addChild(hbox);

    const box1 = hbox.createBox();
    const letterLink = new Link("Example de lettre", url("example/letter/template"));
    box1.addChild(letterLink);
}
