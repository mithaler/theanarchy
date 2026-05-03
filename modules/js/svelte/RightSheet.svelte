<script lang="ts">
  import type { AnarchyContext } from "../context.svelte";
  import BasicRow from "./BasicRow.svelte";
  import { getCheckedBoxes, getAvailableBoxes } from "../context.svelte";

  interface Props {
    ctx: AnarchyContext;
    playerId: number;
    isMe: boolean;
  }
  const { playerId, ctx, isMe }: Props = $props();
</script>

{#snippet leadershipRow(section: string)}
  <BasicRow
    bga={ctx.bga!}
    {playerId}
    {section}
    length={9}
    checkedBoxes={getCheckedBoxes(playerId, section)}
    availableBoxes={isMe ? getAvailableBoxes(playerId, section) : null}
  />
{/snippet}

<div class="anarchy-sheet anarchy-left-sheet">
  {@render leadershipRow("GOVERNANCE")}
  {@render leadershipRow("WARCRAFT")}
  {@render leadershipRow("WORSHIP")}
  {@render leadershipRow("ENTERTAINMENT")}
</div>
