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
  import DraggableIcon, { dragState } from "./DraggableIcon.svelte";
  import { getBga } from "../context.svelte";
  import Icon from "./Icon.svelte";
  import type { CastleMode } from "./Castle.svelte";

  interface Props extends PlayerBoardProps {
    mode: CastleMode;
    position: "top" | "left" | "right" | "bottom";
  }
  const { player, isMe, position, mode }: Props = $props();

  function vertical(): boolean {
    return position === "left" || position === "right";
  }

  const defenders = $derived(
    player.defenders
      .filter((d) => d.side === position)
      .sort((a, b) => weights[a.type] - weights[b.type]),
  );

  const bga = getBga();

  const mustering = $derived(
    bga.states.getCurrentPlayerStateName() === "Muster" &&
      player.musterTokens > 0,
  );

  async function ondrop() {
    if (mustering) {
      await bga.actions.performAction("actMuster", {
        id: dragState.id,
        toSide: position,
      });
    } else {
      await bga.actions.performAction("actCreateDefender", {
        side: position,
        type: dragState.dragging,
      });
    }
  }
</script>

<div
  class={[
    "castle-workers",
    position,
    { vertical: vertical(), dragging: isMe && !!dragState.dragging },
    `mode-${mode}`,
  ]}
  ondrop={isMe ? ondrop : undefined}
  ondragover={isMe ? (e) => e.preventDefault() : undefined}
  role="list"
>
  {#each defenders as defender (defender.id)}
    {#if mustering}
      <DraggableIcon name={defender.type} id={defender.id} />
    {:else}
      <Icon name={defender.type} />
    {/if}
  {/each}
</div>

<style lang="scss">
  .castle-workers {
    position: absolute;
    width: 176px;
    height: 23px;
    border: 1px solid yellow;
    display: flex;
    justify-content: center;

    &.top.mode-panel {
      top: 109px;
    }
    &.top.mode-attack-handler {
      top: 50px;
    }
    &.bottom.mode-panel {
      top: 315px;
    }
    &.bottom.mode-attack-handler {
      top: 424px;
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
