var gulp = require('gulp');
var jshint = require('gulp-jshint');
var less   = require('gulp-less');
var minifyCSS = require('gulp-minify-css');
var path = require('path');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var sourcemaps = require('gulp-sourcemaps');
var browserSync = require('browser-sync');
var autoprefixer = require('gulp-autoprefixer');
var reload      = browserSync.reload;
var minifyHTML = require('gulp-minify-html');

gulp.task('less', function () {
    gulp.src('./themes/mercury/assets/less/styles.less')
        .pipe(less())
        .on('error', function () {
            this.emit('end');
        })
        .pipe(rename('styles.min.css'))
        .pipe(gulp.dest('./themes/mercury/assets/css'))
        .pipe(browserSync.reload({stream:true}));
});

gulp.task('scripts', function() {
    return gulp.src([
        'assets/js/vendor/jquery.js',
        'assets/js/vendor/bootstrap/alert.js',
        'assets/js/vendor/bootstrap/button.js',
        'assets/js/vendor/bootstrap/collapse.js',
        'assets/js/vendor/bootstrap/dropdown.js',
        'assets/js/vendor/bootstrap/modal.js',
        'assets/js/vendor/bootstrap/tab.js',
        'assets/js/vendor/bootstrap/transition.js',
        'assets/js/vendor/bootstrap/affix.js',
        'assets/js/vendor/bootstrap-hover-dropdown.js',
        'assets/js/vendor/jquery.lazyload.min.js',
        'assets/js/vendor/jquery.raty.js',
        'assets/js/vendor/pagination.js',
        'assets/js/vendor/knockout.js',
        'assets/js/vendor/pikaday.js',
        'assets/js/vendor/icheck.js',
        'assets/js/vendor/video.js',
        'assets/js/vendor/youtube.js',
        'assets/js/vendor/noty.min.js',
        'assets/js/knockout/start.js',
        'assets/js/knockout/utils.js',
        'assets/js/knockout/customBindings.js',
        'assets/js/knockout/videos.js',
        'assets/js/knockout/paginator.js',
        'assets/js/knockout/dashboard/titles.js',
        'assets/js/knockout/dashboard/slider.js',
        'assets/js/knockout/dashboard/actors.js',
        'assets/js/knockout/dashboard/news.js',
        'assets/js/knockout/dashboard/users.js',
        'assets/js/knockout/dashboard/categories.js',
        'assets/js/knockout/dashboard/reviews.js',
        'assets/js/knockout/dashboard/menus.js',
        'assets/js/knockout/dashboard/pages.js',
        'assets/js/knockout/dashboard/createEditPage.js',
        'assets/js/knockout/titles/create.js',
        'assets/js/knockout/media.js',
        'assets/js/knockout/home.js',
        'assets/js/knockout/titles/show.js',
        'assets/js/knockout/reviews.js',
        'assets/js/knockout/titles/index.js',
        'assets/js/knockout/actors-create.js',
        'assets/js/knockout/profile.js',
        'assets/js/knockout/autocomplete.js',
        'assets/js/knockout/install.js'
    ])
    .pipe(concat('scripts.min.js'))
    .pipe(gulp.dest('assets/js'))
    .pipe(browserSync.reload({stream:true}));
});


gulp.task('minify', function() {
    gulp.src('assets/js/scripts.min.js').pipe(uglify()).pipe(gulp.dest('assets/js'));

    gulp.src('themes/mercury/assets/css/styles.min.css')
        .pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false,
            remove: false
        }))
        .pipe(minifyCSS({compatibility: 'ie9'}))
        .pipe(gulp.dest('themes/mercury/assets/css'));
});

// Watch Files For Changes
gulp.task('watch', function() {
    browserSync({
        proxy: "localhost/mtdb/"
    });

    gulp.watch('assets/js/**/*.js', ['scripts']);
    gulp.watch('themes/mercury/assets/less/**/*.less', ['less']);
    gulp.watch('themes/mercury/views/**/**').on('change', reload);
});

// Default Task
gulp.task('default', ['watch']);