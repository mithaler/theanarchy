import fs from "fs";

import esbuild from "esbuild";
import sveltePlugin from "esbuild-svelte";
import { sveltePreprocess } from "svelte-preprocess";

const moveCss = {
  name: "move-css",
  setup(build) {
    build.onEnd(() => {
      fs.renameSync("modules/js/Game.css", "theanarchy.css");
    });
  },
};

const ctx = await esbuild.context({
  entryPoints: ["modules/js/Game.svelte.ts"],
  mainFields: ["svelte", "browser", "module", "main"],
  conditions: ["svelte", "browser"],
  bundle: true,
  format: "esm",
  outfile: "modules/js/Game.js",
  external: ["img/*"],
  plugins: [
    sveltePlugin({
      preprocess: sveltePreprocess(),
      compilerOptions: { dev: true },
    }),
    moveCss,
  ],
  logLevel: "info",
});

await ctx.watch();
console.log("Started build watcher...");
