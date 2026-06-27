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

  interface Props {
    playerId: number;
    isMe: boolean;
  }
  const { playerId, isMe }: Props = $props();
</script>

{#snippet basicRow(type: BasicRowType, section: string, length: number = 13)}
  <BasicRow
    {section}
    {type}
    {length}
    {...sectionProps(isMe, playerId, section)}
  />
{/snippet}

{#snippet unclickableRow(
  section: UnclickableType,
  type: "production" | "points",
)}
  <UnclickableRow
    {type}
    section={section as UnclickableType}
    checkedBoxes={getCheckedBoxes(playerId, section)}
  />
{/snippet}

<div class="anarchy-sheet anarchy-left-sheet">
  <div class="fortification-rows">
    {@render basicRow("fortification", "GATE", 6)}
    <TowerWallRow section="TOWER" {...sectionProps(isMe, playerId, "TOWER")} />
    <TowerWallRow section="WALL" {...sectionProps(isMe, playerId, "WALL")} />
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
      <TacticsUse section={tactic} {...sectionProps(isMe, playerId, tactic)} />
    {/each}
  </div>

  <div class="production-rows">
    {#each ["SERFS", "CRAFTSMEN", "MATERIALS", "PARTRONS", "SILVER", "FOOD", "SOLDIERS", "KNIGHTS"] as productionRow (productionRow)}
      {@render unclickableRow(productionRow as UnclickableType, "production")}
    {/each}
  </div>

  <div class="point-rows">
    {#each ["BRAVERY", "LOYALTY", "INFLUENCE", "MIGHT"] as pointRow (pointRow)}
      {@render unclickableRow(pointRow as UnclickableType, "points")}
    {/each}
  </div>

  <Mercenaries {...sectionProps(isMe, playerId, "MERCENARIES")} />
  {#each ["GUILDSMEN", "ALLIES"] as side (side)}
    <WealthWheelSide
      section={side as WealthWheelSideSection}
      {...sectionProps(isMe, playerId, side)}
    />
  {/each}
  {#each ["SIEGECRAFT", "SIEGECRAFT_construction"] as sec (sec)}
    <Siegecraft
      section={sec as SiegecraftSection}
      {...sectionProps(isMe, playerId, sec)}
    />
  {/each}
</div>

<style lang="scss">
  .anarchy-left-sheet {
    background-image: url("img/anarchy_left_sheet.jpg");
    background-size: cover;
    margin-right: 1em;

    div {
      position: absolute;
    }

    .fortification-rows {
      top: 14px;
      left: 103px;
    }

    .resource-rows {
      top: 129px;
      left: 103px;
    }

    .tactic-use-rows {
      top: 215px;
      left: 119px;
    }

    .production-rows {
      top: 362px;
      left: 121px;
    }

    .point-rows {
      top: 592px;
      left: 103px;
    }
  }
</style>
