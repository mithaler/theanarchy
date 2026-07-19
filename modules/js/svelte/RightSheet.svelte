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

  <!-- TODO spies -->

  <SimpleBuilding
    {...sectionProps(isMe, playerId, "STABLES")}
    section="STABLES"
  />

  <Chapel {...sectionProps(isMe, playerId, "CHAPEL")} />
  <KnightsTraining {...sectionProps(isMe, playerId, "KNIGHTS TRAINING")} />
  <StValentinesFestival
    {...sectionProps(isMe, playerId, "ST VALENTINES FESTIVAL")}
  />
  <Tournaments {...sectionProps(isMe, playerId, "TOURNAMENTS")} />
  <Brewhouse {...sectionProps(isMe, playerId, "BREWHOUSE")} />
  <Michaelmas {...sectionProps(isMe, playerId, "MICHAELMAS")} />
  <Lammas {...sectionProps(isMe, playerId, "LAMMAS")} />
</div>

<style lang="scss">
  .anarchy-right-sheet {
    background: url("img/anarchy_right_sheet.jpg");
    background-size: cover;
  }
</style>
