<script lang="ts">
  import type { AnarchyContext } from "../context.svelte";
  import ResourceRow from "./ResourceRow.svelte";
  import {
    UNCLICKABLE_ROWS,
    type UnclickableType,
  } from "./UnclickableRow.svelte";
  import UnclickableRow from "./UnclickableRow.svelte";
  import { getCheckedBoxes, getAvailableBoxes } from "../context.svelte";

  interface Props {
    ctx: AnarchyContext;
    playerId: number;
    isMe: boolean;
  }
  const { playerId, ctx, isMe }: Props = $props();
</script>

{#snippet resourceRow(section: string)}
  <ResourceRow
    bga={ctx.bga!}
    {playerId}
    {section}
    checkedBoxes={getCheckedBoxes(playerId, section)}
    availableBoxes={isMe ? getAvailableBoxes(playerId, section) : null}
  />
{/snippet}

<div class="left-sheet">
  <!-- TODO figure out how to make these translated -->
  {@render resourceRow("QUARRY & FOREST")}
  {@render resourceRow("FARMS")}
  {@render resourceRow("TRAINING GROUNDS")}
  {#each Object.keys(UNCLICKABLE_ROWS) as unclickableRow}
    <UnclickableRow
      bga={ctx.bga!}
      {playerId}
      section={unclickableRow as UnclickableType}
      checkedBoxes={getCheckedBoxes(playerId, unclickableRow)}
    />
  {/each}
</div>

<style lang="scss">
  .left-sheet :global(.row-label) {
    display: inline-block;
    width: 10em;
  }
</style>
