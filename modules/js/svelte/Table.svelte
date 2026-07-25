<script lang="ts">
  import type { AnarchyContext } from "../context.svelte";
  import PlayerBoard from "./PlayerArea.svelte";

  const { ctx }: { ctx: AnarchyContext } = $props();
  const playerIds = $derived.by(() =>
    Object.keys(ctx.data!.players).map((pid) => parseInt(pid, 10)),
  );

  const meId = $derived(ctx.bga!.players.getCurrentPlayerId());
</script>

<p>It is round {ctx.data!.round}</p>

<!-- Show current player first -->
{#if playerIds.includes(meId)}
  <PlayerBoard playerId={meId} isMe={true} />
{/if}
{#each playerIds.filter((p) => p !== meId) as playerId (playerId)}
  <PlayerBoard {playerId} isMe={false} />
{/each}
