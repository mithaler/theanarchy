<script lang="ts">
  import { slide } from "svelte/transition";
  import { getBga, type AnarchyPlayer } from "../context.svelte";
  import Castle from "./Castle.svelte";

  const { me }: { me: AnarchyPlayer } = $props();

  const bga = getBga();
  const state = $derived(bga.states.getCurrentPlayerStateName());
  const visible = $derived(
    me && ["Muster", "UseTactics", "AllocateDefenders"].includes(state),
  );
</script>

{#if visible}
  <div class="attack-handler" transition:slide>
    <div class="attack-handler-castle">
      <Castle mode="attack-handler" player={me} isMe />
    </div>
  </div>
{/if}

<style lang="scss">
  .attack-handler {
    width: 100%;
    display: flex;
    justify-content: center;
  }

  .attack-handler-castle {
    width: 500px;
  }
</style>
