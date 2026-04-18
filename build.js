import esbuild from "esbuild";
import sveltePlugin from "esbuild-svelte";
import sveltePreprocess from "svelte-preprocess";

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
