<script lang="ts">
  import LeftSheet from "./LeftSheet.svelte";
  import RightSheet from "./RightSheet.svelte";

  interface Props {
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
      position: relative; // needed to ensure this is a containing block for boxes
      height: 800px;
    }

    :global(.row-label) {
      display: inline-block;
      width: 10em;
    }
  }
</style>
