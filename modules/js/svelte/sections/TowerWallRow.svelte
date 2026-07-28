<script lang="ts">
  import { getBga } from "../../context.svelte";
  import { castleState } from "../Castle.svelte";
  import Checkbox, { getState, type ChoiceFunc } from "../Checkbox.svelte";
  import { addCancelButton, ids, type SectionProps } from "./utils.svelte";

  interface Props extends SectionProps {
    section: "WALL" | "TOWER";
  }
  const { section, checkedBoxes, availableBoxes, isMe }: Props = $props();

  function chooseSide(doCheck: ChoiceFunc) {
    const bga = getBga();
    const descriptionmyturn =
      section === "WALL"
        ? "${you} must select a wall to build"
        : "${you} must select a tower to build";
    bga.states.setClientState("fortChoice", { descriptionmyturn });

    const prop: keyof typeof castleState =
      section === "WALL" ? "wallChoiceFunc" : "towerChoiceFunc";

    addCancelButton(bga, () => {
      castleState[prop] = undefined;
    });
    castleState[prop] = doCheck;
  }

  function getWidth(id: number): string | undefined {
    if (
      (section === "TOWER" && id !== 2) ||
      (section === "WALL" && ![2, 5, 9, 14].includes(id))
    ) {
      return "37px";
    }
  }

  const length = $derived({ WALL: 16, TOWER: 8 }[section]);
</script>

<div class={["fort-row", section.toLowerCase()]}>
  {#each ids(length) as id (id)}
    <div class={["box-wrapper", `box-wrapper-${id}`]}>
      <Checkbox
        {section}
        boxId={id}
        state={getState(id, isMe, checkedBoxes, availableBoxes)}
        width={getWidth(id)}
        click={chooseSide}
      />
    </div>
  {/each}
</div>

<style lang="scss">
  .fort-row {
    display: flex;
    margin-bottom: 9px;
  }

  .fort-row.wall {
    padding-top: 1px;

    .box-wrapper {
      margin-right: 6.7px;
    }
    .box-wrapper-6 {
      margin-right: 34px;
    }
    .box-wrapper-11 {
      margin-right: 33px;
    }
  }

  .fort-row.tower {
    .box-wrapper-1 {
      margin-right: 32px;
    }
    .box-wrapper-2 {
      margin-right: 25px;
    }
    .box-wrapper-3 {
      margin-right: 32px;
    }
    .box-wrapper-4 {
      margin-right: 34px;
    }
    .box-wrapper-5 {
      margin-right: 50px;
    }
    .box-wrapper-6 {
      margin-right: 102px;
    }
    .box-wrapper-7 {
      margin-right: 49px;
    }
  }
</style>
