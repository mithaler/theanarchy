<script module lang="ts">
  export type Fighter = "soldiers" | "craftsmen" | "knights";

  interface AttackChoiceState {
    needsChoice?: Record<Side, number>;
  }
  export const attackChoice: AttackChoiceState = $state({});

  export enum Hit {
    Beer = 0,
    One = 1,
    Two = 2,
  }

  export const selectedDefenders: Record<
    Side,
    SvelteMap<CastleDefender, Hit>
  > = {
    top: new SvelteMap<CastleDefender, Hit>(),
    left: new SvelteMap<CastleDefender, Hit>(),
    right: new SvelteMap<CastleDefender, Hit>(),
    bottom: new SvelteMap<CastleDefender, Hit>(),
  };
  const weights: Record<Fighter, number> = {
    craftsmen: 2,
    soldiers: 1,
    knights: 0,
  };

  export function totalSelectedStr(
    defenders: SvelteMap<CastleDefender, Hit>,
  ): number {
    return defenders
      .values()
      .reduce(
        (total, hit) =>
          hit === Hit.Beer || hit === Hit.One
            ? total + 1
            : hit === Hit.Two
              ? total + 2
              : total,
        0,
      );
  }
</script>

<script lang="ts">
  import type { PlayerBoardProps } from "./PlayerArea.svelte";
  import DraggableIcon, { dragState } from "./DraggableIcon.svelte";
  import { getBga, type CastleDefender, type Side } from "../context.svelte";
  import Icon from "./Icon.svelte";
  import type { CastleMode } from "./Castle.svelte";
  import { SvelteMap } from "svelte/reactivity";

  interface Props extends PlayerBoardProps {
    mode: CastleMode;
    position: "top" | "left" | "right" | "bottom";
  }
  const { player, isMe, position, mode }: Props = $props();

  const myDefenders = $derived(selectedDefenders[position]);
  const selectedStrength = $derived(totalSelectedStr(myDefenders));

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

  function defenderNextState(
    def: CastleDefender,
    currState?: Hit,
  ): Hit | undefined {
    if (def.type === "knights") {
      if (currState === undefined) {
        return Hit.One;
      } else if (currState === Hit.One) {
        return Hit.Two;
      } else {
        return undefined;
      }
    } else {
      if (currState === undefined) {
        return Hit.One;
      } else if (currState === Hit.One) {
        // TODO: Check if the player actually has beer!
        return Hit.Beer;
      } else if (currState === Hit.Beer) {
        return undefined;
      }
    }
  }

  function clickDefender(def: CastleDefender) {
    if (myDefenders.has(def)) {
      const curr = myDefenders.get(def);
      const next = defenderNextState(def, curr);
      if (next && curr && next > curr && selectedStrength === neededValue) {
        // special case: would this make us over-damage a knight?
        // disable it so the player can choose something else
        myDefenders.delete(def);
      } else if (next !== undefined) {
        myDefenders.set(def, next);
      } else {
        myDefenders.delete(def);
      }
    } else if (neededValue && selectedStrength < neededValue) {
      myDefenders.set(def, defenderNextState(def)!);
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
        <!-- TODO: make this look nice -->
        {#if myDefenders.has(defender)}
          {myDefenders.get(defender)}
        {/if}
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
