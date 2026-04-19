<script lang="ts">
  import { ctx } from "../context";
  let count = $state(0);
</script>

<p>It is round {$ctx.round}</p>

<input bind:value={$ctx.test} />
<button onclick={() => (count += 1)}>
  clicks: {count}
</button>
