<script lang="ts">
  interface Props {
    id: number;
    player: 1 | 2 | 3 | 4;
    face: "front" | "back";
  }
  const { id, player, face }: Props = $props();
</script>

<div
  class={[
    "player-card",
    face === "front" ? `player-card-front-${id}` : `player-card-back-${player}`,
  ]}
></div>

<style lang="scss">
  .player-card {
    background-image: url("img/player_cards.webp");
    background-repeat: no-repeat;
    display: inline-block;
    width: 305px;
    height: 475px;
  }

  .player-card-back-1 {
    background-position: -306px -0px;
  }

  .player-card-back-2 {
    background-position: -612px -0px;
  }

  .player-card-back-3 {
    background-position: -918px -0px;
  }

  .player-card-back-4 {
    background-position: -0px -0px;
  }

  .player-card-front-1 {
    background-position: -1224px -0px;
  }

  .player-card-front-2 {
    background-position: -1530px -0px;
  }

  .player-card-front-3 {
    background-position: -1836px -0px;
  }

  .player-card-front-4 {
    background-position: -0px -476px;
  }

  .player-card-front-5 {
    background-position: -306px -476px;
  }

  .player-card-front-6 {
    background-position: -612px -476px;
  }

  .player-card-front-7 {
    background-position: -918px -476px;
  }

  .player-card-front-8 {
    background-position: -1224px -476px;
  }

  .player-card-front-9 {
    background-position: -1530px -476px;
  }

  .player-card-front-10 {
    background-position: -1836px -476px;
  }

  .player-card-front-11 {
    background-position: -0px -952px;
  }

  .player-card-front-12 {
    background-position: -306px -952px;
  }

  .player-card-front-13 {
    background-position: -612px -952px;
  }

  .player-card-front-14 {
    background-position: -918px -952px;
  }

  .player-card-front-15 {
    background-position: -1224px -952px;
  }

  .player-card-front-16 {
    background-position: -1530px -952px;
  }

  .player-card-front-17 {
    background-position: -1836px -952px;
  }

  .player-card-front-18 {
    background-position: -0px -1428px;
  }

  .player-card-front-19 {
    background-position: -306px -1428px;
  }

  .player-card-front-20 {
    background-position: -612px -1428px;
  }

  .player-card-front-21 {
    background-position: -918px -1428px;
  }

  .player-card-front-23 {
    background-position: -1224px -1428px;
  }

  .player-card-front-22 {
    background-position: -1530px -1428px;
  }

  .player-card-front-24 {
    background-position: -1836px -1428px;
  }
</style>
