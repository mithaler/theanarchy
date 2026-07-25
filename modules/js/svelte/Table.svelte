<script lang="ts">
  import type { AnarchyContext } from "../context.svelte";
  import PathCardSelector from "./PathCardSelector.svelte";
  import PlayerBoard from "./PlayerArea.svelte";

  const { ctx }: { ctx: AnarchyContext } = $props();
  const playerIds = $derived.by(() =>
    Object.keys(ctx.data!.players).map((pid) => parseInt(pid, 10)),
  );

  const meId = $derived(ctx.bga!.players.getCurrentPlayerId());
</script>

<p>It is round {ctx.data!.round}</p>

<PathCardSelector />

<!-- Show current player first -->
{#if playerIds.includes(meId)}
  <PlayerBoard playerId={meId} isMe={true} />
{/if}
{#each playerIds.filter((p) => p !== meId) as playerId (playerId)}
  <PlayerBoard {playerId} isMe={false} />
{/each}

<style lang="scss">
  @import url("https://fonts.googleapis.com/css2?family=UnifrakturMaguntia&family=Young+Serif&display=swap");

  :global(.fraktur) {
    font-family: "UnifrakturMaguntia", cursive;
    font-weight: 400;
    font-style: normal;
  }
  :global(.young-serif) {
    font-family: "Young Serif", serif;
    font-weight: 400;
    font-style: normal;
  }
</style>
