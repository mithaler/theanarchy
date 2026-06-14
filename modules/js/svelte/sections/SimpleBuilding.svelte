<script lang="ts">
  import Checkbox, { getState } from "../Checkbox.svelte";
  import { basicChoice, ids, type SectionProps } from "./utils.svelte";

  interface Props extends SectionProps {
    section: "KEEP" | "MINT" | "STABLES";
  }
  const { section, checkedBoxes, availableBoxes, isMe }: Props = $props();

  const length = $derived({ KEEP: 12, MINT: 9, STABLES: 8 }[section]);

  const leadershipChoice = basicChoice(_("${you} must choose a reward"), [
    "WARCRAFT",
    "WORSHIP",
    "ENTERTAINMENT",
  ]);
  const resourceChoice = basicChoice(_("${you} must choose a reward"), [
    "silver",
    "food",
    "materials",
  ]);
  const keepChoiceFunc = (id: number) => {
    if (id === 4) return leadershipChoice;
    else if ([3, 7, 11].includes(id)) return resourceChoice;
    return undefined;
  };
  const stableChoiceFunc = (id: number) => {
    if (![1, 5].includes(id)) {
      return basicChoice(_("${you} must choose what to pay"), [
        "serfs",
        "soldiers",
      ]);
    }
  };

  function keepWidth(id: number): string | undefined {
    if (id === 4) return "94px";
    else if (id === 8) return "70px";
    else if (id === 12) return "80px";
  }
  function mintWidth(id: number): string | undefined {
    if ([3, 6, 9].includes(id)) {
      return "57px";
    }
    return undefined;
  }
  function build(id: number): string | undefined {
    if (section === "KEEP") {
      return { 1: "small-build", 5: "medium-build", 9: "large-build" }[id];
    } else if (section === "MINT") {
      return { 1: "small-build", 4: "medium-build", 7: "large-build" }[id];
    } else if (section === "STABLES") {
      return { 1: "small-build", 5: "medium-build" }[id];
    }
    return undefined;
  }
</script>

<div class={["simple-building", section.toLowerCase()]}>
  {#each ids(length) as id (id)}
    <div class={["box-wrapper", `box-wrapper-${id}`, build(id)]}>
      <Checkbox
        {section}
        boxId={id}
        state={getState(id, isMe, checkedBoxes, availableBoxes)}
        click={section === "KEEP"
          ? keepChoiceFunc(id)
          : section === "STABLES"
            ? stableChoiceFunc(id)
            : undefined}
        width={section === "KEEP"
          ? keepWidth(id)
          : section === "MINT"
            ? mintWidth(id)
            : undefined}
      />
    </div>
  {/each}
</div>

<style lang="scss">
  .simple-building {
    display: flex;
    position: relative;
    left: 242px;
  }

  .small-build,
  .medium-build,
  .large-build {
    position: relative;
    bottom: 25px;
  }
  .small-build {
    left: 143px;
  }
  .medium-build {
    left: 153px;
  }
  .large-build {
    left: 154px;
  }

  .keep {
    bottom: 73px;

    .box-wrapper-4 {
      margin-right: 23px;
    }
    .box-wrapper-8 {
      margin-right: 55px;
    }
  }

  .mint {
    bottom: 34px;

    .box-wrapper-3 {
      margin-right: 81px;
    }
    .box-wrapper-6 {
      margin-right: 89px;
    }
  }

  .stables {
    top: 115px;

    .box-wrapper-2 {
      margin-left: 17px;
    }
    .box-wrapper-4 {
      margin-right: 71px;
    }
    .box-wrapper-6 {
      margin-left: 26px;
    }
  }
</style>
