<script lang="ts">
  import type { AnarchyContext } from "../context.svelte";
  import ResourceRow from "./ResourceRow.svelte";

  interface Props {
    ctx: AnarchyContext;
    playerId: number;
    isMe: boolean;
  }
  const { playerId, ctx, isMe }: Props = $props();

  const getCheckedBoxes = (section: string) => {
    return ctx.data!.players[playerId].checkedBoxes[section] ?? [];
  };
  const getAvailableBoxes = (section: string) => {
    if (!isMe) {
      return null;
    }

    const allAvailBoxes = ctx.data!.players[playerId].availableBoxes;
    if (allAvailBoxes) {
      return allAvailBoxes[section];
    }
    return null;
  };
</script>

{#snippet resourceRow(section: string)}
  <ResourceRow
    bga={ctx.bga!}
    {playerId}
    {section}
    checkedBoxes={getCheckedBoxes(section)}
    availableBoxes={getAvailableBoxes(section)}
  />
{/snippet}

<div>
  Player {playerId}
  <!-- TODO figure out how to make these translated -->
  {@render resourceRow("QUARRY & FOREST")}
  {@render resourceRow("FARMS")}
  {@render resourceRow("TRAINING GROUNDS")}
</div>
