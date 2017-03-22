let gulp = require('gulp');
let sass = require('gulp-sass');
let useref = require('gulp-useref');
let uglify = require('gulp-uglify');
let gulpIf = require('gulp-if');
let cssnano = require('gulp-cssnano');
let del = require('del');
let plumber = require('gulp-plumber');
let autoprefixer = require('gulp-autoprefixer');
let merge = require('merge-stream');
let runSequence = require('run-sequence');
let sourceMaps = require('gulp-sourcemaps');
let rev = require('gulp-rev');
let revReplace = require('gulp-rev-replace');
let debug = require('gulp-debug');
let rename = require('gulp-rename');
let browserSync = require('browser-sync').create();
let webpack = require('webpack-stream');
let path = require("path");

let prefix = 'forum';

function swallowError (error) {

    // If you want details of the error in the console
    console.log(error.toString());

    this.emit('end')
}

gulp.task('sass', function () {
    return gulp.src('assets/scss/*.scss')
        .pipe(plumber())
        .pipe(sourceMaps.init())
        .pipe(sass())
        .on('error', swallowError)
        .pipe(autoprefixer({
            browsers: ['> 0.5%'],
            cascade: false
        }))
        .pipe(gulpIf('*.css', cssnano({
            discardUnused: false,
            zindex: false
        })))
        .pipe(sourceMaps.write('./'))
        .pipe(gulp.dest('assets/css'))
        .pipe(gulpIf('*.css', browserSync.stream()));
});

gulp.task('useref', function () {
    del('assets/css/admin-*.css');
    del('assets/css/styles-*.css');
    del('assets/js/app-*.js');
    del('assets/js/admin-*.js');
    //noinspection JSUnusedGlobalSymbols
    let ur = gulp.src('app/views/*/*.php')
        .pipe(useref({
            searchPath: './',
            transformPath: function (filePath) {
                return filePath.replace('/' + prefix + '//' + prefix, '/' + prefix)
            }
        }))
        .pipe(gulpIf('*.js', uglify({
            warnings: true,
            mangle: false
        })))
        .pipe(gulpIf('*.js', rev()))
        .pipe(gulpIf('*.css', cssnano({
            discardUnused: false
        })))
        .pipe(gulpIf('*.css', rev()))
        .pipe(revReplace({replaceInExtensions: ['.php'], prefix: (prefix != '' ? '/' + prefix : '')}))
        .pipe(
            gulp.dest(function (file) {
                return file.base + '/build';
            })
        ).on('end', function () {
            gulp.src(['app/views/*/build/assets/css/*.*'])
                .pipe(rename({dirname: ''}))
                .pipe(gulp.dest('./assets/css')).on('end', function () {
                gulp.src(['app/views/*/build/assets/js/*.*'])
                    .pipe(rename({dirname: ''}))
                    .pipe(gulp.dest('./assets/js')).on('end', function () {
                    del(['app/views/*/build']);
                });
            })

        });

    return merge([ur]);
});

gulp.task('clean:dist', function () {
    return del.sync('dist');
});

gulp.task('watch', function () {
    gulp.watch('assets/scss/**/*.scss', ['sass']);
});

gulp.task('pi', function () {
    return gulp.src(['node_modules/jquery/dist/jquery.min.js'])
        .pipe(gulp.dest('assets/js/'));
});

gulp.task('js', function () {
    let app = gulp.src(['assets/dev/js/app.js'])
        .pipe(webpack({
            watch: false,
            output: {
                filename: 'app.js'
            },
            devtool: 'source-map',
            module: {
                loaders: [
                    {
                        test: /\.js$/,
                        exclude: /node_modules/,
                        loader: 'babel-loader',
                        query: {
                            presets: ['es2015']
                        }
                    }
                ],
            }
        }))
        .on('error', swallowError)
        .pipe(gulp.dest('assets/js/'));
    let admin = gulp.src(['assets/dev/js/admin.js'])
        .pipe(webpack({
            watch: false,
            output: {
                filename: 'admin.js'
            },
            devtool: 'source-map',
            module: {
                loaders: [
                    {
                        test: /\.js$/,
                        exclude: /node_modules/,
                        loader: 'babel-loader',
                        query: {
                            presets: ['es2015']
                        }
                    }
                ],
            }
        }))
        .on('error', swallowError)
        .pipe(gulp.dest('assets/js/'));
    return merge(app, admin);
});

gulp.task('build', function (callback) {
    runSequence('clean:dist',
        'sass',
        'js',
        'useref',
        callback
    );
});

gulp.task('serve', ['sass'], function () {

    browserSync.init({
        open: "external",
        host: "dev.koko.lv",
        proxy: "dev.koko.lv"
    });

    gulp.watch("assets/scss/**/*.scss", {interval: 500}, ['sass']);
    gulp.watch("assets/dev/js/{app,admin}.js", ['js']);
    gulp.watch("**/*.php", {interval: 500}).on('change', browserSync.reload);
    gulp.watch("assets/js/**/*.js", {interval: 500}).on('change', browserSync.reload);
});