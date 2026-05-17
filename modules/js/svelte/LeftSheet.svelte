<script lang="ts">
  import BasicRow, { type BasicRowType } from "./sections/BasicRow.svelte";
  import { type UnclickableType } from "./sections/UnclickableRow.svelte";
  import UnclickableRow from "./sections/UnclickableRow.svelte";
  import { getCheckedBoxes, getAvailableBoxes } from "../context.svelte";
  import WealthWheelSide, {
    type WealthWheelSideSection,
  } from "./sections/WealthWheelSide.svelte";
  import Siegecraft, {
    type SiegecraftSection,
  } from "./sections/Siegecraft.svelte";

  interface Props {
    playerId: number;
    isMe: boolean;
  }
  const { playerId, isMe }: Props = $props();
</script>

{#snippet basicRow(type: BasicRowType, section: string, length: number = 13)}
  <BasicRow
    {isMe}
    {section}
    {type}
    {length}
    checkedBoxes={getCheckedBoxes(playerId, section)}
    availableBoxes={isMe ? getAvailableBoxes(playerId, section) : null}
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
    {@render basicRow("fortification", "MOAT", 12)}
  </div>

  <!-- TODO figure out how to make these translated -->
  <div class="resource-rows">
    {@render basicRow("resource", "QUARRY & FOREST")}
    {@render basicRow("resource", "FARMS")}
    {@render basicRow("resource", "TRAINING GROUNDS")}
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

  {#each ["GUILDSMEN", "ALLIES"] as side (side)}
    <WealthWheelSide
      {isMe}
      section={side as WealthWheelSideSection}
      checkedBoxes={getCheckedBoxes(playerId, side)}
      availableBoxes={isMe ? getAvailableBoxes(playerId, side) : null}
    />
  {/each}
  {#each ["SIEGECRAFT", "SIEGECRAFT_construction"] as sec (sec)}
    <Siegecraft
      section={sec as SiegecraftSection}
      {isMe}
      checkedBoxes={getCheckedBoxes(playerId, sec)}
      availableBoxes={isMe ? getAvailableBoxes(playerId, sec) : null}
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
      top: 60px;
      left: 106px;
    }

    .resource-rows {
      top: 175px;
      left: 106px;
    }

    .production-rows {
      top: 408px;
      left: 124px;
    }

    .point-rows {
      top: 638px;
      left: 106px;
    }
  }
</style>
