/**
 * Gulp configuration file
 *
 * @package java-goverment
 * @since   0.0.1
 * @author  Rizal Fauzie <fauzie@idjavahost.com>
 */

var dir_node = './node_modules',
    dir_pub  = './views/assets'
    ;

var gulp = require('gulp'),
    del  = require('del'),
    uglify = require('gulp-uglify'),
    autoprefixer = require('gulp-autoprefixer'),
    sourcemaps = require('gulp-sourcemaps'),
    cleanCSS = require('gulp-clean-css'),
    strip = require('gulp-strip-comments'),
    concat = require('gulp-concat'),
    watch = require('gulp-watch')
    ;

var config = {

    // Autoprefixer options
    autoprefixer: {
        browsers: [
            'Chrome >= 45',
            'Firefox ESR',
            'Edge >= 12',
            'Explorer >= 10',
            'iOS >= 9',
            'Safari >= 9'
        ]
    },

    uglify: {
        src: [
            dir_node + '/bootstrap-toggle/js/bootstrap-toggle.js',
            dir_pub + '/script.js'
        ],
        options: {
            ie8: true,
            warnings: true
        },
        base: dir_pub,
        name: 'script.min.js'
    },

    css: {
        src: [
            dir_pub + '/style.css',
            dir_node + 'bootstrap-toggle/css/bootstrap-toggle.css'
        ],
        options: {
            compatibility: "ie8",
            level: {1: {specialComments: 0}}
        },
        base: dir_pub,
        name: 'style.min.css'
    },
};

// GULP TASKS
gulp.task('clean', function() {
	return del([
        dir_pub + 'core.min.js',
        dir_pub + 'script.min.js',
        dir_pub + 'style.min.css'
    ]);
});

gulp.task('js', function(done) {
	gulp.src(config.uglify.src)
		.pipe(sourcemaps.init())
		.pipe(uglify(config.uglify.options).on('error', function(e){
            console.log(e.cause);
         }))
		.pipe(concat(config.uglify.name),{newLine: ""})
		.pipe(sourcemaps.write('.'))
		.pipe(gulp.dest(config.uglify.base))
        .on('end', done);
});

gulp.task('css', function(done) {
	gulp.src(config.css.src)
		.pipe(sourcemaps.init())
		.pipe(autoprefixer(config.autoprefixer))
        .pipe(cleanCSS(config.css.options))
		.pipe(concat(config.css.name),{newLine: ""})
		.pipe(sourcemaps.write('.'))
		.pipe(gulp.dest(config.css.base))
        .on('end', done);
});

gulp.task('watch', function() {
    // Watch javascript file changes
    gulp.watch(config.uglify.src, ['js']);
    // Watch stylesheet file changes
    gulp.watch(config.css.src, ['css']);
});

// All in One
gulp.task('default', ['clean', 'js', 'css', 'watch']);
