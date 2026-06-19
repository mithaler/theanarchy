import {
  ctx,
  type AnarchyBga,
  type PlayerBoxSet,
  type PlayerKey,
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
  availableWalls: { [key: number]: PlayerKey[] };
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
      ctx.data!.availableWalls = undefined;
    }
  }
}

export interface StValentinesFestivalArgs {
  availableBoxes: { female?: number[]; male?: number[] };
}

export class StValentinesFestival extends State<StValentinesFestivalArgs> {
  onEnteringState(args: StValentinesFestivalArgs) {
    if (!ctx.data?.availableBoxes) {
      ctx.data!.availableBoxes = {
        "ST VALENTINES FESTIVAL": [
          ...(args.availableBoxes.female ?? []),
          ...(args.availableBoxes.male ?? []),
        ],
      };
    }
  }
}

export class KnightsTraining extends State<never> {
  onEnteringState(): void {
    // TODO make these look nice
    ["soldiers", "KNIGHTS"].forEach((choice) => {
      this.bga.statusBar.addActionButton(choice, () =>
        this.bga.actions.performAction("actMakeChoice", { choice }),
      );
    });
  }
}
