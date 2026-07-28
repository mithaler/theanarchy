<script lang="ts">
  import Checkbox, { getState } from "../Checkbox.svelte";
  import { ids, type SectionProps } from "./utils.svelte";

  const { isMe, availableBoxes, checkedBoxes }: SectionProps = $props();
</script>

<div class="ramparts">
  {#each ids(6) as id (id)}
    <div class="box-wrapper box-wrapper-{id}">
      <Checkbox
        section="RAMPARTS"
        boxId={id}
        state={getState(id, isMe, checkedBoxes, availableBoxes)}
        width={[3, 6].includes(id) ? "36px" : undefined}
      />
    </div>
  {/each}
</div>

<style lang="scss">
  .ramparts {
    display: flex;
    flex-direction: row;
    position: absolute;
    top: 210px;
    left: 338px;

    .box-wrapper-1 {
      margin-right: 58px;
    }
    .box-wrapper-2 {
      margin-right: 56px;
    }
    .box-wrapper-3 {
      margin-right: 55px;
    }
    .box-wrapper-4 {
      margin-right: 58px;
    }
    .box-wrapper-5 {
      margin-right: 56px;
    }
  }
</style>
