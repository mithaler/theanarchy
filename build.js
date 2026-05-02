import fs from "fs";

import esbuild from "esbuild";
import sveltePlugin from "esbuild-svelte";
import { sveltePreprocess } from "svelte-preprocess";
import { compile as sassCompile } from "sass";

await esbuild.build({
  entryPoints: ["modules/js/Game.svelte.ts"],
  mainFields: ["svelte", "browser", "module", "main"],
  conditions: ["svelte", "browser"],
  bundle: true,
  format: "esm",
  outfile: "modules/js/Game.js",
  plugins: [
    sveltePlugin({
      preprocess: sveltePreprocess(),
      compilerOptions: { dev: true },
    }),
  ],
  logLevel: "info",
});

const out = sassCompile("modules/css/theanarchy.scss", {
  style: "compressed",
});
fs.writeFileSync("theanarchy.css", out.css);
