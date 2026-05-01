<script lang="ts">
  import type { AnarchyContext } from "../context.svelte";
  import PlayerBoard from "./PlayerBoard.svelte";

  const { ctx }: { ctx: AnarchyContext } = $props();
  const playerIds = $derived.by(() =>
    Object.keys(ctx.data.players).map((pid) => parseInt(pid, 10)),
  );
</script>

<p>It is round {ctx.data.round}</p>

{#each playerIds as playerId}
  <PlayerBoard {playerId} {ctx} />
{/each}
