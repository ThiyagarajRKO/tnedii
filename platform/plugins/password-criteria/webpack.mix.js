let mix = require("laravel-mix");

const path = require("path");
let directory = path.basename(path.resolve(__dirname));

const source = "platform/plugins/" + directory;
const dist = "public/vendor/core/plugins/" + directory;

mix.js(source + "/resources/assets/js/save_criteria.js", dist + "/js")
   .js(source + "/resources/assets/js/save_password.js", dist + "/js")
   .js(source + "/resources/assets/js/session_idle_time_check.js", dist + "/js")

//    .copyDirectory(dist + "/css", source + "/public/css")
    .copyDirectory(dist + "/js", source + "/public/js");
