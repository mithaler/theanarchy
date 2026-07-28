<script module lang="ts">
  import { slide } from "svelte/transition";
  import PathCard from "./cards/PathCard.svelte";
  import { ctx } from "../context.svelte";

  export const state: { cards?: string[] } = $state({});
  const onclick = $derived(
    state.cards && ctx.bga?.players.isCurrentPlayerActive()
      ? (cardId: string) => {
          ctx.bga!.actions.performAction("actChoosePathCard", { cardId });
        }
      : undefined,
  );
</script>

{#if state.cards}
  <div class="path-card-selector" transition:slide>
    {#each state.cards as card (card)}
      <PathCard id={card} {onclick} />
    {/each}
  </div>
{/if}

<style lang="scss">
  .path-card-selector {
    width: 800px;
    display: flex;
    flex-direction: row;
    justify-content: space-evenly;
  }
</style>
