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
  import Checkbox from "./Checkbox.svelte";

  interface Props {
    playerId: number;
    type: "production" | "points";
    section: UnclickableType;
    checkedBoxes: number[];
  }
  const { section, type, checkedBoxes }: Props = $props();
</script>

<div class={["unclickable-row", type]}>
  {#each Array.from({ length: UNCLICKABLE_ROWS[section] }, (_, i) => i + 1) as id}
    <Checkbox
      {type}
      {section}
      boxId={id}
      state={checkedBoxes.includes(id) ? "checked" : "unclickable"}
    />
  {/each}
</div>

<style lang="scss">
  .unclickable-row {
    display: flex;
  }

  .production {
    scale: 80%;
    transform-origin: left;
    position: relative;
    top: -4px;
    left: -2px;
    margin-bottom: 10.5px;
  }

  .points {
    margin-bottom: 13px;
  }
</style>
