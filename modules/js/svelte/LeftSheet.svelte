<script lang="ts">
  import type { AnarchyContext } from "../context.svelte";
  import BasicRow from "./BasicRow.svelte";
  import { type UnclickableType } from "./UnclickableRow.svelte";
  import UnclickableRow from "./UnclickableRow.svelte";
  import { getCheckedBoxes, getAvailableBoxes } from "../context.svelte";
  import Guildsmen from "./Guildsmen.svelte";

  interface Props {
    playerId: number;
    isMe: boolean;
  }
  const { playerId, isMe }: Props = $props();
</script>

{#snippet resourceRow(section: string)}
  <BasicRow
    {playerId}
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
    {playerId}
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
    {#each ["SERFS", "CRAFTSMEN", "MATERIALS", "PARTRONS", "SILVER", "FOOD", "SOLDIERS", "KNIGHTS"] as productionRow}
      {@render unclickableRow(productionRow as UnclickableType, "production")}
    {/each}
  </div>

  <div class="point-rows">
    {#each ["BRAVERY", "LOYALTY", "INFLUENCE", "MIGHT"] as pointRow}
      {@render unclickableRow(pointRow as UnclickableType, "points")}
    {/each}
  </div>

  <Guildsmen
    {playerId}
    checkedBoxes={getCheckedBoxes(playerId, "GUILDSMEN")}
    availableBoxes={isMe ? getAvailableBoxes(playerId, "GUILDSMEN") : null}
  />
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
