<script lang="ts">
  interface Props {
    zoom: number;
  }
  let { zoom = $bindable() }: Props = $props();
</script>

<div class="zoomer">
  <button onclick={() => (zoom *= 1.1)} title="Zoom In">
    <!-- SVGs shamelessly snarfed from other BGA games (and simplified )-->
    <svg viewBox="0 0 70 70" class="zoom-svg">
      <g transform="matrix(1,0,0,1,-7.91703,-76.2244)" class="stroke">
        <path d="M23.8,102.1L35.7,102.1" />
        <path d="M35.7,102.1L47.5,102.1" />
        <path d="M35.7,102.1L35.7,90.3" />
        <path
          d="M51,118C55.2,113.8 57.3,108.7 57.3,102.8C57.3,96.8 55.2,91.8 51,87.6C46.8,83.4 41.7,81.3 35.8,81.3C29.9,81.3 24.8,83.4 20.6,87.6C16.4,91.8 14.3,96.8 14.3,102.8C14.3,108.7 16.4,113.8 20.6,118C24.8,122.2 29.9,124.3 35.8,124.3C41.7,124.3 46.8,122.2 51,118"
        />
        <path d="M35.7,113.9L35.7,102.1" />
        <path d="M65.3,132.3L51,118" />
      </g>
    </svg>
  </button>
  <button onclick={() => (zoom *= 0.9)} title="Zoom Out">
    <svg viewBox="65 0 70 70" class="zoom-svg">
      <g transform="matrix(1,0,0,1,-4.97116,-76.2244)" class="stroke">
        <path
          d="M118,118C122.1,113.8 124.3,108.7 124.3,102.8C124.3,96.8 122.1,91.8 118,87.6C113.8,83.4 108.7,81.3 102.8,81.3C96.8,81.3 91.8,83.4 87.6,87.6C83.3,91.8 81.3,96.8 81.3,102.8C81.3,108.7 83.3,113.8 87.6,118C91.8,122.2 96.8,124.3 102.8,124.3C108.7,124.3 113.8,122.2 118,118"
        />
        <path d="M118,118L132.3,132.3" />
        <path d="M114.5,102.1L90.8,102.1" />
      </g>
    </svg>
  </button>
</div>

<style lang="scss">
  .zoomer {
    float: right;
    display: flex;
    margin-left: -100px; // allow overlap

    button {
      background: none;
      cursor: pointer;
    }
  }

  .zoom-svg {
    width: 50px;
    height: 50px;
    fill-rule: evenodd;
    clip-rule: evenodd;
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-miterlimit: 10;
  }

  .stroke {
    fill: none;
    fill-rule: nonzero;
    stroke: black;
    stroke-width: 7px;
  }
</style>
