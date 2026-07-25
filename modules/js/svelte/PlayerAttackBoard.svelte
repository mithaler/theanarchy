<script lang="ts">
  import { getPlayer } from "../context.svelte";
  import AttackCard from "./cards/AttackCard.svelte";
  import type { PlayerBoardProps } from "./PlayerArea.svelte";

  const { playerId }: PlayerBoardProps = $props();
  const attackCards = $derived(getPlayer(playerId).attackCards);
</script>

<div class="player-attack-board">
  <div class="attack-cards">
    {#if attackCards.finalEscalade}
      <div class="card-wrapper">
        <AttackCard id={attackCards.finalEscalade} face="front" zoom={0.35} />
      </div>
    {/if}
    {#each Object.values(attackCards.attacks) as card (card)}
      <div class="card-wrapper">
        <AttackCard id={card.id} face={card.face} zoom={0.35} />
      </div>
    {/each}
  </div>
  <div class="board"></div>
</div>

<style lang="scss">
  .attack-cards {
    display: flex;
    flex-direction: row;
    margin-bottom: 0.5ch;
    margin-left: 25px;

    .card-wrapper {
      margin-right: 18.7px;
    }
  }
  .board {
    background-image: url("img/player_attack_board.webp");
    background-size: cover;
    width: 786px;
    height: 171px;
    align-self: center;
  }
</style>
