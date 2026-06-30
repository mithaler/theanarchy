<script lang="ts">
  import {
    ctx,
    getBga,
    type BoxSet,
    type PlayerKey,
  } from "../../context.svelte";
  import Checkbox, { getState, type ChoiceFunc } from "../Checkbox.svelte";
  import { tacticChoice, TACTICS } from "./TacticsUse.svelte";
  import { addCancelButton, ids, type SectionProps } from "./utils.svelte";

  const { availableBoxes, checkedBoxes, isMe }: SectionProps = $props();

  const click = $derived((doCheck: ChoiceFunc) => {
    const bga = getBga();
    const player = ctx.data!.players[bga.players.getCurrentPlayerId()]!;
    bga.states.setClientState("basicChoice", {
      descriptionmyturn: _("${you} must decide what to pay"),
    });

    function getTacticsChoice(payment: string) {
      // pass along this choice func so TacticsUse can use it
      tacticChoice.payment = payment;
      tacticChoice.func = doCheck;

      // which tactics are available?
      const oldAvailBoxes = ctx.state.availableBoxes;
      const newAvailBoxes: BoxSet = {};
      const checkedBoxes = player.checkedBoxes;
      TACTICS.forEach((tactic) => {
        if (!checkedBoxes[tactic] || checkedBoxes[tactic].length === 0) {
          newAvailBoxes[tactic] = [1];
        }
      });
      ctx.state.availableBoxes = newAvailBoxes;

      bga.states.setClientState("chooseTactics", {
        descriptionmyturn: _("${you} must choose a tactic to unlock"),
      });
      addCancelButton(bga, () => {
        ctx.state.availableBoxes = oldAvailBoxes;
        tacticChoice.payment = undefined;
        tacticChoice.func = undefined;
      });
    }

    (["patrons", "knights"] as PlayerKey[]).forEach((choice) => {
      if (player[choice] > 0) {
        bga.statusBar.addActionButton(choice, () => getTacticsChoice(choice));
      }
    });
    addCancelButton(bga);
  });
</script>

<div class="tactics">
  {#each ids(5) as id (id)}
    <div class="box-wrapper box-wrapper-{id}">
      <Checkbox
        section="TACTICS"
        boxId={id}
        state={getState(id, isMe, checkedBoxes, availableBoxes)}
        {click}
        width={id === 5 ? "39px" : undefined}
      />
    </div>
  {/each}
</div>

<style lang="scss">
  .tactics {
    display: flex;
    flex-direction: row;
    position: absolute;
    top: 171px;
    left: 367px;

    .box-wrapper {
      margin-right: 74px;
    }
  }
</style>
