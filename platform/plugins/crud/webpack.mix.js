let mix = require("laravel-mix");

const path = require("path");
let directory = path.basename(path.resolve(__dirname));

const source = "platform/plugins/" + directory;
const dist = "public/vendor/core/plugins/" + directory;

mix
.copyDirectory(source + '/resources/assets/js', dist + '/js')
.copyDirectory(source + '/resources/assets/css', dist + '/css');
