// eslint.config.js
import { defineConfig } from "eslint/config";
import globals from "globals";
import js from "@eslint/js";
import ts from "typescript-eslint";
import svelte from "eslint-plugin-svelte";

export default defineConfig(
  js.configs.recommended,
  ts.configs.recommended,
  svelte.configs.recommended,
  {
    languageOptions: {
      globals: {
        ...globals.browser,
        // for Sveltekit in non-SPA mode
        ...globals.node,
      },
    },
    // ...
  },
  {
    files: ["**/*.svelte", "**/*.svelte.ts", "**/*.svelte.js"],
    // See more details at: https://typescript-eslint.io/packages/parser/
    languageOptions: {
      parserOptions: {
        projectService: true,
        // Enable typescript parsing for `.svelte` files.
        extraFileExtensions: [".svelte"],

        // Specify a parser for each language, if needed:
        // parser: {
        //   ts: ts.parser,
        //   typescript: ts.parser
        //   js: espree,            // add `import espree from 'espree'`
        // },
        parser: ts.parser,
      },
    },
  },
  {
    rules: {
      // Override or add rule settings here, such as:
      // 'svelte/rule-name': 'error'
    },
  },
);
