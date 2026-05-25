<script lang="ts">
  import { getPlayer, type AnarchyPlayer } from "../context.svelte";
  import type { PlayerBoardProps } from "./PlayerBoard.svelte";
  const { playerId }: PlayerBoardProps = $props();
  const player = $derived(getPlayer(playerId));
</script>

{#snippet div(classes: string[])}
  <div class={classes}></div>
{/snippet}

<div class="castle">
  {#each ["wallTop", "wallRight", "wallBottom", "wallLeft"] as wall (wall)}
    {#if player[wall as keyof AnarchyPlayer] > 0}
      {@render div([
        wall,
        "wall",
        "tower-walls-sprite",
        `wall-${player[wall as keyof AnarchyPlayer]}`,
      ])}
    {/if}
  {/each}

  {#each ["towerLeftBottom", "towerLeftTop", "towerRightBottom", "towerRightTop"] as tower (tower)}
    {#if player[tower as keyof AnarchyPlayer] > 0}
      {@render div([
        tower,
        "tower",
        "tower-walls-sprite",
        `tower-${player[tower as keyof AnarchyPlayer]}`,
      ])}
    {/if}
  {/each}

  {#if player.moat > 0}
    {@render div(["moat-sprite", "moat-gate-sprite", `sprite-${player.moat}`])}
  {/if}
  {#if player.gate > 0}
    {@render div(["gate-sprite", "moat-gate-sprite", `sprite-${player.gate}`])}
  {/if}
</div>

<style lang="scss">
  .castle {
    background-image: url("img/castle_mount.png");
    background-size: cover;
    width: 225px;
    height: 225px;
  }

  .towers-walls-sprite {
    background-image: url("img/towers_walls.png");

    &.wall {
      width: 121px;
      height: 61px;
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
      height: 100px;
    }
    &.tower-1 {
      background-position: -1px -253px;
    }
    &.tower-2 {
      background-position: -1px -355px;
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
