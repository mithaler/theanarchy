<script module lang="ts">
  export type State = "checked" | "available" | "unavailable" | "unclickable";
  export function getState(
    id: number,
    checkedBoxes: number[],
    availableBoxes: number[] | null,
  ): State {
    return checkedBoxes.includes(id)
      ? "checked"
      : availableBoxes && availableBoxes.includes(id)
        ? "available"
        : "unavailable";
  }
</script>

<script lang="ts">
  import type { AnarchyBga } from "../context.svelte";
  import { CheckBoxes } from "../states.svelte";

  interface Props {
    bga: AnarchyBga;
    section: string;
    boxId: number;
    state: State;
    type: string;

    width?: string;
  }
  const { bga, section, boxId, state, type, width }: Props = $props();

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

  const classes = $derived([
    {
      checkbox: true,
      available: state === "available",
      checked: state === "checked",
      disabled: state === "unavailable",
    },
    type,
  ]);

  const style = $derived(width ? `width: ${width}` : undefined);
</script>

<button
  type="button"
  {style}
  class={classes}
  title={`${section} ${boxId}`}
  {onclick}
>
  {#if state === "checked"}
    <div class="check"></div>
  {/if}
</button>

<style lang="scss">
  .checkbox {
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    width: 19px;
    height: 18px;
    margin-right: 2px;
  }

  .production {
    margin-right: 3px;
  }

  .resource {
    margin-right: 3px;
  }

  .available {
    cursor: pointer;
    box-shadow: 0px 0px 6px 5px rgba(255, 46, 46, 0.9);
  }

  .disabled {
    background-color: lightgray;
    opacity: 0.6;
  }

  .check {
    &::after {
      content: "✕";
      font-size: 30px;
      font-weight: bold;
    }
  }
</style>
