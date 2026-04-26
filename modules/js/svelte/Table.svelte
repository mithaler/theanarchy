<script lang="ts">
  import { ctx } from "../context";
  import PlayerBoard from "./PlayerBoard.svelte";

  const playerIds = Object.keys($ctx.data.players).map((pid) =>
    parseInt(pid, 10),
  );
  $inspect(playerIds);
</script>

<p>It is round {$ctx.data.round}</p>

{#each playerIds as playerId}
  <PlayerBoard {playerId} />
{/each}
