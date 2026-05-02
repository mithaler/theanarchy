<script module>
  export const PRODUCTION_ROWS = {
    SERFS: 6,
    CRAFTSMEN: 3,
    MATERIALS: 5,
    PARTRONS: 4,
    SILVER: 4,
    FOOD: 5,
    SOLDIERS: 7,
    KNIGHTS: 5,
  };

  export type ProductionType = keyof typeof PRODUCTION_ROWS;
</script>

<script lang="ts">
  import type { AnarchyBga } from "../context.svelte";
  import Checkbox from "./Checkbox.svelte";

  interface Props {
    bga: AnarchyBga;
    playerId: number;
    section: ProductionType;
    checkedBoxes: number[];
  }
  const { bga, section, checkedBoxes }: Props = $props();
</script>

<div id={section}>
  <span class="row-label">{section}</span>
  {#each Array.from({ length: PRODUCTION_ROWS[section] }, (_, i) => i + 1) as id}
    <Checkbox
      {bga}
      {section}
      boxId={id}
      state={checkedBoxes.includes(id) ? "checked" : "unavailable"}
    />
  {/each}
</div>
