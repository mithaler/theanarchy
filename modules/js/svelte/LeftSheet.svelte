<script lang="ts">
  import BasicRow from "./sections/BasicRow.svelte";
  import { type UnclickableType } from "./sections/UnclickableRow.svelte";
  import UnclickableRow from "./sections/UnclickableRow.svelte";
  import { getCheckedBoxes, getAvailableBoxes } from "../context.svelte";
  import WealthWheelSide, {
    type WealthWheelSideType,
  } from "./sections/WealthWheelSide.svelte";

  interface Props {
    playerId: number;
    isMe: boolean;
  }
  const { playerId, isMe }: Props = $props();
</script>

{#snippet resourceRow(section: string)}
  <BasicRow
    {isMe}
    {section}
    type="resource"
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
  <!-- TODO figure out how to make these translated -->
  <div class="resource-rows">
    {@render resourceRow("QUARRY & FOREST")}
    {@render resourceRow("FARMS")}
    {@render resourceRow("TRAINING GROUNDS")}
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
      section={side as WealthWheelSideType}
      checkedBoxes={getCheckedBoxes(playerId, side)}
      availableBoxes={isMe ? getAvailableBoxes(playerId, side) : null}
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
