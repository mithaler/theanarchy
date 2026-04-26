import type { AnarchyBga } from "./context";
import type { Game } from "./Game";
import { ctx } from "./context";

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
  availableBoxes: {
    [key: number]: {
      // playerId
      [key: string]: number[]; // section -> boxId
    };
  };
}

export class CheckBoxes extends State<CheckBoxesArgs> {
  onEnteringState(args: CheckBoxesArgs, isCurrentPlayerActive: boolean) {
    ctx.update((c) => {
      Object.entries(c.data.players).forEach(([pid, p]) => {
        p.availableBoxes = args.availableBoxes[pid];
      });
      return c;
    });
    if (isCurrentPlayerActive) {
      this.bga.statusBar.addActionButton(
        _("Pass"),
        () => this.bga.actions.performAction("actPass"),
        { color: "secondary" },
      );
    }
  }

  /**
   * This method is called each time we are leaving the game state. You can use this method to perform some user interface changes at this moment.
   */
  onLeavingState(args: CheckBoxesArgs, isCurrentPlayerActive: boolean) {}

  /**
   * This method is called each time the current player becomes active or inactive in a MULTIPLE_ACTIVE_PLAYER state. You can use this method to perform some user interface changes at this moment.
   * on MULTIPLE_ACTIVE_PLAYER states, you may want to call this function in onEnteringState using `this.onPlayerActivationChange(args, isCurrentPlayerActive)` at the end of onEnteringState.
   * If your state is not a MULTIPLE_ACTIVE_PLAYER one, you can delete this function.
   */
  onPlayerActivationChange(
    args: CheckBoxesArgs,
    isCurrentPlayerActive: boolean,
  ) {}
}
