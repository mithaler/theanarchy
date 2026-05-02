import { ctx, type AnarchyBga, type PlayerBoxSet } from "./context.svelte";
import type { Game } from "./Game.svelte";

abstract class State<ArgType> {
  game: Game;
  bga: AnarchyBga;

  args: ArgType;
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

interface CheckBoxesArgs {
  availableBoxes: PlayerBoxSet;
}

export class CheckBoxes extends State<CheckBoxesArgs> {
  onEnteringState(args: CheckBoxesArgs, isCurrentPlayerActive: boolean) {
    Object.entries(ctx.data.players).forEach(([pid, p]) => {
      p.availableBoxes = args.availableBoxes[pid];
    });

    if (isCurrentPlayerActive) {
      this.bga.statusBar.addActionButton(
        _("Pass"),
        () => this.bga.actions.performAction("actPass"),
        { color: "secondary" },
      );
    }
  }

  async checkBox(section: string, boxId: number) {
    return this.bga.actions.performAction("actCheckBox", { section, boxId });
  }
}
