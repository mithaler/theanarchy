<script module lang="ts">
  export interface PlayerBoardProps {
    playerId: number;
    isMe: boolean;
  }
</script>

<script lang="ts">
  import PlayerSheets from "./PlayerSheets.svelte";
  import Castle from "./Castle.svelte";
  import PlayerAttackBoard from "./PlayerAttackBoard.svelte";
  const props: PlayerBoardProps = $props();
</script>

<div class="player-area">
  <h2>Player {props.playerId}</h2>
  <div class="player-meta">
    <Castle {...props} />
    <PlayerAttackBoard {...props} />
  </div>
  <PlayerSheets {...props} />
</div>

<style lang="scss">
  .player-meta {
    margin-bottom: 1ch;
    display: flex;
    flex-direction: row;
  }
  .player-area {
    margin-bottom: 1em;
  }
</style>
