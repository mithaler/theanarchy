<script lang="ts">
  const { name, zoom }: { name: string; zoom?: number } = $props();
</script>

<div class={["icon", name]} style:zoom></div>

<style lang="scss">
  .icon {
    background-image: url("img/icons.webp");
    background-repeat: no-repeat;
    display: inline-block;
    zoom: 0.3;
  }

  .serfs {
    width: 53px;
    height: 70px;
    background-position: -360px -6px;
  }
  .craftsmen {
    width: 53px;
    height: 70px;
    background-position: -192px -6px;
  }
  .soldiers {
    width: 53px;
    height: 70px;
    background-position: -109px -6px;
  }
  .knights {
    width: 53px;
    height: 70px;
    background-position: -277px -6px;
  }
  .patrons {
    width: 53px;
    height: 70px;
    background-position: -20px -6px;
  }
  .food {
    width: 56px;
    height: 70px;
    background-position: -776px -175px;
  }
  .materials {
    width: 71px;
    height: 59px;
    background-position: -1380px -14px;
  }
  .silver {
    width: 70px;
    height: 71px;
    background-position: -1333px -96px;
  }
</style>
