<script lang="ts">
  import { ctx } from "../context";
  import { CheckBoxes } from "../states";

  interface Props {
    playerId: number;
    section: string;
  }
  const { playerId, section }: Props = $props();
  const playerData = $derived($ctx.data.players[playerId]);
  const lastCheckedBoxId = $derived.by(() => {
    const sectionData: number[] = playerData.checkedBoxes[section];
    if (sectionData) {
      return sectionData.reduce((acc, curr) => {
        if (curr > acc) {
          return curr;
        }
        return acc;
      }, 0);
    }
    return 0;
  });

  const validBox: number | null = $derived.by(() => {
    const state = $ctx.bga.states.getCurrentMainStateClass();
    if (state instanceof CheckBoxes && playerData.availableBoxes) {
      const availableSectionBoxes = playerData.availableBoxes[section] ?? [];
      return availableSectionBoxes.length > 0 ? availableSectionBoxes[0] : null;
    }
    return null;
  });
  $inspect(section, validBox);
</script>

<div id={section}>
  {#each Array.from({ length: 13 }, (_, i) => i + 1) as id}
    <input
      type="checkbox"
      checked={id < lastCheckedBoxId}
      disabled={!(validBox == id) && id > lastCheckedBoxId}
    />
  {/each}
</div>
