<script module lang="ts">
  export type BasicRowType = "resource" | "leadership" | "fortification";
</script>

<script lang="ts">
  import Checkbox, { getState } from "../Checkbox.svelte";
  import { basicChoice, ids, type SectionProps } from "./utils.svelte";

  interface Props extends SectionProps {
    section: string;
    length: number;
    type: BasicRowType;
  }
  const { section, length, isMe, type, checkedBoxes, availableBoxes }: Props =
    $props();

  function getWidth(id: number) {
    if (type === "resource") {
      return [3, 7, 11].includes(id) ? "37px" : id === 13 ? "56px" : undefined;
    } else if (
      (type === "leadership" &&
        ((id === 5 && section === "GOVERNANCE") ||
          (id === 9 && ["WARCRAFT", "WORSHIP"].includes(section)) ||
          (id === 3 && section === "ENTERTAINMENT"))) ||
      section === "GATE"
    ) {
      return "37px";
    } else if (section === "MOAT") {
      if (id % 6 === 0) {
        return "54px";
      } else if (id % 2 === 0) {
        return "36px";
      }
    }
    return undefined;
  }

  const sectionClass = $derived(section.split(" ")[0].toLowerCase());
  const click = $derived(
    section === "MOAT"
      ? basicChoice(_("${you} must choose what to pay"), ["serfs", "soldiers"])
      : undefined,
  );
</script>

<div class={["basic-row", type, sectionClass]}>
  {#each ids(length) as id (id)}
    <div class="box-wrapper box-wrapper-{id}">
      <Checkbox
        {type}
        {section}
        boxId={id}
        state={getState(id, isMe, checkedBoxes, availableBoxes)}
        width={getWidth(id)}
        {click}
      />
    </div>
  {/each}
</div>

<style lang="scss">
  .basic-row {
    display: flex;
  }

  .resource {
    margin-bottom: 10px;

    .box-wrapper {
      margin-right: 2.9px;
    }
  }

  .leadership {
    position: relative;
    left: 13px;

    .box-wrapper {
      margin-right: 2.4px;
    }

    &.governance {
      top: 123px;
    }

    &.warcraft {
      top: 236px;
    }

    &.worship {
      top: 457.6px;
    }

    &.entertainment {
      top: 683px;
    }
  }

  .gate {
    margin-bottom: 10px;

    .box-wrapper-1 {
      margin-right: 76px;
    }
    .box-wrapper-2 {
      margin-right: 102px;
    }
    .box-wrapper-3 {
      margin-right: 50px;
    }
    .box-wrapper-4 {
      margin-right: 102px;
    }
    .box-wrapper-5 {
      margin-right: 49px;
    }
  }

  .moat {
    .box-wrapper {
      margin-right: 3.2px;
    }

    .box-wrapper-4 {
      margin-right: 135px;
    }
    .box-wrapper-8 {
      margin-right: 90px;
    }
  }
</style>
