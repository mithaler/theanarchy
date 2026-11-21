<script lang="ts">
  interface Props {
    face: "front" | "back";
    mode: "siege" | "classic";
    num: number;
    zoom?: number;
  }
  const { face, mode, num, zoom }: Props = $props();
</script>

<div
  class={["scenario", "card", `${mode}-${face}`, `${mode}-${face}${num}`]}
  style:zoom
></div>

<style lang="scss">
  .card {
    width: 383px;
    height: 610px;
  }

  .classic-front {
    background-image: url("img/classic_scenario_front-.webp");
  }

  .classic-front-1 {
    background-position: -1px -1px;
  }

  .classic-front-2 {
    background-position: -386px -1px;
  }

  .classic-front-4 {
    background-position: -771px -1px;
  }

  .classic-front-3 {
    background-position: -1156px -1px;
  }

  .classic-front-5 {
    background-position: -1px -613px;
  }

  .classic-front-7 {
    background-position: -386px -613px;
  }

  .classic-front-6 {
    background-position: -771px -613px;
  }

  .classic-front-8 {
    background-position: -1156px -613px;
  }

  .classic-front-10 {
    background-position: -1541px -1px;
  }

  .classic-front-9 {
    background-position: -1541px -613px;
  }

  .classic-front-11 {
    background-position: -1px -1225px;
  }

  .classic-front-12 {
    background-position: -386px -1225px;
  }

  .classic-front-13 {
    background-position: -771px -1225px;
  }

  .classic-front-14 {
    background-position: -1156px -1225px;
  }

  .classic-front-15 {
    background-position: -1541px -1225px;
  }
</style>
