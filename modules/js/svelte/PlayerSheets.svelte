<script lang="ts">
  import type { AnarchyContext } from "../context.svelte";
  import LeftSheet from "./LeftSheet.svelte";

  interface Props {
    ctx: AnarchyContext;
    playerId: number;
    isMe: boolean;
  }
  const { playerId, ctx, isMe }: Props = $props();
</script>

<LeftSheet {playerId} {ctx} {isMe} />
