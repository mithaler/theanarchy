<script lang="ts">
  import type { PlayerBoardProps } from "./PlayerArea.svelte";

  const { playerId, isMe }: PlayerBoardProps = $props();
</script>

<div class="player-attack-board"></div>

<style lang="scss">
  .player-attack-board {
    background-image: url("img/player_attack_board.webp");
    background-size: cover;
    width: 786px;
    height: 171px;
    align-self: center;
  }
</style>
