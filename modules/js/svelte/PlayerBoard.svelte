<script lang="ts">
  import PlayerSheets from "./PlayerSheets.svelte";

  interface Props {
    playerId: number;
  }
  const { playerId }: Props = $props();
</script>

<PlayerSheets {playerId} />
