<script lang="ts">
  import { ids } from "./utils.svelte";

  interface Props {
    discontent: number[] | null;
    joy: number[] | null;
  }
  const { discontent, joy }: Props = $props();

  function max(nums: number[] | null) {
    return nums && nums.length > 0 ? Math.max(...nums) : 0;
  }
  const maxDiscontent = $derived(max(discontent));
  const maxJoy = $derived(max(joy));
</script>

<div class="discontent-row">
  {#each ids(18) as id (id)}
    <div
      class={[
        "bubble",
        id <= maxDiscontent && "discontent",
        id <= maxJoy && "joy",
      ]}
    ></div>
  {/each}
</div>

<style lang="scss">
  .discontent-row {
    position: absolute;
    bottom: 50px;
    left: 20px;
    display: flex;
    flex-direction: row;
  }

  .bubble {
    width: 15px;
    height: 15px;
  }

  .discontent {
    background-color: black;
    border-radius: 50%;
  }

  .joy {
    border-radius: 50%;
    border: 3px solid red;
  }
</style>
