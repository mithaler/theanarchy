<script lang="ts">
  import type { AnarchyContext } from "../context.svelte";
  import LeftSheet from "./LeftSheet.svelte";

  interface Props {
    ctx: AnarchyContext;
    playerId: number;
  }
  const { playerId, ctx }: Props = $props();
</script>

<LeftSheet {playerId} {ctx} />
