<script lang="ts">
  import { ctx, getBga } from "../../context.svelte";
  import Checkbox, { getState, type ChoiceFunc } from "../Checkbox.svelte";
  import {
    addCancelButton,
    ids,
    multiResourceChoice,
    type SectionProps,
  } from "./utils.svelte";
  const { isMe, checkedBoxes, availableBoxes }: SectionProps = $props();
  const choiceCounts: number[] = [1, 1, 3, 3, 2, 1, 1, 2, 3];

  /**
   * This works around a bug in BGA's framework: when using client states, restoreServerGameState()
   * doesn't work correctly: it restores the _main_ state, not the _private_ state, which breaks the
   * UI. We have to detect this case and work around it by entering a client state that looks like
   * it. I hate this.
   */
  function restoreFakeMichaelmasState() {
    const bga = getBga();
    if (bga.states.getCurrentPlayerStateName() === "Michaelmas") {
      bga.states.setClientState("fakeMichaelmas", {
        descriptionmyturn: _("${you} must fill in Michaelmas numbers"),
      });
    }
  }

  const advanceToResourceChoice = $derived(
    async (boxId: number, num: string, doCheck: ChoiceFunc) => {
      // did we just complete a set?
      const setIdx = Math.floor((boxId - 7) / 2);
      const otherId = boxId % 2 == 1 ? boxId + 1 : boxId - 1;

      if (checkedBoxes.includes(otherId)) {
        const count = choiceCounts[setIdx];
        // we just completed a set, so we need resource choices from the player
        multiResourceChoice(
          count,
          (choices) => doCheck(`${num},${choices.join(",")}`),
          restoreFakeMichaelmasState,
        );
      } else {
        // we didn't just complete a set; just write the number
        await doCheck(num);
        restoreFakeMichaelmasState();
      }
    },
  );

  const clickFunc = $derived((boxId: number) => {
    return (doCheck: ChoiceFunc) => {
      // hoo boy.
      // first, check if there are multiple number choices for this box.
      // if so, ask which one.
      // then, check if a set was just completed.
      // then, if there are resource reward choices, ask which the player wants.
      // yikes.
      const possibleNums = Object.entries(
        ctx.state.availableBoxesByNum!,
      ).reduce(
        (nums, [num, boxIds]) =>
          boxIds.includes(boxId) ? [...nums, num] : nums,
        [] as string[],
      );
      if (possibleNums.length > 1) {
        const bga = getBga();
        bga.states.setClientState("basicChoice", {
          descriptionmyturn: _("${you} must choose which number to write"),
        });
        possibleNums.forEach((choice) => {
          bga.statusBar.addActionButton(choice, async () => {
            advanceToResourceChoice(boxId, choice, doCheck);
          });
        });
        addCancelButton(bga, restoreFakeMichaelmasState);
      } else {
        // there's only one number choice, just use it
        advanceToResourceChoice(boxId, possibleNums[0], doCheck);
      }
    };
  });
</script>

{#snippet box(id: number, includeClick: boolean = true, width?: string)}
  <div class="box-wrapper box-wrapper-{id}">
    <Checkbox
      section="MICHAELMAS"
      boxId={id}
      state={getState(id, isMe, checkedBoxes, availableBoxes)}
      {width}
      click={includeClick ? clickFunc(id) : undefined}
    />
  </div>
{/snippet}

<div class="michaelmas">
  <div class="activation">
    <div class="column column-1">
      {#each ids(3) as id (id)}
        {@render box(id, false)}
      {/each}
    </div>
    <div class="column column-2">
      {#each ids(3) as id (id)}
        {@render box(id + 3, false)}
      {/each}
    </div>
  </div>

  <div class="numbers column">
    <div class="first-number-row row">
      <!-- 25 and 26 are results that are in weird positions -->
      {@render box(7)}
      {@render box(8)}
      {@render box(25, false)}
      {@render box(9)}
      {@render box(10)}
      {@render box(26, false)}
    </div>
    <div class="column">
      {#each ids(7) as col (col)}
        <div class="number-set row">
          {const id = 11 + (col - 1) * 2}
          {@render box(id)}
          {@render box(id + 1)}
        </div>
      {/each}
    </div>
    <div class="result-column column">
      {#each ids(7) as col (col)}
        {@render box(col + 26)}
      {/each}
    </div>
  </div>
</div>

<style lang="scss">
  .michaelmas {
    position: absolute;
    left: 600px;
    top: 509px;
  }

  .column {
    display: flex;
    flex-direction: column;
  }

  .activation {
    display: flex;
    flex-direction: row;

    .box-wrapper {
      margin-bottom: 4px;
    }

    .column-1 {
      margin-left: 60.5px;
    }

    .column-2 {
      margin-left: 68px;
    }
  }

  .row {
    display: flex;
    flex-direction: row;
  }

  .numbers {
    align-items: flex-start;
    margin-top: 5px;

    .box-wrapper {
      margin-bottom: 2.5px;
      margin-right: 2.5px;
    }

    .first-number-row {
      width: 100%;

      .box-wrapper-25 {
        margin-left: 16.5px;
      }

      .box-wrapper-9 {
        margin-left: 11px;
      }

      .box-wrapper-26 {
        margin-left: 18px;
      }
    }
  }

  .result-column {
    position: relative;
    left: 73.4px;
    bottom: 143.5px;
  }
</style>
