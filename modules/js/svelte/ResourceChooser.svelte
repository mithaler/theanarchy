<script lang="ts" module>
  export interface Values {
    materials: number;
    food: number;
    silver: number;
  }
</script>

<script lang="ts">
  import { slide } from "svelte/transition";

  const {
    values,
    count,
    confirm,
  }: { values: Values; count: number; confirm: () => Promise<void> } = $props();
  const maxed = $derived(
    Object.values(values).reduce((sum, val) => sum + val, 0) >= count,
  );
  const resourceTypes: (keyof Values)[] = ["food", "materials", "silver"];
</script>

<div transition:slide class="chooser">
  {#each resourceTypes as resource (resource)}
    <div class="resource {resource}">
      <button
        class="button"
        disabled={values[resource] < 1}
        onclick={() => values[resource]--}
      >
        -
      </button>
      {resource}: {values[resource]}
      <button
        class="button"
        disabled={maxed}
        onclick={() => values[resource]++}
      >
        +
      </button>
    </div>
  {/each}
  <div class="confirm">
    <button class="button" disabled={!maxed} onclick={confirm}>Confirm</button>
  </div>
</div>

<style lang="scss">
  .chooser {
    display: flex;
    flex-direction: row;
    justify-content: center;
  }
  .confirm {
    display: flex;
    flex-direction: column;
    justify-content: center;
  }
</style>
