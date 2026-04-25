<script lang="ts">
  import { ctx } from "../context";

  interface Props {
    playerId: number;
    section: string;
  }
  const { playerId, section }: Props = $props();
  const boxId = $derived.by(() => {
    const sectionData: number[] = $ctx.players[playerId]?[section];
    if (section) {
      return sectionData.reduce((acc, curr) => {
        if (curr > acc) {
          return curr;
        }
        return acc;
      }, 0);
    }
    return 0;
  })
</script>

<div id={section}>
  {#each Array.from({length: 13}, (_, i) => i + 1) as id}
    <input type="checkbox" disabled={id > boxId}/>
  {/each}
</div>