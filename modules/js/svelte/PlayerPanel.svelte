<script lang="ts">
  import type { AnarchyPlayer, PlayerKey } from "../context.svelte";
  import Castle from "./Castle.svelte";
  import Icon from "./Icon.svelte";

  interface Props {
    player: AnarchyPlayer;
    isMe: boolean;
  }

  let { player, isMe }: Props = $props();
</script>

{#if player}
  <div class="stat-panel">
    <div class="stats">
      <div class="counters">
        {#each ["serfs", "craftsmen", "patrons", "soldiers", "knights"] as field (field)}
          <div class="counter">
            <div>{player[field as PlayerKey]}</div>
            <Icon name={field} />
          </div>
        {/each}
      </div>
      <div class="counters">
        {#each ["food", "materials", "silver"] as field (field)}
          <div class="counter">
            <div>{player[field as PlayerKey]}</div>
            <Icon name={field} />
          </div>
        {/each}
      </div>
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

  .stats {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5ch;
    margin: 1ch 0;
  }

  .counters {
    display: flex;
    flex-direction: row;
    gap: 1ch;
    align-items: center;
  }

  .counter {
    display: flex;
    flex-direction: row;
    align-items: center;
  }
</style>
