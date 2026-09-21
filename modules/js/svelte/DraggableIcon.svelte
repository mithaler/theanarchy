<script module lang="ts">
  import type { Fighter } from "./CastleWorkers.svelte";
  import Icon from "./Icon.svelte";
  export const dragState: { dragging?: Fighter; id?: number } = $state({});
</script>

<script lang="ts">
  const { name, id }: { name: Fighter; id?: number } = $props();

  function ondragstart(field: Fighter) {
    return () => {
      dragState.dragging = field;
      dragState.id = id;
    };
  }

  function ondragend() {
    dragState.dragging = undefined;
    dragState.id = undefined;
  }
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
