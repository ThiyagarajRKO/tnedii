let mix = require('laravel-mix');

const path = require('path');
let directory = path.basename(path.resolve(__dirname));

const source = 'platform/core/' + directory;
const dist = 'public/vendor/core/core/' + directory;

mix
    .js(source + '/resources/assets/js/profile.js', dist + '/js')
    .js(source + '/resources/assets/js/login.js', dist + '/js')
    .js(source + '/resources/assets/js/role.js', dist + '/js')
    /*  @customized Sabari Shankar.Parthiban start */
    .js(source + '/resources/assets/js/user.js', dist + '/js')
    /*  @customized Sabari Shankar.Parthiban end */

    .sass(source + '/resources/assets/sass/login.scss', dist + '/css')
    /*  @customized Sabari Shankar.Parthiban start */
    .sass(source + '/resources/assets/sass/custom-style.scss', dist + '/css')
    /*  @customized Sabari Shankar.Parthiban end */
    .copyDirectory(dist + '/js', source + '/public/js')
    .copyDirectory(dist + '/css', source + '/public/css');
