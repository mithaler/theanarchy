<script lang="ts">
  import type { AnarchyContext } from "../context.svelte";
  import PlayerSheets from "./PlayerSheets.svelte";

  interface Props {
    ctx: AnarchyContext;
    playerId: number;
    isMe: boolean;
  }
  const { playerId, ctx, isMe }: Props = $props();
</script>

<div class="player-board">
  <h2>Player {playerId}</h2>
  <PlayerSheets {playerId} {ctx} {isMe} />
</div>

<style lang="scss">
  .player-board {
    margin-bottom: 1em;
  }
</style>
