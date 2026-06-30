import { mount, unmount } from "svelte";
import {
  getAvailableBoxes,
  getBga,
  getCheckedBoxes,
  type AnarchyBga,
} from "../../context.svelte";
import type { ChoiceFunc } from "../Checkbox.svelte";
import ResourceChooser, { type Values } from "../ResourceChooser.svelte";

export interface SectionProps {
  isMe: boolean;
  checkedBoxes: number[];
  availableBoxes: number[] | null;
}

export function sectionProps(
  isMe: boolean,
  playerId: number,
  section: string,
): SectionProps {
  return {
    isMe,
    checkedBoxes: getCheckedBoxes(playerId, section),
    availableBoxes: isMe ? getAvailableBoxes(section) : null,
  };
}

export function addCancelButton(bga: AnarchyBga, addlCleanup?: () => void) {
  bga.statusBar.addActionButton(
    _("Cancel"),
    () => {
      bga.states.restoreServerGameState();
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
    bga.states.setClientState("basicChoice", {
      descriptionmyturn: question,
    });
    choices.forEach((choice) => {
      bga.statusBar.addActionButton(choice, async () => {
        await doCheck(choice);
        bga.states.restoreServerGameState();
      });
    });
    addCancelButton(bga);
  };
}

export async function multiResourceChoice(
  count: number,
  callback: (choices: string[]) => Promise<void>,
  onCancel?: () => void,
) {
  const bga = getBga();
  bga.states.setClientState("multiResourceChoice", {
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
  });

  const statusBar = document.getElementById("maintitlebar_content")!;
  statusBar.insertAdjacentHTML(
    "beforeend",
    '<div id="resource-chooser"></div>',
  );
  const chooser = mount(ResourceChooser, {
    target: document.getElementById("resource-chooser")!,
    props: { values, count, confirm },
  });

  addCancelButton(bga, () => {
    unmount(chooser, { outro: true }).then(() => {
      document.getElementById("resource-chooser")?.remove();
      if (onCancel) onCancel();
    });
  });
}

export function ids(length: number): number[] {
  return Array.from({ length }, (_, i) => i + 1);
}
