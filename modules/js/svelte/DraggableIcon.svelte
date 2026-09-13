<script module lang="ts">
  import type { Fighter } from "./CastleWorkers.svelte";
  import Icon from "./Icon.svelte";
  export const dragState: { dragging?: Fighter } = $state({});
</script>

<script lang="ts">
  function ondragstart(field: Fighter) {
    return () => {
      dragState.dragging = field;
    };
  }

  function ondragend() {
    dragState.dragging = undefined;
  }

  const { name }: { name: Fighter } = $props();
</script>

<div
  class="draggable"
  draggable="true"
  ondragstart={ondragstart(name)}
  {ondragend}
  role="img"
>
  <Icon {name} />
</div>

<style lang="scss">
  .draggable {
    cursor: grab;
  }
</style>
