<script module lang="ts">
  interface PathCardEntry {
    name: string;
    description: string;
    conditions: string[];
  }
  const PATH_CARDS: Record<string, PathCardEntry> = {
    abbot: {
      name: _("Abbot"),
      description: _("Completed chapel levels"),
      conditions: ["1", "2", "3"],
    },
    advocate: {
      name: _("Advocate"),
      description: _("Final discontent"),
      conditions: ["5-6", "3-4", "0-2"],
    },
    architect: {
      name: _("Architect"),
      description: _("Completed keep levels"),
      conditions: ["1", "2", "3"],
    },
    baron: {
      name: _("Baron"),
      description: _("Completed leadership rows"),
      conditions: ["1", "2", "3"],
    },
    bellwether: {
      name: _("Bellwether"),
      description: _("Final influence"),
      conditions: ["10", "15", "20"],
    },
    captain: {
      name: _("Captain"),
      description: _("Muster tokens"),
      conditions: ["1", "2", "3"],
    },
    champion: {
      name: _("Champion"),
      description: _("Tournaments won"),
      conditions: ["2", "3", "4"],
    },
    commander: {
      name: _("Commander"),
      description: _("Final might"),
      conditions: ["10", "15", "20"],
    },
    conqueror: {
      name: _("Conqueror"),
      description: _("Castles defeated"),
      conditions: ["2", "3", "4"],
    },
    defender: {
      name: _("Defender"),
      description: _("Constructed walls"),
      conditions: ["6", "11", "16"],
    },
    emissary: {
      name: _("Emissary"),
      description: _("Assisted others with spies"),
      conditions: ["1", "2", "3"],
    },
    engineer: {
      name: _("Engineer"),
      description: _("Constructed towers / gates"),
      conditions: ["4/2", "6/4", "8/6"],
    },
    excavator: {
      name: _("Excavator"),
      description: _("Constructed moat"),
      conditions: ["2", "4", "6"],
    },
    foreman: {
      name: _("Foreman"),
      description: _("Completed Quarry & Forest, Farms, and Training Grounds"),
      conditions: ["1", "2", "3"],
    },
    mentor: {
      name: _("Mentor"),
      description: _("Completed Knight's Training"),
      conditions: ["2", "4", "6"],
    },
    merrymaker: {
      name: _("Merrymaker"),
      description: _("Filled Lammas flags"),
      conditions: ["6", "8", "10"],
    },
    monk: {
      name: _("Monk"),
      description: _("Completed sums in Michaelmas"),
      conditions: ["2", "3", "4"],
    },
    partisan: {
      name: _("Partisan"),
      description: _("Final loyalty"),
      conditions: ["10", "15", "20"],
    },
    priest: {
      name: _("Priest"),
      description: _("St. Valentine's Festival pairs"),
      conditions: ["2", "3", "4"],
    },
    recruiter: {
      name: _("Recruiter"),
      description: _("Completed Guildsmen, Mercenaries, and Allies"),
      conditions: ["1", "2", "3"],
    },
    scout: {
      name: _("Scout"),
      description: _("Spies gained"),
      conditions: ["1", "2", "3"],
    },
    silversmith: {
      name: _("Silversmith"),
      description: _("Completed mint levels"),
      conditions: ["1", "2", "3"],
    },
    tactician: {
      name: _("Tactician"),
      description: _("Researched Tactics"),
      conditions: ["2", "3", "4"],
    },
    warrior: {
      name: _("Warrior"),
      description: _("Final bravery"),
      conditions: ["10", "15", "20"],
    },
    weaponsmith: {
      name: _("Weaponsmith"),
      description: _("Constructed siege weapons"),
      conditions: ["2", "4", "6"],
    },
  };
</script>

<script lang="ts">
  interface Props {
    id: string;
    onclick?: (id: string) => void;
    zoom?: number;
  }
  const { id, onclick, zoom }: Props = $props();
  const card = $derived(PATH_CARDS[id]);
</script>

<button
  class={["goal", `goal-${id}`, onclick && "clickable"]}
  onclick={onclick ? () => onclick(id) : undefined}
  style:zoom
>
  <div class="name blackletter">{card?.name ?? id}</div>
  <div class="description game-text">{card?.description ?? ""}</div>
  <div class="conditions">
    <div class="condition-1 game-text">{card.conditions[0]} = 1VP</div>
    <div class="condition-2 game-text">{card.conditions[1]} = 2VP</div>
    <div class="condition-3 game-text">{card.conditions[2]} = 3VP</div>
  </div>
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

  .description {
    text-align: center;
    position: absolute;
    bottom: 56px;
    margin: 0px 17px;
    font-size: 2.2em;
    height: 65px;
    width: 350px;
    line-height: 24px;
    align-content: center;
  }

  .conditions {
    position: absolute;
    bottom: 20px;
    display: flex;
    flex-direction: row;
    justify-content: space-evenly;
    font-size: 2.2em;
    width: 350px;
    margin: 0 16px;
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
