<script lang="ts">
  import type { AnarchyPlayer, PlayerKey } from "../context.svelte";
  import Castle from "./Castle.svelte";
  import type { Fighter } from "./CastleWorkers.svelte";
  import DraggableIcon from "./DraggableIcon.svelte";
  import Icon from "./Icon.svelte";

  interface Props {
    player: AnarchyPlayer;
    isMe: boolean;
  }

  let { player, isMe }: Props = $props();
</script>

{#snippet counter(field: string, draggable: boolean = false)}
  <div class={{ counter: true }}>
    <div>{player[field as PlayerKey]}</div>
    {#if draggable}
      <DraggableIcon name={field as Fighter} />
    {:else}
      <Icon name={field} />
    {/if}
  </div>
{/snippet}

{#if player}
  <div class="stat-panel">
    <div class="stats">
      <div class="counters">
        {#each ["serfs", "patrons"] as field (field)}
          {@render counter(field)}
        {/each}
        {#each ["craftsmen", "soldiers", "knights"] as field (field)}
          {@render counter(field, isMe && player[field as PlayerKey] > 0)}
        {/each}
      </div>
      <div class="counters">
        {#each ["food", "materials", "silver"] as field (field)}
          {@render counter(field)}
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
