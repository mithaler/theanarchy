/**
 *------
 * BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
 * theanarchy implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 */

import { mount } from "svelte";
import Table from "./svelte/Table.svelte";
import PlayerPanel from "./svelte/PlayerPanel.svelte";
import type { AnarchyData, AnarchyBga } from "./context.svelte";
import { ctx } from "./context.svelte";
import { CheckBoxes, type CheckBoxesArgs } from "./states.svelte";

export class Game {
  bga: AnarchyBga;

  constructor(bga: AnarchyBga) {
    console.log("theanarchy constructor");
    this.bga = bga;

    // Declare the State classes
    this.bga.states.register("CheckBoxes", new CheckBoxes(this, bga));

    // Uncomment the next line to show debug informations about state changes in the console. Remove before going to production!
    this.bga.states.logger = console.log;
  }

  setup(gamedatas: AnarchyData) {
    console.log("Starting game setup", gamedatas);

    // Initialize the Svelte app
    ctx.data = gamedatas;
    ctx.bga = this.bga;

    this.bga.gameArea
      .getElement()
      .insertAdjacentHTML("beforeend", `<div id="svelte-app"></div>`);
    mount(Table, {
      target: document.getElementById("svelte-app")!,
      props: { ctx },
    });

    // Set up player boards
    Object.values(gamedatas.players).forEach((player) => {
      const divId = `player-panel-${player.id}`;
      this.bga.playerPanels
        .getElement(parseInt(player.id, 10))
        .insertAdjacentHTML("beforeend", `<div id="${divId}"></div>`);

      mount(PlayerPanel, {
        target: document.getElementById(divId)!,
        props: { player },
      });
    });

    this.setupNotifications();
    console.log("Ending game setup");
  }

  setupNotifications() {
    console.log("notifications subscriptions setup");

    // automatically listen to the notifications, based on the `notif_xxx` function on this class.
    // Uncomment the logger param to see debug information in the console about notifications.
    this.bga.notifications.setupPromiseNotifications({
      logger: console.log,
    });
  }

  async notif_updateAvailableBoxes(args: CheckBoxesArgs) {
    CheckBoxes.updateCtx(args);
  }
}
