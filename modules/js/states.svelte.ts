import {
  ctx,
  performAction,
  type AnarchyBga,
  type AnarchyPlayer,
  type PlayerBoxSet,
} from "./context.svelte";
import type { Game } from "./Game.svelte";

abstract class State<ArgType> {
  game: Game;
  bga: AnarchyBga;

  /* eslint-disable @typescript-eslint/no-unused-vars */
  onEnteringState(args: ArgType, isCurrentPlayerActive: boolean): void {}
  onLeavingState(args: ArgType, isCurrentPlayerActive: boolean): void {}
  onPlayerActivationChange(
    args: ArgType,
    isCurrentPlayerActive: boolean,
  ): void {}

  constructor(game: Game, bga: AnarchyBga) {
    this.game = game;
    this.bga = bga;
  }
}

export interface CheckBoxesArgs {
  availableBoxes: PlayerBoxSet;
  availableWalls: { [key: number]: (keyof AnarchyPlayer)[] };
}

export class CheckBoxes extends State<CheckBoxesArgs> {
  onEnteringState(args: CheckBoxesArgs, isCurrentPlayerActive: boolean) {
    if (!ctx.data?.availableBoxes) {
      ctx.data!.availableBoxes =
        args.availableBoxes[this.bga.players.getCurrentPlayerId()];
    }
    if (!ctx.data?.availableWalls) {
      ctx.data!.availableWalls =
        args.availableWalls[this.bga.players.getCurrentPlayerId()];
    }
    if (isCurrentPlayerActive) {
      this.bga.statusBar.addActionButton(
        _("Pass"),
        () => this.bga.actions.performAction("actPass"),
        { color: "secondary" },
      );
    }
  }

  onPlayerActivationChange(
    args: CheckBoxesArgs,
    isCurrentPlayerActive: boolean,
  ): void {
    if (!isCurrentPlayerActive) {
      ctx.data!.availableBoxes = undefined;
    }
  }

  async checkBox(
    section: string,
    boxId: number,
    choice?: string,
    writtenValue?: number,
  ) {
    // zero out the player's available boxes while performing the action so it doesn't stutter
    // the notification coming back will update it with the new options, see notif_newAvailable
    const oldAvailBoxes = ctx.data!.availableBoxes;
    ctx.data!.availableBoxes = undefined;

    try {
      return await performAction("actCheckBox", {
        section,
        boxId,
        choice,
        writtenValue,
      });
    } catch (e) {
      // on error, set them back so we don't leave the UI unusable
      ctx.data!.availableBoxes = oldAvailBoxes;
      console.error("Error checking box", e);
    }
  }
}
