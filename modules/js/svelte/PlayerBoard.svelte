<script lang="ts">
  import PlayerSheets from "./PlayerSheets.svelte";

  interface Props {
    playerId: number;
    isMe: boolean;
  }
  const { playerId, isMe }: Props = $props();
</script>

<div class="player-board">
  <h2>Player {playerId}</h2>
  <PlayerSheets {playerId} {isMe} />
</div>

<style lang="scss">
  .player-board {
    margin-bottom: 1em;
  }
</style>
