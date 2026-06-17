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
        width={[3, 6].includes(id) ? "39px" : undefined}
      />
    </div>
  {/each}
</div>

<style lang="scss">
  .ramparts {
    display: flex;
    flex-direction: row;
    position: absolute;
    top: 205px;
    left: 348px;

    .box-wrapper {
      margin-right: 56px;
    }
  }
</style>
