<script module>
  export const UNCLICKABLE_ROWS = {
    // Resource production
    SERFS: 6,
    CRAFTSMEN: 3,
    MATERIALS: 5,
    PARTRONS: 4,
    SILVER: 4,
    FOOD: 5,
    SOLDIERS: 7,
    KNIGHTS: 5,

    // Points
    BRAVERY: 24,
    LOYALTY: 24,
    INFLUENCE: 24,
    MIGHT: 24,
  };

  export type UnclickableType = keyof typeof UNCLICKABLE_ROWS;
</script>

<script lang="ts">
  import Checkbox from "../Checkbox.svelte";
  import { ids } from "./utils.svelte";

  interface Props {
    type: "production" | "points";
    section: UnclickableType;
    checkedBoxes: number[];
  }
  const { section, type, checkedBoxes }: Props = $props();
</script>

<div class={["unclickable-row", type]}>
  {#each ids(UNCLICKABLE_ROWS[section]) as id (id)}
    <div class="box-wrapper">
      <Checkbox
        {type}
        {section}
        boxId={id}
        state={checkedBoxes.includes(id) ? "checked" : "unclickable"}
      />
    </div>
  {/each}
</div>

<style lang="scss">
  .unclickable-row {
    display: flex;
  }

  .points {
    .box-wrapper {
      margin-right: 2.4px;
    }
  }

  .production {
    scale: 80%;
    transform-origin: left;
    position: relative;
    top: -4px;
    left: -1.5px;
    margin-bottom: 10px;

    .box-wrapper {
      margin-right: 1px;
    }
  }

  .points {
    margin-bottom: 12.5px;
  }
</style>
