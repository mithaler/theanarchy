<script module lang="ts">
  export type WealthWheelSideSection = "GUILDSMEN" | "ALLIES";
</script>

<script lang="ts">
  import Checkbox, { getState } from "../Checkbox.svelte";
  import { ids, type SectionProps } from "./utils.svelte";

  interface Props extends SectionProps {
    section: WealthWheelSideSection;
  }
  const { section, isMe, checkedBoxes, availableBoxes }: Props = $props();
  const sectionLower = $derived(section.toLowerCase());
</script>

<div class={sectionLower}>
  {#each ids(11) as id (id)}
    <div class="{sectionLower}-box {sectionLower}-{id}">
      <Checkbox
        type={sectionLower}
        {section}
        boxId={id}
        state={getState(id, isMe, checkedBoxes, availableBoxes)}
      />
    </div>
  {/each}
</div>

<style lang="scss">
  .guildsmen {
    position: absolute;
    top: 200px;
    left: 498px;
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

  .allies {
    position: absolute;
    top: 200px;
    left: 648px;
  }

  .allies-box {
    position: absolute;

    &.allies-1 {
      top: 78px;
    }

    &.allies-2 {
      top: 52px;
      left: 26px;
    }

    &.allies-3 {
      top: 104px;
      left: 26px;
    }

    &.allies-4 {
      top: 26px;
      left: 52px;
    }

    &.allies-5 {
      top: 78px;
      left: 52px;
    }

    &.allies-6 {
      top: 130px;
      left: 52px;
    }

    &.allies-7 {
      left: 79px;
    }

    &.allies-8 {
      top: 52px;
      left: 79px;
    }

    &.allies-9 {
      top: 104px;
      left: 79px;
    }

    &.allies-10 {
      top: 130px;
      left: 79px;
    }

    &.allies-11 {
      top: 74px;
      left: 105px;
    }
  }
</style>
