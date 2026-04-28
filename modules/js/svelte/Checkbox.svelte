<script lang="ts">
  import { ctx } from "../context";
  import { CheckBoxes } from "../states";

  type State = "checked" | "available" | "unavailable";
  interface Props {
    section: string;
    boxId: number;
    state: State;
  }
  const { section, boxId, state }: Props = $props();

  const onClick = $derived.by(() => {
    if (state === "available") {
      const gameState = $ctx.bga.states.getCurrentMainStateClass();
      if (gameState instanceof CheckBoxes) {
        return () => {
          gameState.checkBox(section, boxId);
        };
      }
    }
    return null;
  });
</script>

<input
  id="box-{section}-{boxId}"
  type="checkbox"
  checked={state === "checked"}
  disabled={state === "unavailable"}
  onclick={onClick}
/>
