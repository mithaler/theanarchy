<script lang="ts">
  import BasicRow from "./sections/BasicRow.svelte";
  import SimpleBuilding from "./sections/SimpleBuilding.svelte";
  import StValentinesFestival from "./sections/StValentinesFestival.svelte";
  import Tactics from "./sections/Tactics.svelte";
  import Ramparts from "./sections/Ramparts.svelte";
  import { sectionProps } from "./sections/utils.svelte";

  interface Props {
    playerId: number;
    isMe: boolean;
  }
  const { playerId, isMe }: Props = $props();
</script>

{#snippet leadershipRow(section: string)}
  <BasicRow
    {...sectionProps(isMe, playerId, section)}
    {section}
    length={9}
    type="leadership"
  />
{/snippet}

<div class="anarchy-sheet anarchy-right-sheet">
  {@render leadershipRow("GOVERNANCE")}
  {@render leadershipRow("WARCRAFT")}
  {@render leadershipRow("WORSHIP")}
  {@render leadershipRow("ENTERTAINMENT")}

  {#each ["KEEP", "MINT"] as section (section)}
    <SimpleBuilding
      {...sectionProps(isMe, playerId, section)}
      section={section as "KEEP" | "MINT"}
    />
  {/each}

  <Tactics {...sectionProps(isMe, playerId, "TACTICS")} />
  <Ramparts {...sectionProps(isMe, playerId, "RAMPARTS")} />

  <!-- TODO spies, ramparts -->

  <SimpleBuilding
    {...sectionProps(isMe, playerId, "STABLES")}
    section="STABLES"
  />

  <StValentinesFestival
    {...sectionProps(isMe, playerId, "ST VALENTINES FESTIVAL")}
  />
</div>

<style lang="scss">
  .anarchy-right-sheet {
    background: url("img/anarchy_right_sheet.jpg");
    background-size: cover;
  }
</style>
