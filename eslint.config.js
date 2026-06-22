import * as rippleCompiler from "ripple/compiler";
import ripple from "@ripple-ts/eslint-plugin";

globalThis.__RIPPLE_COMPILER__ = rippleCompiler;

export default [...ripple.configs.recommended];
