import {
  ctx,
  getCurrentPlayer,
  performAction,
  type AnarchyBga,
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
  checkedBoxes: PlayerBoxSet;
}

export class CheckBoxes extends State<CheckBoxesArgs> {
  onEnteringState(args: CheckBoxesArgs, isCurrentPlayerActive: boolean) {
    Object.entries(ctx.data!.players).forEach(([pid, p]) => {
      p.availableBoxes = args.availableBoxes[parseInt(pid, 10)];
      p.checkedBoxes = args.checkedBoxes[parseInt(pid, 10)] ?? {};
    });
    if (isCurrentPlayerActive) {
      this.bga.statusBar.addActionButton(
        _("Pass"),
        () => this.bga.actions.performAction("actPass"),
        { color: "secondary" },
      );
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
    const player = getCurrentPlayer();
    const oldAvailBoxes = player.availableBoxes;
    player.availableBoxes = undefined;

    try {
      return await performAction("actCheckBox", {
        section,
        boxId,
        choice,
        writtenValue,
      });
    } catch (e) {
      // on error, set them back so we don't leave the UI unusable
      player.availableBoxes = oldAvailBoxes;
      console.error("Error checking box", e);
    }
  }
}
