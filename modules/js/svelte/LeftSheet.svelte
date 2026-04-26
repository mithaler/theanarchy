<script lang="ts">
  import ResourceRow from "./ResourceRow.svelte";

  interface Props {
    playerId: number;
  }
  const { playerId }: Props = $props();
</script>

<div>
  Player {playerId}
  <ResourceRow {playerId} section={_("QUARRY & FOREST")} />
  <ResourceRow {playerId} section={_("FARMS")} />
  <ResourceRow {playerId} section={_("TRAINING GROUNDS")} />
</div>
