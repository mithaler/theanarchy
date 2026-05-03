<script lang="ts">
  import type { AnarchyContext } from "../context.svelte";
  import BasicRow from "./BasicRow.svelte";
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
  <BasicRow
    bga={ctx.bga!}
    {playerId}
    {section}
    length={13}
    checkedBoxes={getCheckedBoxes(playerId, section)}
    availableBoxes={isMe ? getAvailableBoxes(playerId, section) : null}
  />
{/snippet}

<div class="anarchy-sheet anarchy-left-sheet">
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
