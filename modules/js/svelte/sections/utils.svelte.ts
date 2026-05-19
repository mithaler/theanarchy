import { getBga } from "../../context.svelte";

export interface SectionProps {
  isMe: boolean;
  checkedBoxes: number[];
  availableBoxes: number[] | null;
}

export function basicChoice(
  question: string,
  choices: string[],
): (doCheck: (choice: string) => Promise<void>) => void {
  return (doCheck: (choice: string) => Promise<void>) => {
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
    bga.statusBar.addActionButton(
      _("Cancel"),
      () => bga.states.restoreServerGameState(),
      { color: "secondary" },
    );
  };
}
