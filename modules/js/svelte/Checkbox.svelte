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

  const onclick = $derived.by(() => {
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
  class={state}
  type="checkbox"
  checked={state === "checked"}
  disabled={state === "unavailable" || state == "checked"}
  {onclick}
/>

<style lang="scss">
  .available {
    cursor: pointer;
  }
</style>
