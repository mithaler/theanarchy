import {
  ctx,
  type AnarchyBga,
  type Attack,
  type BoxSet,
  type PlayerBoxSet,
  type PlayerKey,
  type Side,
} from "./context.svelte";
import type { Game } from "./Game.svelte";
import { attackChoice, selectedDefenders } from "./svelte/CastleWorkers.svelte";
import { state as pathCardState } from "./svelte/PathCardSelector.svelte";

export abstract class State<ArgType> {
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

  addDoneButton(isCurrentPlayerActive: boolean) {
    if (isCurrentPlayerActive) {
      this.bga.statusBar.addActionButton(
        _("Done"),
        () => this.bga.actions.performAction("actPass"),
        { color: "secondary" },
      );
    }
  }
}

interface ChoosePathCardArgs {
  choices: string[];
}

export class ChoosePathCards extends State<ChoosePathCardArgs> {
  onEnteringState(args: ChoosePathCardArgs): void {
    pathCardState.cards = args.choices;
  }

  onLeavingState(): void {
    pathCardState.cards = undefined;
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
    this.addDoneButton(isCurrentPlayerActive);
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
    /* eslint-disable svelte/prefer-svelte-reactivity */
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

export class Muster extends State<null> {
  onEnteringState(args: null, isCurrentPlayerActive: boolean): void {
    this.addDoneButton(isCurrentPlayerActive);
  }
}

export class UseTactics extends State<null> {
  onEnteringState(args: null, isCurrentPlayerActive: boolean): void {
    this.addDoneButton(isCurrentPlayerActive);
  }
}

interface AllocateDefendersState {
  attack: Attack;
  needsChoice: Side[];
}

export class AllocateDefenders extends State<AllocateDefendersState> {
  doneUpdater?: () => void;

  onEnteringState(
    args: AllocateDefendersState,
    isCurrentPlayerActive: boolean,
  ): void {
    if (isCurrentPlayerActive) {
      if (args.needsChoice.length > 0) {
        attackChoice.needsChoice = args.needsChoice.reduce(
          (glom, choice) => ({
            ...glom,
            [choice]: args.attack.attacks![choice],
          }),
          {},
        ) as Record<Side, number>;

        this.bga.statusBar.addActionButton(
          "Done",
          () => {
            const choices = args.needsChoice.reduce(
              (glom, side) => ({
                ...glom,
                [side]: [...selectedDefenders[side]].map((def) => def.id),
              }),
              {} as Record<Side, number[]>,
            );
            console.log(choices);
            this.bga.actions.performAction("actChooseDefenders", {
              choices: JSON.stringify(choices),
            });
          },
          {
            color: "primary",
            id: "action-bar-done-button",
          },
        );

        // this is needed to update the action button defined above (outside the Svelte app)
        this.doneUpdater = $effect.root(() => {
          $effect(() => {
            const btn: HTMLButtonElement = document.getElementById(
              "action-bar-done-button",
            ) as HTMLButtonElement;

            if (btn) {
              const notDone = Object.entries(attackChoice.needsChoice!).some(
                ([side, needed]) =>
                  selectedDefenders[side as Side].size < needed,
              );

              if (notDone) {
                btn.classList.add("disabled");
                btn.disabled = true;
              } else {
                btn.classList.remove("disabled");
                btn.disabled = false;
              }
            }
          });
        });
      }
    }
  }

  onLeavingState(): void {
    // cancel the reactive Done button
    if (this.doneUpdater) {
      this.doneUpdater();
    }
    attackChoice.needsChoice = undefined;
    selectedDefenders.top.clear();
    selectedDefenders.left.clear();
    selectedDefenders.right.clear();
    selectedDefenders.bottom.clear();
  }
}
