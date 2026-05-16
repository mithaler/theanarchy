<script module lang="ts">
  export type SiegecraftSection = "SIEGECRAFT" | "SIEGECRAFT_construction";
</script>

<script lang="ts">
  import Checkbox, { getState } from "../Checkbox.svelte";
  import type { SectionProps } from "./utils.svelte";

  interface Props extends SectionProps {
    section: SiegecraftSection;
  }
  const { section, isMe, checkedBoxes, availableBoxes }: Props = $props();
  const length = $derived(section === "SIEGECRAFT" ? 10 : 8);
  const className = $derived(section.toLowerCase().replace("_", "-"));

  function getWidth(id: number): string | undefined {
    if (section === "SIEGECRAFT_construction" && [7, 8].includes(id)) {
      return "30px";
    }
    return undefined;
  }
</script>

<div class={[className, "siegecraft-section"]}>
  {#each Array.from({ length }, (_, i) => i + 1) as id (id)}
    <div class={`${className}-${id}`}>
      <Checkbox
        {section}
        boxId={id}
        state={getState(id, isMe, checkedBoxes, availableBoxes)}
        width={getWidth(id)}
      />
    </div>
  {/each}
</div>

<style lang="scss">
  .siegecraft-section {
    position: absolute;
    display: flex;
    flex-direction: column;
    gap: 10.7px;
  }

  .siegecraft {
    top: 350px;
    left: 546px;

    .siegecraft-1 {
      position: relative;
      left: 79px;
      margin-bottom: 24px;
    }

    .siegecraft-2,
    .siegecraft-3 {
      position: relative;
      left: 78px;
    }

    .siegecraft-4,
    .siegecraft-8,
    .siegecraft-9 {
      position: relative;
      left: 26px;
    }

    .siegecraft-5,
    .siegecraft-6 {
      position: relative;
      left: 52px;
    }

    .siegecraft-10 {
      position: relative;
      bottom: 135px;
    }
  }

  .siegecraft-construction {
    top: 403px;
    left: 737px;
    align-items: flex-end;
  }
</style>
