<script module lang="ts">
  export type Fighter = "soldiers" | "craftsmen" | "knights";
</script>

<script lang="ts">
  import { zone } from "../Game.svelte";
  import type { PlayerBoardProps } from "./PlayerArea.svelte";
  import { dragState } from "./PlayerPanel.svelte";
  import { getBga } from "../context.svelte";
  import { mount } from "svelte";
  import Icon from "./Icon.svelte";

  interface Props extends PlayerBoardProps {
    position: "top" | "left" | "right" | "bottom";
  }
  const { player, isMe, position }: Props = $props();
  const id = $props.id();

  function vertical(): boolean {
    return position === "left" || position === "right";
  }

  // not state! not reactive!
  const iconZone = new zone();

  function createZone(e: HTMLElement) {
    iconZone.create(getBga().gameui, e.id, 15.9, 21);
    iconZone.setPattern("horizontalfit");
  }

  const weights: Record<Fighter, number> = {
    craftsmen: 2,
    soldiers: 1,
    knights: 0,
  };

  let icons: HTMLElement;
  let counter = 0;

  /**
   * Cursed hack: mount actual HTML for these icons, so we can pass it to
   * the BGA framework's Zone component. It grabs them out of the hidden div
   * below.
   */
  function makeIcon(field: string): string {
    const iconId = `${id}-${field}-${counter++}`;
    const elem = document.createElement("div");
    elem.id = iconId;

    // Svelte doesn't like that we insert it manually here
    // eslint-disable-next-line svelte/no-dom-manipulating
    icons.appendChild(elem);

    mount(Icon, { target: elem, props: { name: field } });
    return iconId;
  }

  function ondrop() {
    console.log(`DROPPING ${dragState.dragging}`);
    const icon = makeIcon(dragState.dragging!);
    console.log(icon);
    iconZone.placeInZone(icon, weights[dragState.dragging!]);
  }
</script>

<div style:display="none" id="{id}-icons" bind:this={icons}></div>

<div
  id="{id}-castle-defenders-{position}"
  class={[
    "castle-workers",
    position,
    { vertical: vertical(), dragging: isMe && !!dragState.dragging },
  ]}
  ondrop={isMe ? ondrop : undefined}
  ondragover={isMe ? (e) => e.preventDefault() : undefined}
  role="list"
  {@attach createZone}
></div>

<style lang="scss">
  .castle-workers {
    position: absolute;
    width: 176px;
    height: 30px;
    border: 1px solid yellow;
    display: flex;

    &.top {
      top: 102px;
    }
    &.bottom {
      top: 315px;
    }
    &.left {
      left: -75px;
    }
    &.right {
      right: -77px;
    }
  }

  .dragging {
    border: 3px solid red;
  }

  .vertical {
    rotate: 90deg;
  }
</style>
