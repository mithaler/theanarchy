<script lang="ts">
  import Checkbox, { getState } from "../Checkbox.svelte";
  import { ids, type SectionProps } from "./utils.svelte";
  const { isMe, checkedBoxes, availableBoxes }: SectionProps = $props();

  function getWidth(id: number): string {
    if ([3, 4].includes(id)) {
      return "50px";
    } else if ([5, 6].includes(id)) {
      return "68px";
    } else if ([7, 8, 9, 10].includes(id)) {
      return "33px";
    } else if ([11, 12].includes(id)) {
      return "48px";
    }
    return "37px";
  }
</script>

<div class="tournaments">
  <div class="column activation">
    {#each ids(6) as id (id)}
      <div class="box-wrapper box-wrapper-{id}">
        <Checkbox
          section="TOURNAMENTS"
          boxId={id}
          state={getState(id, isMe, checkedBoxes, availableBoxes)}
          width={getWidth(id)}
        />
      </div>
    {/each}
  </div>
  <div class="column rewards">
    {#each ids(6) as key (key)}
      {@const id = key + 6}
      <div class="box-wrapper box-wrapper-{id}">
        <Checkbox
          section="TOURNAMENTS"
          boxId={id}
          state={getState(id, isMe, checkedBoxes, availableBoxes)}
          width={getWidth(id)}
        />
      </div>
    {/each}
  </div>
</div>

<style lang="scss">
  .tournaments {
    display: flex;
    flex-direction: row;
    position: absolute;
    left: 324px;
    top: 576px;
  }

  .column {
    display: flex;
    flex-direction: column;
  }

  .activation {
    width: 102.4px;
  }

  .box-wrapper {
    margin-bottom: 5.7px;
  }
</style>
