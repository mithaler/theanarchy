<script module lang="ts">
  export const TACTICS = [
    "COVERS",
    "ROCKS",
    "HOT OIL",
    "LOGS",
    "BOLTS",
  ] as const;
  export type Tactic = (typeof TACTICS)[number];

  // This is so Tactics can pass in state data for the initial tactics choice
  export const tacticChoice: {
    payment?: string;
    func?: ChoiceFunc;
  } = $state({});
</script>

<script lang="ts">
  import Checkbox, { getState, type ChoiceFunc } from "../Checkbox.svelte";
  import { ids, type SectionProps } from "./utils.svelte";

  interface Props extends SectionProps {
    section: Tactic;
  }
  const { section, availableBoxes, checkedBoxes, isMe }: Props = $props();

  // does not use the doCheck passed to it by CheckBox!
  // uses the one from state instead, passed in by Tactics
  const startOnClick = $derived(async () => {
    if (!tacticChoice.payment || !tacticChoice.func) {
      console.error("Bad state with tactic choice func!");
      return;
    }
    await tacticChoice.func(`${tacticChoice.payment}-${section}`);
    tacticChoice.payment = undefined;
    tacticChoice.func = undefined;
  });
</script>

<div class="tactic">
  <!-- Initial checkbox, not available until activated by a client state -->
  <div class="box-wrapper box-wrapper-1">
    <Checkbox
      {section}
      boxId={1}
      state={getState(1, isMe, checkedBoxes, availableBoxes)}
      click={startOnClick}
    />
  </div>

  <!-- Checkbox bubbles, start from 2 (1 is above) -->
  {#each ids(15) as id (id)}
    <div class="box-wrapper box-wrapper-${id + 1}">
      <Checkbox
        type="tactics-use"
        {section}
        boxId={id + 1}
        state={getState(id + 1, isMe, checkedBoxes, availableBoxes)}
      />
    </div>
  {/each}
</div>

<style lang="scss">
  .tactic {
    display: flex;
    flex-direction: row;
    margin-bottom: 11px;

    .box-wrapper-1 {
      margin-right: 58px;
    }
  }
</style>
