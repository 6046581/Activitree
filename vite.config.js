import { defineConfig } from "vite";
import { ripple } from "@ripple-ts/vite-plugin";

export default defineConfig({
   plugins: [ripple()],
   server: {
      port: 3000,
      proxy: {
         "/api": {
            target: "http://localhost",
            changeOrigin: true,
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
});
