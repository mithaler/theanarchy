<script lang="ts">
  import type { AnarchyPlayer } from "../context.svelte";

  interface Props {
    player: AnarchyPlayer;
  }

  let { player }: Props = $props();
</script>

{#each ["serfs", "craftsmen", "materials", "patrons", "silver", "food", "soldiers", "knights"] as field}
  <div>{field} {player[field as keyof AnarchyPlayer]}</div>
{/each}
