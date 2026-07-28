<script module lang="ts">
  export type SiegecraftSection = "SIEGECRAFT" | "SIEGECRAFT_construction";
</script>

<script lang="ts">
  import Checkbox, { getState } from "../Checkbox.svelte";
  import { ids, type SectionProps } from "./utils.svelte";

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
  {#each ids(length) as id (id)}
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
    gap: 9.9px;
  }

  .siegecraft {
    top: 306px;
    left: 539px;

    .siegecraft-1 {
      position: relative;
      left: 77px;
      margin-bottom: 24px;
    }

    .siegecraft-2,
    .siegecraft-3 {
      position: relative;
      left: 76px;
    }

    .siegecraft-4,
    .siegecraft-8,
    .siegecraft-9 {
      position: relative;
      left: 25px;
    }

    .siegecraft-5,
    .siegecraft-6 {
      position: relative;
      left: 51px;
    }

    .siegecraft-10 {
      position: relative;
      bottom: 132px;
    }
  }

  .siegecraft-construction {
    top: 358px;
    left: 724.3px;
    align-items: flex-end;
  }
</style>
