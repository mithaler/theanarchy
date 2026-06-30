<script module lang="ts">
  import { checkBox, ctx } from "../context.svelte";
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

  /**
   * A function, bound to a specific checkbox, which checks that box with the
   * given player choices set.
   */
  export type ChoiceFunc = (choice?: string) => Promise<void>;
</script>

<script lang="ts">
  interface Props {
    section: string;
    boxId: number;
    state: State;
    type?: string; // TODO remove this and move this class logic out
    click?: (doCheck: ChoiceFunc) => void;

    width?: string;
  }
  const { section, boxId, state, type, click, width }: Props = $props();

  const onclick = $derived.by(() => {
    if (state === "available") {
      if (click) {
        return (evt: Event) => {
          evt.preventDefault();
          click(async (costChoice) => {
            await checkBox(section, boxId, costChoice);
          });
        };
      }
      return (evt: Event) => {
        evt.preventDefault();
        checkBox(section, boxId);
      };
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

  .tactics-use {
    margin-right: -2px;
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
