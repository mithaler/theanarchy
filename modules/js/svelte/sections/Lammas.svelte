<script lang="ts">
  import Checkbox, { getState } from "../Checkbox.svelte";
  import { ids, type SectionProps } from "./utils.svelte";

  const { isMe, checkedBoxes, availableBoxes }: SectionProps = $props();
</script>

{#snippet box(id: number)}
  <div class="box-wrapper box-wrapper-{id}">
    <Checkbox
      section="LAMMAS"
      boxId={id}
      state={getState(id, isMe, checkedBoxes, availableBoxes)}
    />
  </div>
{/snippet}

<div class="lammas">
  <div class="flags row">
    {#each ids(3) as flagRow (flagRow)}
      <div class="flag-row flag-row-{flagRow} row">
        {#each ids(7) as key (key)}
          {@render box(key + 6 + (flagRow - 1) * 7)}
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
    left: 224px;
    top: 707px;
  }

  .flag-row {
    position: relative;

    .box-wrapper {
      margin-right: 1px;
    }
  }

  .flag-row-1 {
    transform: rotate(10deg);
    top: 17px;
    left: -1px;
  }

  .flag-row-2 {
    transform: rotate(-2deg);
    top: 5px;
    left: -2px;
  }

  .flag-row-3 {
    transform: rotate(-20deg);
    left: -27px;
    top: 8px;
  }

  .activation {
    position: absolute;
    left: 115px;
    top: 51px;

    .box-wrapper {
      margin-right: 63.4px;
    }
  }
</style>
