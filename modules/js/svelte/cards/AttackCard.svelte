<script lang="ts">
  interface Props {
    id: number;
    face: "front" | "back";
    zoom?: number;
  }
  const { id, face, zoom }: Props = $props();
  const klass = $derived(face === "front" ? "attack" : "attack-back");
</script>

<div class={["card", klass, `${klass}-${id}`]} style:zoom></div>

<style lang="scss">
  .card {
    background-repeat: no-repeat;
    display: inline-block;
    width: 305px;
    height: 475px;
    border-radius: 12px;
    box-shadow: 3px 3px 3px #000000a0;
  }

  .attack {
    background-image: url("img/attack_cards.webp");
  }

  .attack-1 {
    background-position: -306px -2380px;
  }

  .attack-2 {
    background-position: -0px -0px;
  }

  .attack-3 {
    background-position: -306px -0px;
  }

  .attack-4 {
    background-position: -612px -0px;
  }

  .attack-5 {
    background-position: -918px -0px;
  }

  .attack-6 {
    background-position: -1224px -0px;
  }

  .attack-7 {
    background-position: -1530px -0px;
  }

  .attack-8 {
    background-position: -1836px -0px;
  }

  .attack-9 {
    background-position: -2142px -0px;
  }

  .attack-10 {
    background-position: -0px -476px;
  }

  .attack-11 {
    background-position: -306px -476px;
  }

  .attack-12 {
    background-position: -612px -476px;
  }

  .attack-13 {
    background-position: -918px -476px;
  }

  .attack-14 {
    background-position: -1224px -476px;
  }

  .attack-15 {
    background-position: -1530px -476px;
  }

  .attack-16 {
    background-position: -1836px -476px;
  }

  .attack-17 {
    background-position: -2142px -476px;
  }

  .attack-18 {
    background-position: -0px -952px;
  }

  .attack-19 {
    background-position: -306px -952px;
  }

  .attack-20 {
    background-position: -612px -952px;
  }

  .attack-21 {
    background-position: -918px -952px;
  }

  .attack-22 {
    background-position: -1224px -952px;
  }

  .attack-23 {
    background-position: -1530px -952px;
  }

  .attack-24 {
    background-position: -1836px -952px;
  }

  .attack-25 {
    background-position: -2142px -952px;
  }

  .attack-26 {
    background-position: -0px -1428px;
  }

  .attack-27 {
    background-position: -306px -1428px;
  }

  .attack-28 {
    background-position: -612px -1428px;
  }

  .attack-29 {
    background-position: -918px -1428px;
  }

  .attack-30 {
    background-position: -1224px -1428px;
  }

  .attack-31 {
    background-position: -1530px -1428px;
  }

  .attack-32 {
    background-position: -1836px -1428px;
  }

  .attack-33 {
    background-position: -2142px -1428px;
  }

  .attack-34 {
    background-position: -0px -1904px;
  }

  .attack-35 {
    background-position: -306px -1904px;
  }

  .attack-36 {
    background-position: -612px -1904px;
  }

  .attack-37 {
    background-position: -918px -1904px;
  }

  .attack-38 {
    background-position: -1224px -1904px;
  }

  .attack-39 {
    background-position: -1530px -1904px;
  }

  .attack-41 {
    background-position: -1836px -1904px;
  }

  .attack-40 {
    background-position: -2142px -1904px;
  }

  .attack-42 {
    background-position: -0px -2380px;
  }

  // Backs: include only distinct ones (since that's all the backend gives us)
  .attack-back {
    background-image: url("img/attack_card_backs.webp");
  }

  .attack-back-1 {
    background-position: -0px -0px;
  }

  .attack-back-7 {
    background-position: -306px -0px;
  }

  .attack-back-13 {
    background-position: -612px -0px;
  }

  .attack-back-19 {
    background-position: -0px -476px;
  }

  .attack-back-25 {
    background-position: -306px -476px;
  }

  .attack-back-31 {
    background-position: -612px -476px;
  }

  .attack-back-37 {
    background-position: -0px -952px;
  }
</style>
