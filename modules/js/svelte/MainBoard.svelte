<script lang="ts">
  import type { AnarchyContext } from "../context.svelte";
  import ScenarioCard from "./cards/ScenarioCard.svelte";
  const { ctx }: { ctx: AnarchyContext } = $props();
  const { round, scenario } = $derived(ctx.data!);
</script>

<div class="main-board">
  <div class="scenario-cards">
    {#each Object.keys(scenario.cards) as scenarioId, idx (scenarioId)}
      <ScenarioCard
        face={round >= idx + 1 ? "front" : "back"}
        mode={scenario.mode}
        num={scenarioId}
        zoom={0.25}
      />
    {/each}
  </div>
</div>

<style lang="scss">
  .main-board {
    position: relative;
    background-size: cover;
    width: 786px;
    height: 218px;
    align-self: center;
    background-image: url("img/main_board.webp");
  }

  .scenario-cards {
    display: flex;
    flex-direction: row;
    gap: 10.4px;
    position: relative;
    left: 71.4px;
    top: 54.8px;
  }
</style>
