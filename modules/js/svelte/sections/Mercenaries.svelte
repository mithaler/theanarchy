<script lang="ts">
  import Checkbox, { getState } from "../Checkbox.svelte";
  import { ids, type SectionProps } from "./utils.svelte";
  const { isMe, checkedBoxes, availableBoxes }: SectionProps = $props();
</script>

{#snippet box(id: number, width?: string)}
  <div class="box-wrapper box-wrapper-{id}">
    <Checkbox
      section="MERCENARIES"
      boxId={id}
      state={getState(id, isMe, checkedBoxes, availableBoxes)}
      {width}
    />
  </div>
{/snippet}

<div class="mercenaries">
  <div class="row row-5">
    {@render box(11, "37px")}
  </div>

  <div class="row row-4">
    {#each ids(4) as key (key)}
      {const id = key + 6}
      {@render box(id, "37px")}
    {/each}
  </div>

  <div class="row row-3">
    {#each ids(3) as key (key)}
      {const id = key + 3}
      {@render box(id)}
    {/each}
  </div>

  <div class="row row-2">
    {#each ids(2) as key (key)}
      {const id = key + 1}
      {@render box(id, "37px")}
    {/each}
  </div>

  <div class="row row-1">
    {@render box(1)}
  </div>
</div>

<style lang="scss">
  .mercenaries {
    position: absolute;
    top: 147px;
    left: 543px;
    display: flex;
    flex-direction: column;
    width: 175px;
  }

  .row {
    display: flex;
    flex-direction: row;
    margin-bottom: 8.5px;
    width: 100%;
  }

  .box-wrapper {
    position: relative;
  }

  // Row 5
  .box-wrapper-11 {
    left: 69.4px;
  }

  // Row 4
  .row-4 .box-wrapper {
    margin-right: 7px;
  }

  // Row 3
  .box-wrapper-4 {
    margin-left: 26px;
  }
  .row-3 .box-wrapper {
    margin-right: 31.4px;
  }

  // Row 2
  .box-wrapper-2 {
    margin-left: 43px;
    margin-right: 14px;
  }

  // Row 1
  .box-wrapper-1 {
    margin-left: 79px;
  }
</style>