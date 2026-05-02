<script lang="ts">
  import type { AnarchyBga } from "../context.svelte";
  import { CheckBoxes } from "../states.svelte";

  type State = "checked" | "available" | "unavailable";
  interface Props {
    bga: AnarchyBga;
    section: string;
    boxId: number;
    state: State;
  }
  const { bga, section, boxId, state }: Props = $props();

  const onClick = $derived.by(() => {
    if (state === "available") {
      const gameState = bga.states.getCurrentMainStateClass();
      if (gameState instanceof CheckBoxes) {
        return (evt: Event) => {
          evt.preventDefault();
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
