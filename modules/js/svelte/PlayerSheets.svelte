<script lang="ts">
  import type { AnarchyContext } from "../context.svelte";
  import LeftSheet from "./LeftSheet.svelte";
  import RightSheet from "./RightSheet.svelte";

  interface Props {
    ctx: AnarchyContext;
    playerId: number;
    isMe: boolean;
  }
  const props: Props = $props();
</script>

<div class="anarchy-player-sheets">
  <LeftSheet {...props} />
  <RightSheet {...props} />
</div>

<style lang="scss">
  .anarchy-player-sheets {
    display: flex;
    flex-wrap: wrap;

    :global(.anarchy-sheet) {
      width: 800px;
      height: 800px;
    }

    :global(.row-label) {
      display: inline-block;
      width: 10em;
    }
  }
</style>
