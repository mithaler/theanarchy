<script lang="ts">
  import type { AnarchyPlayer } from "../context";
  import { ctx } from "../context";

  interface Props {
    playerId: string;
  }

  let { playerId }: Props = $props();
  let player: AnarchyPlayer = $derived($ctx.players[playerId]);
</script>

I am player {player.id}, I have {player.serfs} serfs
