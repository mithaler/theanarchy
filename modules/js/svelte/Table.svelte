<script lang="ts">
  import { ctx } from "../context";
  let count = $state(0);
</script>

<p>It is round {$ctx.round}</p>

<button onclick={() => (count += 1)}>
  clicks: {count}
</button>
