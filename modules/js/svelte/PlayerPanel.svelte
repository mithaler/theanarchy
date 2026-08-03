<script module lang="ts">
  export const dragState: { dragging?: Fighter } = $state({});
</script>

<script lang="ts">
  import type { AnarchyPlayer, PlayerKey } from "../context.svelte";
  import Castle from "./Castle.svelte";
  import type { Fighter } from "./CastleWorkers.svelte";
  import Icon from "./Icon.svelte";

  interface Props {
    player: AnarchyPlayer;
    isMe: boolean;
  }

  let { player, isMe }: Props = $props();

  function ondragstart(field: Fighter) {
    return () => {
      dragState.dragging = field;
    };
  }

  function ondragend() {
    dragState.dragging = undefined;
  }
</script>

{#snippet counter(field: string, draggable: boolean = false)}
  <div class={{ counter: true, draggable }}>
    <div>{player[field as PlayerKey]}</div>
    <div
      class={{ draggable }}
      {draggable}
      ondragstart={draggable ? ondragstart(field as Fighter) : undefined}
      ondragend={draggable ? ondragend : undefined}
      role="img"
    >
      <Icon name={field} />
    </div>
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
          {@render counter(field, isMe)}
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

  .draggable {
    cursor: grab;
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
