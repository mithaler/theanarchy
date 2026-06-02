<script lang="ts">
  import BasicRow from "./sections/BasicRow.svelte";
  import { getCheckedBoxes, getAvailableBoxes } from "../context.svelte";
  import SimpleBuilding from "./sections/SimpleBuilding.svelte";
  import StValentinesFestival from "./sections/StValentinesFestival.svelte";

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

  {#each ["KEEP", "MINT"] as section (section)}
    <SimpleBuilding
      {isMe}
      section={section as "KEEP" | "MINT"}
      checkedBoxes={getCheckedBoxes(playerId, section)}
      availableBoxes={isMe ? getAvailableBoxes(section) : null}
    />
  {/each}

  <!-- TODO spies, tactics, ramparts -->

  <SimpleBuilding
    {isMe}
    section="STABLES"
    checkedBoxes={getCheckedBoxes(playerId, "STABLES")}
    availableBoxes={isMe ? getAvailableBoxes("STABLES") : null}
  />

  <StValentinesFestival
    {isMe}
    checkedBoxes={getCheckedBoxes(playerId, "ST VALENTINES FESTIVAL")}
    availableBoxes={isMe ? getAvailableBoxes("ST VALENTINES FESTIVAL") : null}
  />
</div>

<style lang="scss">
  .anarchy-right-sheet {
    background: url("img/anarchy_right_sheet.jpg");
    background-size: cover;
  }
</style>
