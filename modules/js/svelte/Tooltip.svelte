<script lang="ts">
  import type { Snippet } from "svelte";
  import { getBga } from "../context.svelte";
  import type { Attachment } from "svelte/attachments";

  interface Props {
    children: Snippet;
    help?: string;
    action?: string;
    html?: string;
  }
  const { children, help, action, html }: Props = $props();
  const id = $props.id();
  const bga = getBga();

  const tooltip: Attachment = (e) => {
    if (html) {
      bga.gameui.addTooltipHtml(e.id, html);
    } else {
      bga.gameui.addTooltip(e.id, help!, action!);
    }
    return () => bga.gameui.removeTooltip(e.id);
  };
</script>

<div {id} {@attach tooltip}>
  {@render children()}
</div>
