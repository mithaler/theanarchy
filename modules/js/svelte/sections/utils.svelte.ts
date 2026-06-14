import { getBga, type AnarchyBga } from "../../context.svelte";
import type { ChoiceFunc } from "../Checkbox.svelte";

export interface SectionProps {
  isMe: boolean;
  checkedBoxes: number[];
  availableBoxes: number[] | null;
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

export function ids(length: number): number[] {
  return Array.from({ length }, (_, i) => i + 1);
}
