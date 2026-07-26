<script lang="ts">
  import type { AnarchyPlayer, PlayerKey } from "../context.svelte";
  import Castle from "./Castle.svelte";

  interface Props {
    player: AnarchyPlayer;
    isMe: boolean;
  }

  let { player, isMe }: Props = $props();
</script>

{#if player}
  <div class="stat-panel">
    <div class="counters">
      {#each ["serfs", "craftsmen", "materials", "patrons", "silver", "food", "soldiers", "knights"] as field (field)}
        <div>{field[0].toUpperCase()}{player[field as PlayerKey]}</div>
      {/each}
    </div>
    <Castle {player} {isMe} />
  </div>
{/if}

<style lang="scss">
  .stat-panel {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .counters {
    display: flex;
    flex-direction: row;
    gap: 1ch;
  }
</style>
