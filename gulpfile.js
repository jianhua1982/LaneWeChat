var gulp = require('gulp'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    less = require('gulp-less'),
    minifycss = require('gulp-minify-css'),
    minifyhtml = require('gulp-minify-html'),
    gulpif = require('gulp-if'),
    minifyInline = require('gulp-minify-inline'),
    connect = require('gulp-connect');

var patchDir = './Production';

var srcDirClient = './Client/';
var releaseDirClient = patchDir + '/Client/';

// {任务名称:{任务配置}}
var tasksClient = {
    'js_client': {
        handler: 'js',
        src: [srcDirClient + 'js/*.js'],
        dest: releaseDirClient + 'js'
    },

    'html_client': {
        handler: 'html',
        src: [srcDirClient + 'html/*.html'],
        dest: releaseDirClient + 'html'
    },

    'img_client': {
        handler: 'file',
        src: [srcDirClient + 'img/**'],
        dest: releaseDirClient + 'img'
    },

    'css_client': {
        handler: 'css',
        src: [srcDirClient + 'css/*.css'],
        dest: releaseDirClient + 'css'
    }
};

var ifconcat = function (concatName) {
    return !!concatName ? concat(concatName) : 1;
};

// 根据任务的不同类型作对应处理
var taskHandler = {
    'less': function (options) {
        gulp.src(options.src)
            .pipe(gulpif(!!options.concat, ifconcat(options.concat)))
            .pipe(less().on('error', function (e) {
                console.log(e)
            }))
            .pipe(minifycss({
                compatibility: '*',
                advanced: false
            }))
            .pipe(gulp.dest(options.dest))
    },

    'css': function (options) {
        gulp.src(options.src)
            .pipe(gulpif(!!options.concat, ifconcat(options.concat)))
            .pipe(minifycss({
                compatibility: '*',
                advanced: false
            }))
            .pipe(gulp.dest(options.dest))
    },

    'js': function (options) {
        gulp.src(options.src)
            .pipe(gulpif(!!options.concat, ifconcat(options.concat)))
            .pipe(uglify({}))
            .pipe(gulp.dest(options.dest))
    },

    'html': function (options) {
        gulp.src(options.src)
            .pipe(gulpif(!!options.concat, ifconcat(options.concat)))
            .pipe(minifyhtml({quotes: 1}))
            .pipe(minifyInline())
            .pipe(gulp.dest(options.dest))
    },

    'img': function (options) {
        gulp.src(options.src)
            .pipe(imagemin())
            .pipe(gulp.dest(options.dest))
    },

    'file': function (options) {
        gulp.src(options.src)
            .pipe(gulp.dest(options.dest))
    }
};


var allTasks = [];

// 注册任务 Client
for (var taskName in tasksClient) {
    (function (options) {
        gulp.task(taskName, function () {
            taskHandler[options.handler](options);
        });
        allTasks.push(taskName);
    })(tasksClient[taskName]);
}

// server
gulp.task('server', function () {
    connect.server({
        root: '../../',
        livereload: true,
        port: 8080
    });
});


// watch dev
gulp.task('watch', function () {
    var i, taskName;

    // live reload

    for (i in allTasks) {
        taskName = allTasks[i];

        gulp.watch(tasksAds[taskName].src, function () {
            gulp.src(tasksAds[taskName].src)
                .pipe(connect.reload());
        });

        gulp.watch(tasksClient[taskName].src, function () {
            gulp.src(tasksClient[taskName].src)
                .pipe(connect.reload());
        });
    }

});

// all dev
gulp.task('default', [/*'less', */'server'/*, 'watch'*/]);


// release
gulp.task('release', allTasks);
