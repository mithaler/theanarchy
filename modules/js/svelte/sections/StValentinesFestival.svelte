<script lang="ts">
  import Checkbox, { getState } from "../Checkbox.svelte";
  import { type SectionProps } from "./utils.svelte";
  const { availableBoxes, checkedBoxes, isMe }: SectionProps = $props();
</script>

{#snippet box(id: number)}
  <div class={["box-wrapper", `box-wrapper-${id}`]}>
    <Checkbox
      section="ST VALENTINES FESTIVAL"
      boxId={id}
      state={getState(id, isMe, checkedBoxes, availableBoxes)}
    />
  </div>
{/snippet}

<div class="stvalentinesfestival">
  <!-- Clickable boxes -->
  <div class="festival-row activation">
    <!-- Three columns of 2 -->
    {#each Array.from({ length: 3 }, (_, i) => i) as col (col)}
      <div class="activation-column">
        {@render box(col * 2 + 1)}
        {@render box(col * 2 + 2)}
      </div>
    {/each}
  </div>

  <!-- Gender-pair boxes -->
  <div class="festival-row gender">
    {#each Array.from({ length: 12 }, (_, i) => 6 + i + 1) as id (id)}
      {@render box(id)}
    {/each}
  </div>

  <!-- Reward boxes -->
  <div class="festival-row reward">
    {#each Array.from({ length: 6 }, (_, i) => 18 + i + 1) as id (id)}
      {@render box(id)}
    {/each}
  </div>
</div>

<style lang="scss">
  .stvalentinesfestival {
    display: flex;
    flex-direction: column;
    position: absolute;
    left: 505px;
    top: 330px;
  }

  .festival-row {
    display: flex;
    flex-direction: row;
    width: 268.5px;

    .box-wrapper {
      margin-right: 2px;
    }
  }

  .festival-row.activation {
    justify-content: flex-end;
    gap: 4px;
    margin-bottom: 7px;

    .activation-column {
      display: flex;
      flex-direction: column;
      margin-left: 68px;
      gap: 4px;
    }
  }

  .festival-row.gender {
    margin-bottom: 17px;

    .box-wrapper:nth-child(even) {
      margin-right: 6.5px;
    }
  }

  .festival-row.reward {
    .box-wrapper:nth-child(odd) :global(.checkbox) {
      width: 33px;
    }
    .box-wrapper:nth-child(even) :global(.checkbox) {
      width: 51px;
    }
  }
</style>
