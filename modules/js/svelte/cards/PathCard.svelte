<script module lang="ts">
  interface PathCardEntry {
    name: string;
    description?: string;
    conditions?: number[];
  }
  const PATH_CARDS: Record<string, PathCardEntry> = {
    // TODO fill
    abbot: { name: _("Abbot") },
    bellwether: { name: _("Bellwether") },
    champion: { name: _("Champion") },
    conqueror: { name: _("Conqueror") },
    defender: { name: _("Defender") },
    engineer: { name: _("Engineer") },
    mentor: { name: _("Mentor") },
    merrymaker: { name: _("Merrymaker") },
    monk: { name: _("Monk") },
    priest: { name: _("Priest") },
    scout: { name: _("Scout") },
    weaponsmith: { name: _("Weaponsmith") },
  };
</script>

<script lang="ts">
  interface Props {
    id: string;
    onclick?: (id: string) => void;
    zoom?: number;
  }
  const { id, onclick, zoom }: Props = $props();
</script>

<button
  class={["goal", `goal-${id}`, onclick && "clickable"]}
  onclick={onclick ? () => onclick(id) : undefined}
  style:zoom
>
  <div class="name blackletter">{PATH_CARDS[id]?.name ?? id}</div>
</button>

<style lang="scss">
  .goal {
    background-image: url("img/goal_cards.webp");
    background-repeat: no-repeat;
    display: inline-block;
    width: 383px;
    height: 610px;
    border-radius: 12px;
    box-shadow: 3px 3px 3px #000000a0;
    zoom: 0.4;
  }

  .name {
    font-size: 4.5em;
    text-align: center;
    position: relative;
    top: 158px;
  }

  .clickable {
    cursor: pointer;
  }

  .goal-abbot {
    background-position: -0px -0px;
  }

  .goal-advocate {
    background-position: -384px -0px;
  }

  .goal-architect {
    background-position: -768px -0px;
  }

  .goal-baron {
    background-position: -1152px -0px;
  }

  .goal-bellwether {
    background-position: -1536px -0px;
  }

  .goal-captain {
    background-position: -1920px -0px;
  }

  .goal-champion {
    background-position: -0px -611px;
  }

  .goal-commander {
    background-position: -384px -611px;
  }

  .goal-conqueror {
    background-position: -768px -611px;
  }

  .goal-defender {
    background-position: -1152px -611px;
  }

  .goal-emissary {
    background-position: -1536px -611px;
  }

  .goal-engineer {
    background-position: -1920px -611px;
  }

  .goal-excavator {
    background-position: -0px -1222px;
  }

  .goal-foreman {
    background-position: -384px -1222px;
  }

  .goal-mentor {
    background-position: -768px -1222px;
  }

  .goal-merrymaker {
    background-position: -1152px -1222px;
  }

  .goal-monk {
    background-position: -1536px -1222px;
  }

  .goal-partisan {
    background-position: -1920px -1222px;
  }

  .goal-priest {
    background-position: -0px -1833px;
  }

  .goal-recruiter {
    background-position: -384px -1833px;
  }

  .goal-scout {
    background-position: -768px -1833px;
  }

  .goal-silversmith {
    background-position: -1152px -1833px;
  }

  .goal-tactician {
    background-position: -1536px -1833px;
  }

  .goal-warrior {
    background-position: -1920px -1833px;
  }

  .goal-weaponsmith {
    background-position: -0px -2444px;
  }
</style>
