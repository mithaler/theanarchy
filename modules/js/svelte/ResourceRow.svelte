<script lang="ts">
  import type { AnarchyBga } from "../context.svelte";
  import Checkbox from "./Checkbox.svelte";

  interface Props {
    bga: AnarchyBga;
    playerId: number;
    section: string;
    checkedBoxes: number[];
    availableBoxes: number[] | null;
  }
  const { bga, section, checkedBoxes, availableBoxes }: Props = $props();
</script>

<div id={section}>
  <span class="row-label">{section}</span>
  {#each Array.from({ length: 13 }, (_, i) => i + 1) as id}
    <Checkbox
      {bga}
      {section}
      boxId={id}
      state={checkedBoxes.includes(id)
        ? "checked"
        : availableBoxes && availableBoxes.includes(id)
          ? "available"
          : "unavailable"}
    />
  {/each}
</div>
