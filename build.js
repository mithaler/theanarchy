import fs from "fs";

import esbuild from "esbuild";
import sveltePlugin from "esbuild-svelte";
import { sveltePreprocess } from "svelte-preprocess";
import { compile as sassCompile } from "sass";

esbuild
  .build({
    entryPoints: ["modules/js/Game.ts"],
    mainFields: ["svelte", "browser", "module", "main"],
    conditions: ["svelte", "browser"],
    bundle: true,
    format: "esm",
    outfile: "modules/js/Game.js",
    plugins: [
      sveltePlugin({
        preprocess: sveltePreprocess(),
      }),
    ],
    logLevel: "info",
  })
  .catch(() => process.exit(1));

const out = sassCompile("modules/css/theanarchy.scss", {
  style: "compressed",
});
fs.writeFileSync("theanarchy.css", out.css);
