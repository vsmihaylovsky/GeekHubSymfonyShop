var gulp = require('gulp'),
    less = require('gulp-less'),
    clean = require('gulp-clean'),
    concatJs = require('gulp-concat'),
    minifyJs = require('gulp-uglify'),
    sass = require('gulp-sass');
gulp.task('less', function() {
    return gulp.src([
            'web-src/less/*.less',
            'web-src/css/*.css',
            'bower_components/bootstrap/less/bootstrap.less',
            'bower_components/font-awesome/less/font-awesome.less',
            'bower_components/metisMenu/dist/metisMenu.min.css',
            'bower_components/morris.js/morris.css',
            'bower_components/animate.css/animate.min.css',
            'bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css',
            'bower_components/bootstrap-social/bootstrap-social.css'
        ])
        .pipe(less({compress: true}))
        .pipe(gulp.dest('web/css/'));
});
gulp.task('sass', function () {
    return gulp.src([
        'bower_components/datatables-responsive/css/responsive.dataTables.scss'
    ])
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest('web/css/'));
});
gulp.task('images', function () {
    return gulp.src([
            'web-src/images/**/*'
        ])
        .pipe(gulp.dest('web/images/'))
});
gulp.task('media', function () {
    return gulp.src([
            'web-src/media/**/*'
        ])
        .pipe(gulp.dest('web/media/'))
});
gulp.task('fonts', function () {
    return gulp.src([
            'bower_components/bootstrap/fonts/*',
            'bower_components/font-awesome/fonts/*',
            'web-src/fonts/*'])
        .pipe(gulp.dest('web/fonts/'))
});
gulp.task('lib-js', function() {
    return gulp.src([
            'bower_components/jquery/dist/jquery.js',
            'bower_components/bootstrap/dist/js/bootstrap.js',
            'bower_components/metisMenu/dist/metisMenu.min.js'
        ])
        .pipe(concatJs('app.js'))
        .pipe(minifyJs())
        .pipe(gulp.dest('web/js/'));
});
gulp.task('pages-js', function() {
    return gulp.src([
            'web-src/js/*.js',
            'bower_components/morris.js/morris.min.js',
            'bower_components/raphael/raphael-min.js',
            'bower_components/datatables/media/js/jquery.dataTables.min.js',
            'bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js',
            'bower_components/datatables-responsive/js/dataTables.responsive.js'
        ])
        .pipe(minifyJs())
        .pipe(gulp.dest('web/js/'));
});
gulp.task('clean', function () {
    return gulp.src(['web/css/*', 'web/js/*', 'web/images/*', 'web/media/*', 'web/fonts/*'])
        .pipe(clean());
});
gulp.task('default', ['clean'], function () {
    var tasks = ['images', 'media', 'fonts', 'less', 'sass', 'lib-js', 'pages-js'];
    tasks.forEach(function (val) {
        gulp.start(val);
    });
});
gulp.task('watch', function () {
    var less = gulp.watch('web-src/less/*.less', ['less']),
        js = gulp.watch('web-src/js/*.js', ['pages-js']);
});