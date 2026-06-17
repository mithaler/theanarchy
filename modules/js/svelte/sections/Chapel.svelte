<script lang="ts">
  import Checkbox, { getState } from "../Checkbox.svelte";
  import { ids, type SectionProps } from "./utils.svelte";
  const { isMe, checkedBoxes, availableBoxes }: SectionProps = $props();
</script>

<div class="chapels">
  {#each ids(3) as cpl (cpl)}
    <div class="chapel">
      {#each ids(5) as key (key)}
        {@const id = (cpl - 1) * 5 + key}
        <div class="chapel-box chapel-box-{key}">
          <Checkbox
            section="CHAPEL"
            boxId={id}
            state={getState(id, isMe, checkedBoxes, availableBoxes)}
            width={key == 5 ? "59px" : undefined}
          />
        </div>
      {/each}
    </div>
  {/each}
</div>

<style lang="scss">
  .chapels {
    display: flex;
    flex-direction: column;
    position: absolute;
    left: 234px;
    top: 305px;
  }

  .chapel {
    position: relative;
    height: 79px;
  }

  .chapel-box {
    position: absolute;
  }

  .chapel-box-1 {
    left: 148px;
  }

  .chapel-box-2,
  .chapel-box-3,
  .chapel-box-4 {
    top: 24px;
  }

  .chapel-box-2 {
    left: 31px;
  }

  .chapel-box-3 {
    left: 92px;
  }

  .chapel-box-4 {
    left: 148px;
  }

  .chapel-box-5 {
    top: 48.4px;
    left: 88px;
  }
</style>
