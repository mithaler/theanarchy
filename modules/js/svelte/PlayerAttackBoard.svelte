<script lang="ts">
  import { getPlayer } from "../context.svelte";
  import AttackCard from "./cards/AttackCard.svelte";
  import PathCard from "./cards/PathCard.svelte";
  import type { PlayerBoardProps } from "./PlayerArea.svelte";

  const { playerId }: PlayerBoardProps = $props();
  const player = $derived(getPlayer(playerId));
  const attackCards = $derived(player.attackCards);
</script>

<div class="player-attack-board">
  <div class="attack-cards">
    {#if attackCards.finalEscalade}
      <div class="card-wrapper">
        <AttackCard id={attackCards.finalEscalade} face="front" zoom={0.35} />
      </div>
    {/if}
    {#each Object.values(attackCards.attacks) as card (card.id)}
      <div class="card-wrapper">
        <AttackCard id={card.id} face={card.face} zoom={0.35} />
      </div>
    {/each}
  </div>
  <div class="board"></div>
  <div class="path-cards">
    {#each player.pathCards as pathCard (pathCard)}
      <div class="path-card-wrapper">
        <PathCard id={pathCard} zoom={0.35} />
      </div>
    {/each}
  </div>
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

  .path-cards {
    display: flex;
    flex-direction: row;
    position: relative;
    bottom: 146px;
    margin-bottom: -146px;
    margin-left: 18px;
    z-index: -1;
  }

  .path-card-wrapper {
    margin-right: 20px;
  }
</style>
