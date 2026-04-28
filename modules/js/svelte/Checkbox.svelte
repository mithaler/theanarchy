<script lang="ts">
  type State = "checked" | "available" | "unavailable";
  interface Props {
    section: string;
    boxId: number;
    state: State;
    onCheck?: () => void;
  }
  const { section, boxId, state, onCheck }: Props = $props();
</script>

<input
  id="box-{section}-{boxId}"
  type="checkbox"
  checked={state === "checked"}
  disabled={state === "unavailable"}
  onclick={onCheck}
/>
