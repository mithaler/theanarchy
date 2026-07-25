<script module lang="ts">
  export const castleState: {
    towerChoiceFunc?: ChoiceFunc;
    wallChoiceFunc?: ChoiceFunc;
  } = $state({});
</script>

<script lang="ts">
  import { ctx, getBga, getPlayer, type PlayerKey } from "../context.svelte";
  import type { ChoiceFunc } from "./Checkbox.svelte";
  import type { PlayerBoardProps } from "./PlayerArea.svelte";
  const { playerId, isMe }: PlayerBoardProps = $props();
  const player = $derived(getPlayer(playerId));

  const WALLS: PlayerKey[] = ["wallTop", "wallRight", "wallBottom", "wallLeft"];
  const TOWERS: PlayerKey[] = [
    "towerLeftBottom",
    "towerLeftTop",
    "towerRightBottom",
    "towerRightTop",
  ];

  function available(choice: PlayerKey): boolean {
    return (
      (choice.startsWith("wall") &&
        (ctx.state.availableWalls ?? []).includes(choice)) ||
      (choice.startsWith("tower") && player[choice] < 2)
    );
  }

  function doCheck(
    choice: PlayerKey,
    choiceFunc: ChoiceFunc,
  ): (e: Event) => Promise<void> {
    return async () => {
      if (!isMe || !available(choice)) {
        return;
      }
      await choiceFunc(choice);
      getBga().states.restoreServerGameState();
      castleState.towerChoiceFunc = undefined;
      castleState.wallChoiceFunc = undefined;
    };
  }
</script>

{#snippet div(
  choice: PlayerKey,
  classes: (string | null)[],
  choiceFunc?: ChoiceFunc,
)}
  <button
    aria-label={choice}
    class={classes}
    onclick={choiceFunc && isMe ? doCheck(choice, choiceFunc) : undefined}
  ></button>
{/snippet}

<div class="castle">
  {#each WALLS as wall (wall)}
    {#if (isMe && castleState.wallChoiceFunc) || player[wall] > 0}
      {@render div(
        wall,
        [
          wall,
          "wall",
          "tower-walls-sprite",
          `wall-${player[wall]}`,
          player[wall] > 0 ? "built" : null,
          isMe && castleState.wallChoiceFunc && available(wall)
            ? "clickable"
            : null,
        ],
        castleState.wallChoiceFunc,
      )}
    {/if}
  {/each}

  {#each TOWERS as tower (tower)}
    {#if (isMe && castleState.towerChoiceFunc) || player[tower] > 0}
      {@render div(
        tower,
        [
          tower,
          "tower",
          "tower-walls-sprite",
          `tower-${player[tower]}`,
          player[tower] > 0 ? "built" : null,
          isMe && castleState.towerChoiceFunc && available(tower)
            ? "clickable"
            : null,
        ],
        castleState.towerChoiceFunc,
      )}
    {/if}
  {/each}

  {#if player.moat > 0}
    {@render div("moat", [
      "moat-sprite",
      "moat-gate-sprite",
      `sprite-${player.moat}`,
    ])}
  {/if}
  {#if player.gate > 0}
    {@render div("gate", [
      "gate-sprite",
      "moat-gate-sprite",
      `sprite-${player.gate}`,
    ])}
  {/if}
</div>

<style lang="scss">
  .clickable {
    cursor: pointer;
    box-shadow: 0px 0px 6px 5px rgba(255, 46, 46, 0.9);
  }

  .castle {
    background-image: url("img/castle_mount.png");
    background-size: cover;
    width: 225px;
    height: 225px;
    margin-right: 1ch;
  }

  .tower-walls-sprite {
    position: absolute;
    transform-origin: top left;
    background-color: transparent;

    &.built {
      background-image: url("img/towers_walls.png");
      background-size: cover;
      background-color: transparent;
    }

    &.wall {
      width: 121px;
      height: 61px;
      scale: 35%;

      &.wallLeft {
        top: 138px;
        left: 81px;
        rotate: 90deg;
      }
      &.wallRight {
        top: 138px;
        left: 168px;
        rotate: 90deg;
      }
      &.wallTop {
        top: 109px;
        left: 93.5px;
      }
      &.wallBottom {
        top: 189px;
        left: 93.5px;
      }
    }
    &.wall-1 {
      background-position: -1px -1px;
    }
    &.wall-2 {
      background-position: -1px -64px;
    }
    &.wall-3 {
      background-position: -1px -127px;
    }
    &.wall-4 {
      background-position: -1px -190px;
    }

    &.tower {
      width: 100px;
      height: 84px;
      scale: 31%;

      &.towerLeftBottom {
        top: 191px;
        left: 58px;
      }
      &.towerLeftTop {
        top: 100px;
        left: 58px;
      }
      &.towerRightBottom {
        top: 191px;
        left: 145px;
      }
      &.towerRightTop {
        top: 100px;
        left: 145px;
      }
    }
    &.tower-1 {
      background-position: 1px -204px;
    }
    &.tower-2 {
      background-position: -1px -287px;
    }
  }

  .moat-gate-sprite {
    width: 246px;
    height: 246px;
    scale: 13%;

    &.moat-sprite {
      position: absolute;
      top: -50px;
      left: 80px;
      background-image: url("img/moat.png");
      transform-origin: center;
    }
    &.gate-sprite {
      position: absolute;
      top: 0px;
      left: -8px;
      background-image: url("img/gate.png");
      transform-origin: bottom;
    }

    &.sprite-1 {
      background-position: -9px -278px;
    }
    // 2 not defined because it's 0, 0!
    &.sprite-3 {
      background-position: -278px -278px;
    }
    &.sprite-4 {
      background-position: -278px 0;
    }
    &.sprite-5 {
      background-position: -548px 0;
    }
    &.sprite-6 {
      background-position: -548px -278px;
    }
  }
</style>
