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
    top: 203px;
    left: 493px;
  }

  .guildsmen-box {
    position: absolute;

    &.guildsmen-1 {
      top: 78px;
      left: 97px;
    }

    &.guildsmen-2 {
      top: 52px;
      left: 71.4px;
    }

    &.guildsmen-3 {
      top: 103px;
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
      top: 128.4px;
      left: 46px;
    }

    &.guildsmen-7 {
      top: 2px;
      left: 2px;
    }

    &.guildsmen-8 {
      top: 52px;
      left: 20px;
    }

    &.guildsmen-9 {
      top: 103px;
      left: 20px;
    }

    &.guildsmen-10 {
      top: 128.4px;
      left: 2px;
    }

    &.guildsmen-11 {
      top: 74px;
      left: -7px;
    }
  }

  .allies {
    position: absolute;
    top: 203px;
    left: 640.5px;
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
      top: 103.5px;
      left: 26px;
    }

    &.allies-4 {
      top: 27px;
      left: 51.4px;
    }

    &.allies-5 {
      top: 78px;
      left: 51.4px;
    }

    &.allies-6 {
      top: 129px;
      left: 51.4px;
    }

    &.allies-7 {
      top: 2px;
      left: 77px;
    }

    &.allies-8 {
      top: 52px;
      left: 77px;
    }

    &.allies-9 {
      top: 103.5px;
      left: 77px;
    }

    &.allies-10 {
      top: 129px;
      left: 77px;
    }

    &.allies-11 {
      top: 74px;
      left: 102px;
    }
  }
</style>
