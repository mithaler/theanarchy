<script lang="ts">
  import type { AnarchyContext } from "../context.svelte";
  import PlayerSheets from "./PlayerSheets.svelte";

  interface Props {
    ctx: AnarchyContext;
    playerId: number;
  }
  const { playerId, ctx }: Props = $props();
</script>

<PlayerSheets {playerId} {ctx} />
