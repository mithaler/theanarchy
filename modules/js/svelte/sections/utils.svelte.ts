import { mount, unmount } from "svelte";
import {
  getAvailableBoxes,
  getBga,
  getCheckedBoxes,
  type AnarchyBga,
  type AnarchyPlayer,
} from "../../context.svelte";
import type { ChoiceFunc } from "../Checkbox.svelte";
import ResourceChooser, { type Values } from "../ResourceChooser.svelte";
import { CheckBoxes } from "../../states.svelte";

export interface SectionProps {
  isMe: boolean;
  checkedBoxes: number[];
  availableBoxes: number[] | null;
}

export function sectionProps(
  isMe: boolean,
  player: AnarchyPlayer,
  section: string,
): SectionProps {
  return {
    isMe,
    checkedBoxes: getCheckedBoxes(player, section),
    availableBoxes: isMe ? getAvailableBoxes(section) : null,
  };
}

/**
 * The BGA's basic setClientState does not correctly leave private states.
 * Whenever I do it, it's always out of CheckBoxes, so do it correctly.
 */
export function setCheckBoxesClientState(name: string, args: object) {
  const bga = getBga();
  const state = bga.states.getCurrentPlayerStateClass();
  if (state instanceof CheckBoxes) {
    state.onLeavingState();
  }
  bga.states.setClientState(name, args);
}

/**
 * The BGA's basic restoreServerGameState does not correctly restore private states.
 * Whenever I do it, it's always into CheckBoxes, so do it correctly.
 */
export function restoreCheckBoxesPrivateState(bga: AnarchyBga) {
  bga.states.restoreServerGameState();
  const state = bga.states.getCurrentPlayerStateClass();
  if (state instanceof CheckBoxes) {
    state.onEnteringState(state.args, true);
  }
}

export function addCancelButton(bga: AnarchyBga, addlCleanup?: () => void) {
  bga.statusBar.addActionButton(
    _("Cancel"),
    () => {
      restoreCheckBoxesPrivateState(bga);
      if (addlCleanup) {
        addlCleanup();
      }
    },
    { color: "secondary" },
  );
}

export function basicChoice(
  question: string,
  choices: string[],
): (doCheck: ChoiceFunc) => void {
  return (doCheck: ChoiceFunc) => {
    const bga = getBga();
    setCheckBoxesClientState("basicChoice", {
      descriptionmyturn: question,
    });
    choices.forEach((choice) => {
      bga.statusBar.addActionButton(choice, async () => {
        await doCheck(choice);
        restoreCheckBoxesPrivateState(bga);
      });
    });
    addCancelButton(bga);
  };
}

// TODO: Refactor this as a component on the page
export async function multiResourceChoice(
  count: number,
  callback: (choices: string[]) => Promise<void>,
) {
  const bga = getBga();
  setCheckBoxesClientState("multiResourceChoice", {
    descriptionMyTurn: _("${you} must choose ${count} resources"),
    args: { count },
  });

  const values: Values = $state({ materials: 0, food: 0, silver: 0 });
  const confirm = $derived(async () => {
    const choices: string[] = [];
    Object.entries(values).forEach(([resource, nbr]) => {
      for (let i = 0; i < nbr; i++) {
        choices.push(resource);
      }
    });
    await callback(choices);
    restoreCheckBoxesPrivateState(bga);
  });

  const statusBar = document.getElementById("maintitlebar_content")!;
  statusBar.insertAdjacentHTML(
    "beforeend",
    '<div id="resource-chooser"></div>',
  );
  const chooser = mount(ResourceChooser, {
    target: document.getElementById("resource-chooser")!,
    // svelte-ignore state_referenced_locally
    props: { values, count, confirm },
  });

  addCancelButton(bga, () => {
    unmount(chooser, { outro: true }).then(() => {
      document.getElementById("resource-chooser")?.remove();
      restoreCheckBoxesPrivateState(bga);
    });
  });
}

export function ids(length: number): number[] {
  return Array.from({ length }, (_, i) => i + 1);
}
