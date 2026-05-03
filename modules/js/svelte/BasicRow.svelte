<script lang="ts">
  import type { AnarchyBga } from "../context.svelte";
  import Checkbox from "./Checkbox.svelte";

  interface Props {
    bga: AnarchyBga;
    playerId: number;
    section: string;
    type: "resource" | "leadership";
    checkedBoxes: number[];
    availableBoxes: number[] | null;
  }
  const { bga, section, type, checkedBoxes, availableBoxes }: Props = $props();

  function getWidth(id: number) {
    if (type === "resource") {
      return [3, 7, 11].includes(id) ? "37px" : id === 13 ? "56px" : undefined;
    } else if (
      type === "leadership" &&
      ((id === 5 && section === "GOVERNANCE") ||
        (id === 9 && ["WARCRAFT", "WORSHIP"].includes(section)) ||
        (id === 3 && section === "ENTERTAINMENT"))
    ) {
      return "37px";
    }
    return undefined;
  }

  const sectionClass = $derived(section.split(" ")[0].toLowerCase());
</script>

<div class={["basic-row", type, sectionClass]}>
  {#each Array.from({ length: type === "resource" ? 13 : 9 }, (_, i) => i + 1) as id}
    <Checkbox
      {bga}
      {type}
      {section}
      boxId={id}
      state={checkedBoxes.includes(id)
        ? "checked"
        : availableBoxes && availableBoxes.includes(id)
          ? "available"
          : "unavailable"}
      width={getWidth(id)}
    />
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
</style>
