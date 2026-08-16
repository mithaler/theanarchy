<script module lang="ts">
  export type Fighter = "soldiers" | "craftsmen" | "knights";

  const weights: Record<Fighter, number> = {
    craftsmen: 2,
    soldiers: 1,
    knights: 0,
  };
</script>

<script lang="ts">
  import type { PlayerBoardProps } from "./PlayerArea.svelte";
  import { dragState } from "./PlayerPanel.svelte";
  import { getBga } from "../context.svelte";
  import Icon from "./Icon.svelte";

  interface Props extends PlayerBoardProps {
    position: "top" | "left" | "right" | "bottom";
  }
  const { player, isMe, position }: Props = $props();

  function vertical(): boolean {
    return position === "left" || position === "right";
  }

  const defenders = $derived(
    player.defenders
      .filter((d) => d.side === position)
      .sort((a, b) => weights[a.type] - weights[b.type]),
  );

  async function ondrop() {
    await getBga().actions.performAction("actCreateDefender", {
      side: position,
      type: dragState.dragging,
    });
  }
</script>

<div
  class={[
    "castle-workers",
    position,
    { vertical: vertical(), dragging: isMe && !!dragState.dragging },
  ]}
  ondrop={isMe ? ondrop : undefined}
  ondragover={isMe ? (e) => e.preventDefault() : undefined}
  role="list"
>
  {#each defenders as defender (defender.id)}
    <Icon name={defender.type} />
  {/each}
</div>

<style lang="scss">
  .castle-workers {
    position: absolute;
    width: 176px;
    height: 30px;
    border: 1px solid yellow;
    display: flex;

    &.top {
      top: 102px;
    }
    &.bottom {
      top: 315px;
    }
    &.left {
      left: -75px;
    }
    &.right {
      right: -77px;
    }
  }

  .dragging {
    border: 3px solid red;
  }

  .vertical {
    rotate: 90deg;
  }
</style>
