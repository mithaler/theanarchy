<script lang="ts">
  import Checkbox, { getState } from "../Checkbox.svelte";
  import { ids, type SectionProps } from "./utils.svelte";
  const { isMe, checkedBoxes, availableBoxes }: SectionProps = $props();
</script>

<div class="brewhouse">
  {#each ids(4) as row (row)}
    <div class="row row-{row}">
      {#each ids(4) as col (col)}
        {const id = (row - 1) * 4 + col}
        <div class="box-wrapper box-wrapper-{id}">
          <Checkbox
            boxId={id}
            section="BREWHOUSE"
            state={getState(id, isMe, checkedBoxes, availableBoxes)}
          />
        </div>
      {/each}
    </div>
  {/each}
</div>

<style lang="scss">
  .brewhouse {
    position: absolute;
    left: 478px;
    top: 574px;
    display: flex;
    flex-direction: column;
  }

  .row {
    display: flex;
    flex-direction: row;
    margin-bottom: 3.3px;
  }

  .box-wrapper {
    margin-right: 3.3px;
  }
</style>
