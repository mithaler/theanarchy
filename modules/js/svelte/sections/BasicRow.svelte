<script module lang="ts">
  export type BasicRowType = "resource" | "leadership" | "fortification";
</script>

<script lang="ts">
  import { getBga } from "../../context.svelte";
  import Checkbox, { getState } from "../Checkbox.svelte";
  import type { SectionProps } from "./utils.svelte";

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
      ? async (doCheck: (choice: string) => Promise<void>) => {
          const bga = getBga();
          bga.states.setClientState("moatChoice", {
            descriptionmyturn: _("${you} must choose what to pay"),
          });
          bga.statusBar.addActionButton("SERF", async () => {
            await doCheck("serfs");
            bga.states.restoreServerGameState();
          });
          bga.statusBar.addActionButton("SOLDIER", async () => {
            await doCheck("soldiers");
            bga.states.restoreServerGameState();
          });
          bga.statusBar.addActionButton(
            _("Cancel"),
            () => bga.states.restoreServerGameState(),
            { color: "secondary" },
          );
        }
      : undefined,
  );
</script>

<div class={["basic-row", type, sectionClass]}>
  {#each Array.from({ length }, (_, i) => i + 1) as id (id)}
    <div class={["box-wrapper", `box-wrapper-${id}`]}>
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
    margin-bottom: 10px;
  }

  .leadership {
    position: relative;
    left: 13px;

    &.governance {
      top: 116px;
    }

    &.warcraft {
      top: 222px;
    }

    &.worship {
      top: 441px;
    }

    &.entertainment {
      top: 663px;
    }
  }

  .gate {
    .box-wrapper-1 {
      margin-right: 76px;
    }
    .box-wrapper-2 {
      margin-right: 105px;
    }
    .box-wrapper-3 {
      margin-right: 51px;
    }
    .box-wrapper-4 {
      margin-right: 103px;
    }
    .box-wrapper-5 {
      margin-right: 50px;
    }
  }

  .moat {
    position: relative;
    top: 58px;

    .box-wrapper {
      margin-right: 2px;
    }

    .box-wrapper-4 {
      margin-right: 135px;
    }
    .box-wrapper-8 {
      margin-right: 90px;
    }
  }
</style>
