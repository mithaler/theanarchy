<script lang="ts">
  import type { AnarchyContext } from "../context.svelte";
  import PathCardSelector from "./PathCardSelector.svelte";
  import PlayerBoard from "./PlayerArea.svelte";

  const { ctx }: { ctx: AnarchyContext } = $props();
  const meId = $derived(ctx.bga!.players.getCurrentPlayerId());
  const players = $derived(ctx.data!.players);
</script>

<p>It is round {ctx.data!.round}</p>

<PathCardSelector />

<!-- Show current player first -->
{#if players[meId]}
  <PlayerBoard player={players[meId]} isMe={true} />
{/if}
{#each Object.values(players).filter((p) => p.id != meId.toString()) as player (player.id)}
  <PlayerBoard {player} isMe={false} />
{/each}

<style lang="scss">
  @import url("https://fonts.googleapis.com/css2?family=Manufacturing+Consent&family=Young+Serif&display=swap");

  :global(.blackletter) {
    font-family: "Manufacturing Consent", system-ui;
    font-weight: 400;
    font-style: normal;
  }
  :global(.game-text) {
    font-family: "Young Serif", serif;
    font-weight: 400;
    font-style: normal;
    font-variant-caps: all-small-caps;
  }
</style>
