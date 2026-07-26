<script lang="ts">
  import type { Snippet } from "svelte";
  import { getBga } from "../context.svelte";
  import type { Attachment } from "svelte/attachments";

  interface Props {
    children: Snippet;
  }
  const { children }: Props = $props();
  const id = $props.id();
  const bga = getBga();

  const tooltip: Attachment = (e) => {
    bga.gameui.addTooltip(e.id, _("HELLO"), _("GOODBYE"));
    return () => bga.gameui.removeTooltip(e.id);
  };
</script>

<div {id} {@attach tooltip}>
  {@render children()}
</div>
