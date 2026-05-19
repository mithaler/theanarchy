<script lang="ts">
  import BasicRow from "./sections/BasicRow.svelte";
  import { getCheckedBoxes, getAvailableBoxes } from "../context.svelte";

  interface Props {
    playerId: number;
    isMe: boolean;
  }
  const { playerId, isMe }: Props = $props();
</script>

{#snippet leadershipRow(section: string)}
  <BasicRow
    {isMe}
    {section}
    length={9}
    type="leadership"
    checkedBoxes={getCheckedBoxes(playerId, section)}
    availableBoxes={isMe ? getAvailableBoxes(section) : null}
  />
{/snippet}

<div class="anarchy-sheet anarchy-right-sheet">
  {@render leadershipRow("GOVERNANCE")}
  {@render leadershipRow("WARCRAFT")}
  {@render leadershipRow("WORSHIP")}
  {@render leadershipRow("ENTERTAINMENT")}
</div>

<style lang="scss">
  .anarchy-right-sheet {
    background: url("img/anarchy_right_sheet.jpg");
    background-size: cover;
  }
</style>
