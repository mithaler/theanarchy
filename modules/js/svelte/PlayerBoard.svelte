<script lang="ts">
  import type { AnarchyContext } from "../context.svelte";
  import PlayerSheets from "./PlayerSheets.svelte";

  interface Props {
    ctx: AnarchyContext;
    playerId: number;
    isMe: boolean;
  }
  const { playerId, ctx, isMe }: Props = $props();
</script>

<PlayerSheets {playerId} {ctx} {isMe} />
