<script lang="ts">
  import BasicRow from "./sections/BasicRow.svelte";
  import SimpleBuilding from "./sections/SimpleBuilding.svelte";
  import StValentinesFestival from "./sections/StValentinesFestival.svelte";
  import Tactics from "./sections/Tactics.svelte";
  import Ramparts from "./sections/Ramparts.svelte";
  import { sectionProps } from "./sections/utils.svelte";
  import Chapel from "./sections/Chapel.svelte";
  import KnightsTraining from "./sections/KnightsTraining.svelte";
  import Tournaments from "./sections/Tournaments.svelte";
  import Brewhouse from "./sections/Brewhouse.svelte";
  import Michaelmas from "./sections/Michaelmas.svelte";
  import Lammas from "./sections/Lammas.svelte";
  import type { PlayerBoardProps } from "./PlayerArea.svelte";

  const { player, isMe }: PlayerBoardProps = $props();
</script>

{#snippet leadershipRow(section: string)}
  <BasicRow
    {...sectionProps(isMe, player, section)}
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
      {...sectionProps(isMe, player, section)}
      section={section as "KEEP" | "MINT"}
    />
  {/each}

  <Tactics {...sectionProps(isMe, player, "TACTICS")} />
  <Ramparts {...sectionProps(isMe, player, "RAMPARTS")} />

  <!-- TODO spies -->

  <SimpleBuilding
    {...sectionProps(isMe, player, "STABLES")}
    section="STABLES"
  />

  <Chapel {...sectionProps(isMe, player, "CHAPEL")} />
  <KnightsTraining {...sectionProps(isMe, player, "KNIGHTS TRAINING")} />
  <StValentinesFestival
    {...sectionProps(isMe, player, "ST VALENTINES FESTIVAL")}
  />
  <Tournaments {...sectionProps(isMe, player, "TOURNAMENTS")} />
  <Brewhouse {...sectionProps(isMe, player, "BREWHOUSE")} />
  <Michaelmas {...sectionProps(isMe, player, "MICHAELMAS")} />
  <Lammas {...sectionProps(isMe, player, "LAMMAS")} />
</div>

<style lang="scss">
  .anarchy-right-sheet {
    background: url("img/anarchy_right_sheet.webp");
    background-size: cover;
    background-position: right;
    width: 789px;
  }
</style>
