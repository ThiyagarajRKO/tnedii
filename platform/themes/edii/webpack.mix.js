let mix = require("laravel-mix");
const purgeCss = require("@fullhuman/postcss-purgecss");

const path = require("path");
let directory = path.basename(path.resolve(__dirname));

const source = "platform/themes/" + directory;
const dist = "public/themes/" + directory;

mix.js(source + "/assets/js/components.js", dist + "/js").vue({ version: 2 });

mix
    // purgeCSS is used to remove unused CSS, if you are not sure about it, you can change it to
    // .sass(source + '/assets/sass/style.scss', dist + '/css')
    .sass(source + "/assets/sass/style.scss", dist + "/css", {}, [
        purgeCss({
            content: [
                source + "/assets/js/components/*.vue",
                source + "/layouts/*.blade.php",
                source + "/partials/*.blade.php",
                source + "/partials/**/*.blade.php",
                source + "/views/*.blade.php",
                source + "/views/**/*.blade.php",
                source + "/widgets/**/templates/frontend.blade.php",
                "platform/plugins/contact/resources/views/forms/contact.blade.php",
            ],
            defaultExtractor: (content) =>
                content.match(/[\w-/.:]+(?<!:)/g) || [],
            safelist: [
                /^navigation-/,
                /^language-/,
                /language_bar_list/,
                /show-admin-bar/,
                /^fa-/,
                /breadcrumb/,
                /active/,
                /show/,
            ],
        }),
    ])
    .js(source + "/assets/js/tvet.js", dist + "/js")

    .copyDirectory(dist + "/css", source + "/public/css")
    .copyDirectory(dist + "/js", source + "/public/js");
