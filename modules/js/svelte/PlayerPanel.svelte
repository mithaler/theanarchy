<script lang="ts">
  import type { AnarchyPlayer } from "../context.svelte";

  interface Props {
    player: AnarchyPlayer;
  }

  let { player }: Props = $props();
</script>

I am player {player.id}, I have {player.serfs} serfs
