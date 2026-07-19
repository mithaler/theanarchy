/* eslint-disable svelte/prefer-svelte-reactivity */
import {
  ctx,
  type AnarchyBga,
  type BoxSet,
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
    if (!ctx.state.availableBoxes) {
      ctx.state.availableBoxes =
        args.availableBoxes[this.bga.players.getCurrentPlayerId()];
    }
    if (!ctx.state.availableWalls) {
      ctx.state.availableWalls =
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
      ctx.state.availableBoxes = undefined;
      ctx.state.availableWalls = undefined;
    }
  }
}

export interface StValentinesFestivalArgs {
  availableBoxes: { female?: number[]; male?: number[] };
}

export class StValentinesFestival extends State<StValentinesFestivalArgs> {
  onEnteringState(args: StValentinesFestivalArgs) {
    if (!ctx.state.availableBoxes) {
      ctx.state.availableBoxes = {
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

interface SingleBoxStateArgs {
  availableBoxes: BoxSet;
}
class SingleBoxState extends State<SingleBoxStateArgs> {
  onEnteringState(args: SingleBoxStateArgs) {
    if (!ctx.state.availableBoxes) {
      ctx.state.availableBoxes = args.availableBoxes;
    }
  }
}

export class Brewhouse extends SingleBoxState {}
export class Lammas extends SingleBoxState {}

export interface MichaelmasArgs {
  // not actually by player; available number -> boxes
  availableBoxesByNum: { [key: number]: number[] };
}

export class Michaelmas extends State<MichaelmasArgs> {
  static setStateCtx(args: MichaelmasArgs) {
    const boxes = Object.entries(args.availableBoxesByNum).reduce(
      (boxes, [_, numBoxes]) => boxes.union(new Set(numBoxes)),
      new Set<number>(),
    );
    ctx.state.availableBoxes = { MICHAELMAS: [...boxes] };
    ctx.state.availableBoxesByNum = args.availableBoxesByNum;
  }

  onEnteringState(args: MichaelmasArgs): void {
    Michaelmas.setStateCtx(args);
  }

  onLeavingState(): void {
    ctx.state.availableBoxesByNum = undefined;
  }
}
