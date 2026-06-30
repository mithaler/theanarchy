<script lang="ts">
  import Checkbox, { getState } from "../Checkbox.svelte";
  import { ids, type SectionProps } from "./utils.svelte";
  const { isMe, checkedBoxes, availableBoxes }: SectionProps = $props();
</script>

<div class="knights-training">
  {#each ids(6) as id (id)}
    <div class="box-wrapper box-wrapper-{id}">
      <Checkbox
        section="KNIGHTS TRAINING"
        boxId={id}
        state={getState(id, isMe, checkedBoxes, availableBoxes)}
        width="29px"
      />
    </div>
  {/each}
</div>

<style lang="scss">
  .knights-training {
    display: flex;
    flex-direction: column;
    position: absolute;
    top: 329px;
    left: 475px;
  }

  .box-wrapper {
    margin-bottom: 5.8px;
  }
</style>
