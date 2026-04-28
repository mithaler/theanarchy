<script lang="ts">
  import { ctx } from "../context";
  import { CheckBoxes } from "../states";
  import Checkbox from "./Checkbox.svelte";

  interface Props {
    playerId: number;
    section: string;
  }
  const { playerId, section }: Props = $props();
  const playerData = $derived($ctx.data.players[playerId]);
  const lastCheckedBoxId = $derived.by(() => {
    const sectionData: number[] = playerData.checkedBoxes[section];
    return sectionData ? Math.max(...sectionData) : 0;
  });

  const {
    checkableId,
    onCheck,
  }: { checkableId: number | null; onCheck?: () => void } = $derived.by(() => {
    const state = $ctx.bga.states.getCurrentMainStateClass();
    if (state instanceof CheckBoxes && playerData.availableBoxes) {
      const availableSectionBoxes = playerData.availableBoxes[section] ?? [];
      const checkableId =
        availableSectionBoxes.length > 0 ? availableSectionBoxes[0] : null;
      return {
        checkableId,
        onCheck: () => {
          state.checkBox(section, checkableId);
        },
      };
    }
    return { checkableId: null };
  });
</script>

<div id={section}>
  {#each Array.from({ length: 13 }, (_, i) => i + 1) as id}
    <Checkbox
      {section}
      boxId={id}
      state={id <= lastCheckedBoxId
        ? "checked"
        : playerId === $ctx.bga.players.getCurrentPlayerId() &&
            checkableId === id
          ? "available"
          : "unavailable"}
      {onCheck}
    />
  {/each}
</div>
