import { defineConfig, loadEnv } from "vite";
import { ripple } from "@ripple-ts/vite-plugin";

export default defineConfig(({ mode }) => {
   const env = loadEnv(mode, process.cwd(), "");

   const base = env.VITE_BASE_URL || "/";
   const devApiTarget = env.VITE_DEV_API_TARGET || "http://localhost";
   const devApiBasePathRaw = env.VITE_DEV_API_BASE_PATH || "/activitree/public/api";
   const devApiBasePath = `/${devApiBasePathRaw.replace(/^\/+|\/+$/g, "")}`;

   return {
      base,
      plugins: [ripple()],
      server: {
         port: 3000,
         proxy: {
            "/api": {
               target: devApiTarget,
               changeOrigin: true,
               rewrite: (path) => path.replace(/^\/api(?=\/|$)/, devApiBasePath),
            },
         },
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
