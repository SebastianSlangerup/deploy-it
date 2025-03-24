import { defineConfig } from "eslint/config";
import globals from "globals";
import js from "@eslint/js";
import tseslint from "typescript-eslint";
import pluginVue from "eslint-plugin-vue";


export default defineConfig([
  { files: ["**/*.{js,mjs,cjs,ts,vue}"] },
  { files: ["**/*.{js,mjs,cjs,ts,vue}"], languageOptions: { globals: {...globals.browser, ...globals.node} } },
  { files: ["**/*.{js,mjs,cjs,ts,vue}"], plugins: { js }, extends: ["js/recommended"] },
  tseslint.configs.recommended,
  pluginVue.configs["flat/essential"],
  { files: ["**/*.vue"], languageOptions: { parserOptions: { parser: tseslint.parser } } },
  {
    ignores: ["resources/js/components/ui/**", "resouces/js/types/**"]
  },
  {
    files: ["resources/js/**/*.{js,ts,vue}"],
    rules: {
      "no-unused-vars": "off",
      "no-undef": "off",
      "vue/multi-word-component-names": "off",
      "vue/define-emits-declaration": ["error", "type-based"],
      "vue/define-props-declaration": ["error", "type-based"],
      "vue/no-undef-components": "off",
      "vue/no-unused-refs": "error",
      "vue/no-v-html": "off",
      "no-console": "error",
      "@typescript-eslint/no-explicit-any": "off",
      "@typescript-eslint/ban-ts-comment": ["error", { "ts-ignore": "allow-with-description" }],
    },
  },
]);
