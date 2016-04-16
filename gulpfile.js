var gulp = require('gulp'),
    minifyCss = require('gulp-minify-css'),
    uglify = require('gulp-uglifyjs'),
    uglifycss = require('gulp-uglifycss'),
    concat = require('gulp-concat'),
    version = require('gulp-version-number'),
    inject = require('gulp-inject'),
    sass = require('gulp-sass'),
    bump = require('gulp-bump'),
    header = require('gulp-header'),
    pkg = require('./package.json'),
    mainBowerFiles = require('main-bower-files'),
    stripDebug = require('gulp-strip-debug'),
    gulpFilter = require('gulp-filter'),
    onError = function onError(err) {
        console.log(err);
    };
var config = {
    development: {
        root: '.',
        application: 'application',
    },
    prod: {
        root: '/',
        application: 'application',
    }, 
    env: "dev"
};
var banner = ['/**',
  ' * <%= pkg.name %> - <%= pkg.description %>',
  ' * @version v<%= pkg.v.app %>',
  ' * @compile-time: ' + new Date().toString(),
  ' */',
  ''].join('\n');

var scriptsFilter = gulpFilter(["**/*.js"]);

gulp.task("sass-compile", function(){
    gulp.src([
        config.development.application + '/assets/scss/**/*.scss',
        config.development.application + '/app/**/scss/*.scss'
        ])
        .pipe(concat({ path: pkg.name + '_stylesheets.css'}))
        .pipe(sass().on('error', sass.logError))
        .pipe(minifyCss({
            compatibility: 'ie8',
            keepSpecialComments: 0
        }))
        .pipe(uglifycss({
            "max-line-len": 80
        }))
        .pipe(header(banner, {pkg: pkg}))
        .pipe(gulp.dest(config.development.application + '/assets/css/minified'));

    //Update build-nr
    gulp.src(config.development.root + '/package.json')
    .pipe(bump({key: 'v.build', type: 'prerelease', preid: 'compile'}))
    .pipe(gulp.dest(config.development.root)); 
});

gulp.task('minify-css', ['sass-compile'], function(){
    var patterns = [
        config.development.application + '/assets/css/*.css',
    ];
    //CSS - minify vendor css
    var stream = gulp.src(patterns)
    .pipe(minifyCss({
        compatibility: 'ie8',
        keepSpecialComments: 0
    }))    
    .pipe(concat({ path: pkg.name + '_stylesheets.css'}))
    .pipe(header(banner, {pkg: pkg}))
    .pipe(gulp.dest(config.development.application + '/assets/css/minified'));

    //Callback
    return stream;
});

gulp.task('minify-js', function(){

    // Globbing patterns
    var patterns = [
        config.development.application + '/assets/**/*.js'
    ];

    //JS - minify own js
    return gulp.src(patterns)
        .pipe(uglify())
        .pipe(concat({ path: pkg.name + '_scripts.js'}))
        .pipe(header(banner, {pkg: pkg}))
        .pipe(gulp.dest(config.development.application + '/assets/js/minified'));

        //JS - minify vendor js
        gulp.src(mainBowerFiles())
        .pipe(scriptsFilter)
        .pipe(concat({ path: pkg.name + '_vendor_scripts.js'}))
        .pipe(uglify())
        .pipe(stripDebug())
        .pipe(header(banner, {pkg: pkg}))
        .pipe(gulp.dest(config.development.application + '/assets/js/minified'));
})

gulp.task('uglify', ['minify-css', 'minify-js'], function(){
    //Uglify css
    gulp.src(config.development.application + '/assets/css/minified/*.css')
    .pipe(uglifycss({
        "max-line-len": 80
    }))
    .pipe(header(banner, {pkg: pkg}))
    .pipe(gulp.dest(config.development.application + '/assets/css/minified'));

    //Uglify js
    var stream = gulp.src(config.development.application + '/assets/js/minified/' + pkg.name + '_scripts.js')
    .pipe(uglify())
    .pipe(stripDebug())
    .pipe(gulp.dest(config.development.application + '/assets/js/minified'));

    //Callback
    return stream;
});

gulp.task("inject", function(){
    switch(config.env){
        case "prod":
        case "test":
            gulp.start("inject-minified");
            gulp.start("version");
            break;
        case "dev":
            gulp.start("inject-minified");
            break;
    }
});

gulp.task('inject-minified', ['minify-css', 'minify-js'], function(){
    //Inject in header and footer
    var stream = gulp.src(config.development.application + '/layout/myLayout/*.php')
    .pipe(
        inject(
            gulp.src([
                config.development.application + '/assets/css/minified/*.css', 
                config.development.application + '/assets/js/minified/*.js',
            ], {read: false}
            )
        )
    )
    .pipe(gulp.dest(config.development.application + '/layout/myLayout'));

    //Callback
    return stream;
});

gulp.task('version', ['inject-minified'], function () {
    gulp.src(config.development.application + '/layout/myLayout/*.php')
    .pipe(
        version({
            'value' : '%MDS%',
            'append': {
                'key': 'v',
                'cover': 1,
                'to': [
                    'js',
                    'css',
                ],
            },
        })
    )
    .pipe(gulp.dest(config.development.application + '/layout/myLayout'));
});

gulp.task('bump-version', function(){
    switch(config.env){
        case "prod":
            var bump_type = "minor";
            break;
        case "test":
            var bump_type = "patch";
            break;
        case "dev":
            var bump_type = "patch";
            break;
    }
    gulp.src(config.development.root + '/package.json')
    .pipe(bump({key: 'v.app', type: bump_type}))
    .pipe(bump({key: 'v.build', type: bump_type}))
    .pipe(gulp.dest(config.development.root)); 
});

/* CHAIN TASKS */
gulp.task('prod', ['version-prod', 'minify-css', 'minify-js', 'uglify', 'inject']);
gulp.task('test', ['version-test', 'minify-css', 'minify-js', 'inject']);
gulp.task('dev', ['version-dev', 'inject']);

/* WATCH .SCSS FILES FOR CHANGES */
gulp.task('watch', function(){
    config.env = "dev";
    gulp.watch([
        config.development.application + '/assets/scss/**/*.scss',
        config.development.application + '/app/**/scss/*.scss',
        ], ['sass-compile', 'inject']);
});

/* SET ENVIRONMENT AT START OF TASKS */
gulp.task('version-prod', function(){
    config.env = "prod"; 
    gulp.start("bump-version");   
});

gulp.task('version-test', function(){
    config.env = "test";    
    gulp.start("bump-version");
});

gulp.task('version-dev', function(){
    config.env = "dev";   
    gulp.start("bump-version"); 
});
