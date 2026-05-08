import fs from "fs";

import esbuild from "esbuild";
import sveltePlugin from "esbuild-svelte";
import { sveltePreprocess } from "svelte-preprocess";

await esbuild.build({
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
  ],
  logLevel: "info",
});

fs.renameSync("modules/js/Game.css", "theanarchy.css");
