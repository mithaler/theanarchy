<script lang="ts">
  import { ctx } from "../context";
  let count = $state(0);
</script>

<h1 class="hello">Hello {$ctx.test}!</h1>

<input bind:value={$ctx.test} />
<button onclick={() => (count += 1)}>
  clicks: {count}
</button>
