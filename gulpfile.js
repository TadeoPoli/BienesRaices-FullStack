const { src, dest, watch, parallel } = require("gulp");
const sass = require("gulp-sass")(require("sass"));
const autoprefixer = require("autoprefixer");
const postcss = require("gulp-postcss");
const sourcemaps = require("gulp-sourcemaps");
const cssnano = require("cssnano");
const concat = require("gulp-concat");
const terser = require("gulp-terser-js");
const rename = require("gulp-rename");
const sharp = require("sharp");
const fs = require("fs");
const path = require("path");
const { glob } = require("glob");

const paths = {
  scss: "src/scss/**/*.scss",
  js: "src/js/**/*.js",
  imagenes: "src/img/**/*.{png,jpg,jpeg}",
  svg: "src/img/**/*.svg",
};

function css() {
  return src(paths.scss)
    .pipe(sourcemaps.init())
    .pipe(sass())
    .pipe(postcss([autoprefixer(), cssnano()]))
    .pipe(sourcemaps.write("."))
    .pipe(dest("public/build/css"));
}

function javascript() {
  return src(paths.js)
    .pipe(sourcemaps.init())
    .pipe(concat("bundle.js"))
    .pipe(terser())
    .pipe(sourcemaps.write("."))
    .pipe(rename({ suffix: ".min" }))
    .pipe(dest("./public/build/js"));
}

async function imagenes() {
  const srcDir = "src/img";
  const buildDir = "public/build/img";
  const images = await glob("src/img/**/*.{png,jpg,jpeg}");

  await Promise.all(images.map((file) => {
    const relativePath = path.relative(srcDir, path.dirname(file));
    const outputSubDir = path.join(buildDir, relativePath);
    return procesarImagenes(file, outputSubDir);
  }));
}

function procesarImagenes(file, outputSubDir) {
  if (!fs.existsSync(outputSubDir)) {
    fs.mkdirSync(outputSubDir, { recursive: true });
  }
  const baseName = path.basename(file, path.extname(file));
  const outputFile = path.join(outputSubDir, `${baseName}.jpg`);
  const outputFileWebp = path.join(outputSubDir, `${baseName}.webp`);

  const options = { quality: 80 };
  return Promise.all([
    sharp(file).jpeg(options).toFile(outputFile),
    sharp(file).webp(options).toFile(outputFileWebp),
  ]);
}

function svg() {
  return src(paths.svg, { base: "src/img" })
    .pipe(dest("public/build/img"));
}

function watchArchivos() {
  watch(paths.scss, css);
  watch(paths.js, javascript);
  watch(paths.imagenes, imagenes);
  watch(paths.svg, svg);
}

const build = parallel(css, javascript, imagenes, svg);

exports.css = css;
exports.javascript = javascript;
exports.imagenes = imagenes;
exports.svg = svg;
exports.build = build;
exports.watchArchivos = watchArchivos;
exports.default = parallel(build, watchArchivos);
