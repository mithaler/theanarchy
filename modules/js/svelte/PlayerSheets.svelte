<script lang="ts">
  import LeftSheet from "./LeftSheet.svelte";

  interface Props {
    playerId: number;
  }
  const { playerId }: Props = $props();
</script>

<LeftSheet {playerId} />
