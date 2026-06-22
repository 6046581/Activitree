import { mount } from "ripple";
import { App } from "./App.tsrx";

mount(App, {
   // @ts-ignore
   target: document.getElementById("root"),
});
