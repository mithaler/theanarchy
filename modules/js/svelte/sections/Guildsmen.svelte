<script lang="ts">
  import Checkbox, { getState } from "../Checkbox.svelte";
  import type { SectionProps } from "./utils.svelte";

  const { checkedBoxes, availableBoxes }: SectionProps = $props();
</script>

<div class="guildsmen">
  {#each Array.from({ length: 11 }, (_, i) => i + 1) as id (id)}
    <div class="guildsmen-box guildsmen-{id}">
      <Checkbox
        type="guildsmen"
        section="GUILDSMEN"
        boxId={id}
        state={getState(id, checkedBoxes, availableBoxes)}
      />
    </div>
  {/each}
</div>

<style lang="scss">
  .guildsmen {
    position: absolute;
    left: 501px;
    top: 246px;
  }

  .guildsmen-box {
    position: absolute;

    &.guildsmen-1 {
      top: 78px;
      left: 98px;
    }

    &.guildsmen-2 {
      top: 52px;
      left: 71px;
    }

    &.guildsmen-3 {
      top: 104px;
      left: 71px;
    }

    &.guildsmen-4 {
      top: 26px;
      left: 46px;
    }

    &.guildsmen-5 {
      top: 78px;
      left: 46px;
    }

    &.guildsmen-6 {
      top: 130px;
      left: 46px;
    }

    /*
      &.guildsmen-7 {
        width: 37px;
      }*/

    &.guildsmen-8 {
      top: 52px;
      left: 20px;
    }

    &.guildsmen-9 {
      top: 104px;
      left: 20px;
    }

    &.guildsmen-10 {
      top: 130px;
    }

    &.guildsmen-11 {
      top: 74px;
      left: -7px;
    }
  }
</style>
