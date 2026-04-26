/**
 *------
 * BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
 * theanarchy implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

import { mount } from "svelte";
import Table from "./svelte/Table.svelte";
import PlayerPanel from "./svelte/PlayerPanel.svelte";
import type { AnarchyData, AnarchyBga } from "./context";
import { ctx } from "./context";
import { CheckBoxes } from "./states";

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
    ctx.set({ data: gamedatas, bga: this.bga });
    this.bga.gameArea
      .getElement()
      .insertAdjacentHTML("beforeend", `<div id="svelte-app"></div>`);
    mount(Table, { target: document.getElementById("svelte-app")! });

    // Set up player boards
    Object.values(gamedatas.players).forEach((player) => {
      const divId = `player-panel-${player.id}`;
      this.bga.playerPanels
        .getElement(parseInt(player.id, 10))
        .insertAdjacentHTML("beforeend", `<div id="${divId}"></div>`);

      mount(PlayerPanel, {
        target: document.getElementById(divId),
        props: { playerId: player.id },
      });
    });

    // TODO: Set up your game interface here, according to "gamedatas"

    // Setup game notifications to handle (see "setupNotifications" method below)
    this.setupNotifications();

    console.log("Ending game setup");
  }

  ///////////////////////////////////////////////////
  //// Utility methods

  /*

        Here, you can defines some utility methods that you can use everywhere in your javascript
        script. Typically, functions that are used in multiple state classes or outside a state class.

    */

  ///////////////////////////////////////////////////
  //// Reaction to cometD notifications

  /*
        setupNotifications:

        In this method, you associate each of your game notifications with your local method to handle it.

        Note: game notification names correspond to "bga->notify->all" calls in your Game.php file.

    */
  setupNotifications() {
    console.log("notifications subscriptions setup");

    // automatically listen to the notifications, based on the `notif_xxx` function on this class.
    // Uncomment the logger param to see debug information in the console about notifications.
    this.bga.notifications.setupPromiseNotifications({
      logger: console.log,
    });
  }

  // TODO: from this point and below, you can write your game notifications handling methods

  /*
    Example:
    async notif_cardPlayed( args ) {
        // Note: args contains the arguments specified during you "notifyAllPlayers" / "notifyPlayer" PHP call

        // TODO: play the card in the user interface.
    }
    */
}
