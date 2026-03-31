import { defineConfig, loadEnv } from "vite";
import { ripple } from "@ripple-ts/vite-plugin";

export default defineConfig(({ mode }) => {
   const env = loadEnv(mode, process.cwd(), "");

   const base = env.VITE_BASE_URL || "/";

   return {
      base,
      plugins: [ripple()],
      server: {
         port: 3000,
         watch: {
            usePolling: true,
            interval: 100,
         },
      },
      build: {
         target: "esnext",
      },
   };
});
