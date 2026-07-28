<script lang="ts">
  import BasicRow, { type BasicRowType } from "./sections/BasicRow.svelte";
  import { type UnclickableType } from "./sections/UnclickableRow.svelte";
  import UnclickableRow from "./sections/UnclickableRow.svelte";
  import { getCheckedBoxes } from "../context.svelte";
  import WealthWheelSide, {
    type WealthWheelSideSection,
  } from "./sections/WealthWheelSide.svelte";
  import Siegecraft, {
    type SiegecraftSection,
  } from "./sections/Siegecraft.svelte";
  import TowerWallRow from "./sections/TowerWallRow.svelte";
  import TacticsUse, { TACTICS } from "./sections/TacticsUse.svelte";
  import { sectionProps } from "./sections/utils.svelte";
  import Mercenaries from "./sections/Mercenaries.svelte";
  import Discontent from "./sections/Discontent.svelte";
  import type { PlayerBoardProps } from "./PlayerArea.svelte";

  const { player, isMe }: PlayerBoardProps = $props();
</script>

{#snippet basicRow(type: BasicRowType, section: string, length: number = 13)}
  <BasicRow
    {section}
    {type}
    {length}
    {...sectionProps(isMe, player, section)}
  />
{/snippet}

{#snippet unclickableRow(
  section: UnclickableType,
  type: "production" | "points",
)}
  <UnclickableRow
    {type}
    section={section as UnclickableType}
    checkedBoxes={getCheckedBoxes(player, section)}
  />
{/snippet}

<div class="anarchy-sheet anarchy-left-sheet">
  <div class="fortification-rows">
    {@render basicRow("fortification", "GATE", 6)}
    <TowerWallRow section="TOWER" {...sectionProps(isMe, player, "TOWER")} />
    <TowerWallRow section="WALL" {...sectionProps(isMe, player, "WALL")} />
    {@render basicRow("fortification", "MOAT", 12)}
  </div>

  <!-- TODO figure out how to make these translated -->
  <div class="resource-rows">
    {@render basicRow("resource", "QUARRY & FOREST")}
    {@render basicRow("resource", "FARMS")}
    {@render basicRow("resource", "TRAINING GROUNDS")}
  </div>

  <div class="tactic-use-rows">
    {#each TACTICS as tactic (tactic)}
      <TacticsUse section={tactic} {...sectionProps(isMe, player, tactic)} />
    {/each}
  </div>

  <div class="production-rows">
    {#each ["SERFS", "CRAFTSMEN", "MATERIALS", "PARTRONS", "SILVER", "FOOD", "SOLDIERS", "KNIGHTS"] as productionRow (productionRow)}
      {@render unclickableRow(productionRow as UnclickableType, "production")}
    {/each}
  </div>

  <Discontent
    discontent={getCheckedBoxes(player, "DISCONTENT")}
    joy={getCheckedBoxes(player, "JOY")}
  />

  <div class="point-rows">
    {#each ["BRAVERY", "LOYALTY", "INFLUENCE", "MIGHT"] as pointRow (pointRow)}
      {@render unclickableRow(pointRow as UnclickableType, "points")}
    {/each}
  </div>

  <Mercenaries {...sectionProps(isMe, player, "MERCENARIES")} />
  {#each ["GUILDSMEN", "ALLIES"] as side (side)}
    <WealthWheelSide
      section={side as WealthWheelSideSection}
      {...sectionProps(isMe, player, side)}
    />
  {/each}
  {#each ["SIEGECRAFT", "SIEGECRAFT_construction"] as sec (sec)}
    <Siegecraft
      section={sec as SiegecraftSection}
      {...sectionProps(isMe, player, sec)}
    />
  {/each}
</div>

<style lang="scss">
  .anarchy-left-sheet {
    background-image: url("img/anarchy_left_sheet.webp");
    background-size: cover;
    width: 790px;

    div {
      position: absolute;
    }

    .fortification-rows {
      top: 24px;
      left: 110px;
    }

    .resource-rows {
      top: 136px;
      left: 111px;
    }

    .tactic-use-rows {
      top: 220px;
      left: 126px;
    }

    .production-rows {
      top: 363px;
      left: 128px;
    }

    .point-rows {
      top: 586px;
      left: 110px;
    }
  }
</style>
