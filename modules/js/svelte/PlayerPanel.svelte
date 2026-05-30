<script lang="ts">
  import type { AnarchyPlayer, PlayerKey } from "../context.svelte";

  interface Props {
    player: AnarchyPlayer;
  }

  let { player }: Props = $props();
</script>

{#each ["serfs", "craftsmen", "materials", "patrons", "silver", "food", "soldiers", "knights"] as field (field)}
  <div>{field} {player[field as PlayerKey]}</div>
{/each}
