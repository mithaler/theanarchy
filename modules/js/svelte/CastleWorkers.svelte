<script module lang="ts">
  export type Fighter = "soldiers" | "craftsmen" | "knights";

  interface AttackChoiceState {
    needsChoice?: Record<Side, number>;
  }
  export const attackChoice: AttackChoiceState = $state({});
  export const selectedDefenders: Record<Side, SvelteSet<CastleDefender>> = {
    top: new SvelteSet<CastleDefender>(),
    left: new SvelteSet<CastleDefender>(),
    right: new SvelteSet<CastleDefender>(),
    bottom: new SvelteSet<CastleDefender>(),
  };

  const weights: Record<Fighter, number> = {
    craftsmen: 2,
    soldiers: 1,
    knights: 0,
  };
</script>

<script lang="ts">
  import type { PlayerBoardProps } from "./PlayerArea.svelte";
  import DraggableIcon, { dragState } from "./DraggableIcon.svelte";
  import { getBga, type CastleDefender, type Side } from "../context.svelte";
  import Icon from "./Icon.svelte";
  import type { CastleMode } from "./Castle.svelte";
  import { SvelteSet } from "svelte/reactivity";

  interface Props extends PlayerBoardProps {
    mode: CastleMode;
    position: "top" | "left" | "right" | "bottom";
  }
  const { player, isMe, position, mode }: Props = $props();

  const myDefenders = $derived(selectedDefenders[position]);

  const selectedStrength = $derived.by(() => {
    let out = 0;
    myDefenders?.forEach((def) => {
      if (["soldiers", "craftsmen", "tactics"].includes(def.type)) {
        out += 1;
      } else if (def.type === "knights") {
        out += def.exhausted ? 1 : 2;
      }
    });
    return out;
  });

  const neededValue = $derived(
    attackChoice.needsChoice ? attackChoice.needsChoice[position] : undefined,
  );

  function vertical(): boolean {
    return position === "left" || position === "right";
  }

  const defenders = $derived(
    player.defenders
      .filter((d) => d.side === position)
      .sort((a, b) => weights[a.type] - weights[b.type]),
  );

  const bga = getBga();

  const interaction = $derived.by(() => {
    if (
      mode === "attack-handler" &&
      player.musterTokens > 0 &&
      bga.states.getCurrentPlayerStateName() === "Muster"
    ) {
      return "mustering";
    } else if (
      mode === "attack-handler" &&
      attackChoice.needsChoice &&
      position in attackChoice.needsChoice
    ) {
      return "allocatingHits";
    } else if (bga.states.getCurrentPlayerStateName() === "CheckBoxes") {
      return "allocatingDefenders";
    }
    return "none";
  });

  function clickDefender(def: CastleDefender) {
    if (myDefenders.has(def)) {
      myDefenders.delete(def);
    } else if (neededValue && selectedStrength < neededValue) {
      myDefenders.add(def);
    }
  }

  async function ondrop() {
    if (interaction === "mustering") {
      await bga.actions.performAction("actMuster", {
        id: dragState.id,
        toSide: position,
      });
    } else if (interaction === "allocatingDefenders") {
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
    {#if interaction === "mustering"}
      <DraggableIcon name={defender.type} id={defender.id} />
    {:else if interaction === "allocatingHits"}
      <button
        class={[
          "defender-button",
          {
            clickable:
              (neededValue && selectedStrength < neededValue) ||
              myDefenders.has(defender),
            selected: myDefenders.has(defender),
          },
        ]}
        onclick={() => clickDefender(defender)}
      >
        <Icon name={defender.type} />
      </button>
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

  .defender-button {
    width: 53px;
    background: none;

    &.clickable {
      cursor: pointer;
    }

    &.selected {
      border: 1px solid red;
    }
  }

  .vertical {
    rotate: 90deg;
  }
</style>
