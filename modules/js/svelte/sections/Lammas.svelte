<script lang="ts">
  import Checkbox, { getState } from "../Checkbox.svelte";
  import { ids, type SectionProps } from "./utils.svelte";

  const { isMe, checkedBoxes, availableBoxes }: SectionProps = $props();
</script>

{#snippet box(id: number, noMargin?: boolean)}
  <div class="box-wrapper box-wrapper-{id}">
    <Checkbox
      section="LAMMAS"
      boxId={id}
      state={getState(id, isMe, checkedBoxes, availableBoxes)}
      {noMargin}
    />
  </div>
{/snippet}

<div class="lammas">
  <div class="flags row">
    {#each ids(3) as flagRow (flagRow)}
      <div class="flag-row flag-row-{flagRow} row">
        {#each ids(7) as key (key)}
          {@render box(key + 6 + (flagRow - 1) * 7, true)}
        {/each}
      </div>
    {/each}
  </div>

  <div class="activation row">
    {#each ids(6) as id (id)}
      {@render box(id)}
    {/each}
  </div>
</div>

<style lang="scss">
  .row {
    display: flex;
    flex-direction: row;
  }

  .lammas {
    position: absolute;
    left: 233px;
    top: 718px;
  }

  .flag-row {
    position: relative;
  }

  .flag-row-1 {
    transform: rotate(11deg);
    top: 17px;
    left: -4px;
  }

  .flag-row-2 {
    top: 5px;
    left: -1px;
  }

  .flag-row-3 {
    transform: rotate(-20deg);
    left: -23px;
    top: 7px;
  }

  .activation {
    position: absolute;
    left: 115px;
    top: 50.6px;

    .box-wrapper {
      margin-right: 62.8px;
    }
  }
</style>
