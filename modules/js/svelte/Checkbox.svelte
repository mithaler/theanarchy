<script module lang="ts">
  import { ctx } from "../context.svelte";
  export type State = "checked" | "available" | "unavailable" | "unclickable";
  export function getState(
    id: number,
    isMe: boolean,
    checkedBoxes: number[],
    availableBoxes: number[] | null,
  ): State {
    return checkedBoxes.includes(id)
      ? "checked"
      : !ctx.locked && availableBoxes && availableBoxes.includes(id)
        ? "available"
        : isMe
          ? "unavailable"
          : "unclickable";
  }
</script>

<script lang="ts">
  import { getBga } from "../context.svelte";
  import { CheckBoxes } from "../states.svelte";

  interface Props {
    section: string;
    boxId: number;
    state: State;
    type?: string; // TODO remove this and move this class logic out
    click?: (
      doCheck: (choice?: string, writtenValue?: number) => Promise<void>,
    ) => void;

    width?: string;
  }
  const { section, boxId, state, type, click, width }: Props = $props();

  const onclick = $derived.by(() => {
    if (state === "available") {
      const gameState = getBga().states.getCurrentMainStateClass();
      if (gameState instanceof CheckBoxes) {
        if (click) {
          return (evt: Event) => {
            evt.preventDefault();
            click(async (costChoice, writtenValue) => {
              await gameState.checkBox(
                section,
                boxId,
                costChoice,
                writtenValue,
              );
            });
          };
        }
        return (evt: Event) => {
          evt.preventDefault();
          gameState.checkBox(section, boxId);
        };
      }
    }
    return null;
  });

  const style = $derived(width ? `width: ${width}` : undefined);
</script>

<button
  type="button"
  {style}
  class={["checkbox", state, type]}
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

  .unavailable {
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
