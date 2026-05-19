<script lang="ts">
  import Checkbox, { getState } from "../Checkbox.svelte";
  import { basicChoice, type SectionProps } from "./utils.svelte";
  const { checkedBoxes, availableBoxes, isMe }: SectionProps = $props();

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
  const choiceFunc = (id: number) => {
    if (id === 4) return leadershipChoice;
    else if ([3, 7, 11].includes(id)) return resourceChoice;
    return undefined;
  };

  function width(id: number): string | undefined {
    if (id === 4) return "94px";
    else if (id === 8) return "70px";
    else if (id === 12) return "80px";
  }
</script>

<div class="keep">
  {#each Array.from({ length: 12 }, (_, i) => i + 1) as id (id)}
    <div class={["box-wrapper", `box-wrapper-${id}`]}>
      <Checkbox
        section="KEEP"
        boxId={id}
        state={getState(id, isMe, checkedBoxes, availableBoxes)}
        click={choiceFunc(id)}
        width={width(id)}
      />
    </div>
  {/each}
</div>

<style lang="scss">
  .keep {
    display: flex;
    position: relative;
    left: 242px;
    bottom: 73px;

    .box-wrapper-1 {
      position: relative;
      bottom: 25px;
      left: 143px;
    }
    .box-wrapper-5 {
      position: relative;
      bottom: 25px;
      left: 153px;
    }
    .box-wrapper-9 {
      position: relative;
      bottom: 25px;
      left: 154px;
    }

    .box-wrapper-4 {
      margin-right: 23px;
    }
    .box-wrapper-8 {
      margin-right: 55px;
    }
  }
</style>
