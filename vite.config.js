import { defineConfig, loadEnv } from "vite";
import { ripple } from "@ripple-ts/vite-plugin";

export default defineConfig(({ mode }) => {
   const env = loadEnv(mode, process.cwd(), "");

   const base = env.VITE_BASE_URL || "/";
   const devApiTarget = env.VITE_DEV_API_TARGET || "http://localhost:8080";
   const devApiBasePathRaw = env.VITE_DEV_API_BASE_PATH || "/api";
   const devApiBasePath = `/${devApiBasePathRaw.replace(/^\/+|\/+$/g, "")}`;
   const apiProxy = {
      "/api": {
         target: devApiTarget,
         changeOrigin: true,
         rewrite: (path) => path.replace(/^\/api(?=\/|$)/, devApiBasePath),
      },
   };

   return {
      base,
      plugins: [ripple()],
      server: {
         port: 3000,
         proxy: apiProxy,
         watch: {
            usePolling: true,
            interval: 100,
         },
      },
      preview: {
         port: 4173,
         proxy: apiProxy,
      },
      build: {
         target: "esnext",
      },
   };
});
