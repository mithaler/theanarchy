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
import type {
  AnarchyData,
  AnarchyBga,
  PlayerCounterArgs,
  BoxRewardArgs,
  BoxSet,
  PlayerKey,
} from "./context.svelte";
import { ctx, getPlayer } from "./context.svelte";
import {
  Brewhouse,
  CheckBoxes,
  StValentinesFestival,
  KnightsTraining,
  type StValentinesFestivalArgs,
  Michaelmas,
  type MichaelmasArgs,
  Lammas,
  ChoosePathCards,
} from "./states.svelte";

export class Game {
  bga: AnarchyBga;

  constructor(bga: AnarchyBga) {
    console.log("theanarchy constructor");
    this.bga = bga;

    this.bga.states.register("ChoosePathCards", new ChoosePathCards(this, bga));
    this.bga.states.register("CheckBoxes", new CheckBoxes(this, bga));
    this.bga.states.register("KnightsTraining", new KnightsTraining(this, bga));
    this.bga.states.register(
      "StValentinesFestival",
      new StValentinesFestival(this, bga),
    );
    this.bga.states.register("Brewhouse", new Brewhouse(this, bga));
    this.bga.states.register("Michaelmas", new Michaelmas(this, bga));
    this.bga.states.register("Lammas", new Lammas(this, bga));

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

    // Set up player panels
    Object.values(gamedatas.players).forEach((player) => {
      const divId = `player-panel-${player.id}`;
      this.bga.playerPanels
        .getElement(parseInt(player.id, 10))
        .insertAdjacentHTML("beforeend", `<div id="${divId}"></div>`);

      mount(PlayerPanel, {
        target: document.getElementById(divId)!,
        props: { player: getPlayer(player.id) },
      });
    });

    this.setupNotifications();
    console.log("Ending game setup");

    // @ts-expect-error: For debugging
    window.game = this;
  }

  setupNotifications() {
    console.log("notifications subscriptions setup");

    // automatically listen to the notifications, based on the `notif_xxx` function on this class.
    // Uncomment the logger param to see debug information in the console about notifications.
    this.bga.notifications.setupPromiseNotifications({
      logger: console.log,
    });
  }

  async notif_boxReward(args: BoxRewardArgs) {
    const player = ctx.data!.players[args.player_id]!;

    const section = player.checkedBoxes[args.boxSection] ?? [];
    section.push(args.boxId);
    ctx.data!.players[args.player_id]!.checkedBoxes[args.boxSection] = section;
  }

  async notif_newAvailable(args: BoxSet) {
    // this notification is for states that use basic availableBoxes only
    // sometimes we get this notification late after a state change; if that happens ignore it
    if (
      ["CheckBoxes", "Brewhouse", "Lammas"].includes(
        ctx.bga!.states.getCurrentPlayerStateName(),
      )
    ) {
      ctx.state.availableBoxes = args;
    }
  }

  async notif_newValentinesAvailable(args: StValentinesFestivalArgs) {
    ctx.state.availableBoxes = {
      "ST VALENTINES FESTIVAL": [
        ...(args.availableBoxes.female ?? []),
        ...(args.availableBoxes.male ?? []),
      ],
    };
  }

  async notif_newAvailableWalls(args: PlayerKey[]) {
    ctx.state.availableWalls = args;
  }

  async notif_setPlayerCounter(args: PlayerCounterArgs) {
    (getPlayer(args.playerId)[args.name] as number) = args.value;
  }

  async notif_michaelmasUpdate(args: MichaelmasArgs) {
    Michaelmas.setStateCtx(args);
  }
}
