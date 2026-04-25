<script lang="ts">
  import LeftSheet from "./LeftSheet.svelte";

  interface Props {
    playerId: number;
  }
  const playerId: number = $props();
</script>

<LeftSheet {playerId} />
