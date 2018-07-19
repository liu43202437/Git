var gulp = require('gulp');
var uglify = require('gulp-uglify');
var gutil = require('gulp-util');
var babel = require("gulp-babel");
var es2015 = require("babel-preset-es2015");
var minifyCSS = require('gulp-minify-css')
var htmlmin = require('gulp-htmlmin');
var replace = require('gulp-replace');
var del = require('del');
var gulpsync = require('gulp-sync')(gulp);

// Error handler
function handleError(err) {
  log(err.toString());
  this.emit('end');
}

function log(msg) {
  gutil.log(gutil.colors.blue(msg));
}

//====================================================================================================
// Gulp task shortcut
//====================================================================================================
gulp.task('default', ['app']);
gulp.task('wechat', gulpsync.sync(['build:wechat', 'rep:wechat', 'repwechat:css']));
gulp.task('app', gulpsync.sync(['build:app', 'rep:app', 'rep:css']));

//====================================================================================================
// Common task
//====================================================================================================
gulp.task('del', function (cb) {
  del([
    'wechat/images/*',
  ], cb)
})

gulp.task('deln', function (cb) {
  del([
    'wechat/news/*',
  ], cb)
})

//====================================================================================================
// BUILD Wechat pages
//====================================================================================================
var vueHtmlWechat = [
  'wechat/area-manager-point.html',
  'wechat/area-about.html',
  'wechat/manager-point.html',
  'wechat/about-manager.html',
  'wechat/market-manager-point.html',
  'wechat/about-market.html',
  'wechat/manager-review.html',
  'wechat/market-manager-review.html',
  'wechat/area-manager-review.html',
  'wechat/vc-review-info.html',
  'wechat/vc-visiter-manager-zc.html',
  'wechat/vc-visiter-manager-info.html',
  'wechat/vc-shop-img-zc.html',
  'wechat/vc-bank.html',

];

var DevFilesWechat = [
  'wechat-dev/js/route.js',
  'wechat-dev/js/api.js',
  'wechat-dev/js/axios-request.js'
];

gulp.task('build:wechat', [
  'build:wechat:devfile',
  'build:wechat:js',
  'build:wechat:vue',
  'build:wechat:agreement',
  'build:wechat:css',
  'build:wechat:news',
  'build:wechat:html',
  'build:wechat:image',
  'build:wechat:fonts',
  'build:wechat:lib',
  'build:wechat:qrcode:js',
  'build:wechat:qrcode:css',
  'build:wechat:qrcode:html'
]);

gulp.task('build:wechat:devfile', function (cb) {
  return gulp.src(DevFilesWechat)
    .pipe(replace('__DEV__ = 1', '__DEV__ = 0'))
    .pipe(babel({
      presets: [es2015]
    }))
    .pipe(uglify())
    .on('error', handleError)
    .pipe(gulp.dest('wechat/js'));
});

gulp.task('build:wechat:js', function (cb) {
  return gulp.src([
      'wechat-dev/js/*.js',
      '!wechat-dev/js/*.min.js',
      '!wechat-dev/js/pcas-code.js',
    ].concat(DevFilesWechat.map(function (file) {
      return '!' + file;
    })))
    .pipe(babel({
      presets: [es2015]
    }))
    .pipe(uglify())
    .on('error', handleError)
    .pipe(gulp.dest('wechat/js'));
});

gulp.task('build:wechat:vue', function (cb) {
  return gulp.src([
      'wechat-dev/vue-component/*.js'
    ])
    .pipe(babel({
      presets: [es2015]
    }))
    .pipe(uglify())
    .on('error', handleError)
    .pipe(gulp.dest('wechat/vue-component'));
})

gulp.task('build:wechat:agreement', function (cb) {
  return gulp.src([
      'wechat-dev/agreements/*.js'
    ])
    .pipe(babel({
      presets: [es2015]
    }))
    .pipe(uglify())
    .on('error', handleError)
    .pipe(gulp.dest('wechat/agreements'));
})

gulp.task('build:wechat:css', function (cb) {
  return gulp.src('wechat-dev/css/*.css')
    .pipe(minifyCSS())
    .on('error', handleError)
    .pipe(gulp.dest('wechat/css'));
});

gulp.task('build:wechat:html', function (cb) {
  return gulp.src('wechat-dev/*.html')
    .on('error', handleError)
    .pipe(gulp.dest('wechat'));
});

gulp.task('build:wechat:lib', function (cb) {
  return gulp.src('wechat-dev/lib/**')
    .on('error', handleError)
    .pipe(gulp.dest('wechat/lib'));
});

gulp.task('build:wechat:image', function (cb) {
  return gulp.src('wechat-dev/images/**')
    .on('error', handleError)
    .pipe(gulp.dest('wechat/images'))
});

gulp.task('build:wechat:fonts', function (cb) {
  return gulp.src('wechat-dev/fonts/**')
    .on('error', handleError)
    .pipe(gulp.dest('wechat/fonts'))
});

gulp.task('build:wechat:news', function (cb) {
  return gulp.src(['wechat-dev/news/**'])
    .on('error', handleError)
    .pipe(gulp.dest('wechat/news'));
});

gulp.task('build:wechat:qrcode:js', function (cb) {
  return gulp.src([
      'wechat-dev/scan-qr-code/js/*.js',
      '!wechat-dev/scan-qr-code/js/jweixin-1.2.0.js',
      '!wechat-dev/scan-qr-code/js/jquery.sha1.js'
    ])
    .pipe(babel({
      presets: [es2015]
    }))
    .pipe(uglify())
    .on('error', handleError)
    .pipe(gulp.dest('wechat/scan-qr-code/js'));
});

gulp.task('build:wechat:qrcode:css', function (cb) {
  return gulp.src('wechat-dev/scan-qr-code/css/*.css')
    .pipe(minifyCSS())
    .on('error', handleError)
    .pipe(gulp.dest('wechat/scan-qr-code/css'));
});

gulp.task('build:wechat:qrcode:html', function (cb) {
  return gulp.src('wechat-dev/scan-qr-code/*.html')
    .on('error', handleError)
    .pipe(gulp.dest('wechat/scan-qr-code'));
});

gulp.task('rep:wechat', function () {
  return gulp.src(vueHtmlWechat)
    .pipe(replace('lib/css/onsenui.css', 'lib/css/onsenui.min.css'))
    .pipe(replace('lib/css/onsen-css-components.css', 'lib/css/onsen-css-components.min.css'))
    .pipe(replace('lib/js/vue.js', 'lib/js/vue.min.js'))
    .pipe(replace('lib/js/onsenui.js', 'lib/js/onsenui.min.js'))
    .pipe(replace('lib/js/vue-onsenui.js', 'lib/js/vue-onsenui.min.js'))
    .pipe(replace('lib/js/axios.js', 'lib/js/axios.min.js'))
    .pipe(replace('lib/js/select2.js', 'lib/js/select2.min.js'))
    .pipe(replace('lib/css/select2.css', 'lib/css/select2.min.css'))
    .on('error', handleError)
    .pipe(gulp.dest('wechat'));
});


gulp.task('repwechat:css', function () {
  return gulp.src(['wechat/lib/css/*.css'])
    .pipe(replace('-webkit-overflow-scrolling:touch;', '/*-webkit-overflow-scrolling:touch;*/'))
    .pipe(replace('-webkit-overflow-scrolling: touch;', '/* -webkit-overflow-scrolling: touch; */'))
    .on('error', handleError)
    .pipe(gulp.dest('wechat/lib/css'));
})

//====================================================================================================
// BUILD App pages
//====================================================================================================
var vueHtmlApp = [
  'app/area-manager-point.html',
  'app/area-about.html',
  'app/manager-point.html',
  'app/about-manager.html',
  'app/market-manager-point.html',
  'app/about-market.html',
  'app/manager-review.html',
  'app/market-manager-review.html',
  'app/area-manager-review.html',
  'app/vc-review-info.html',
  'app/vc-visiter-manager-zc.html',
  'app/vc-visiter-manager-info.html',
  'app/vc-bank.html',
];

var DevFilesApp = [
  'app-dev/js/route.js',
  'app-dev/js/api.js',
  'app-dev/js/axios-request.js'
];

gulp.task('build:app', [
  'build:app:devfile',
  'build:app:js',
  'build:app:vue',
  'build:app:agreement',
  'build:app:css',
  'build:app:news',
  'build:app:html',
  'build:app:image',
  'build:app:fonts',
  'build:app:lib',
  'build:app:qrcode:js',
  'build:app:qrcode:css',
  'build:app:qrcode:html',
]);

gulp.task('build:app:devfile', function (cb) {
  return gulp.src(DevFilesApp)
    .pipe(replace('__DEV__ = 1', '__DEV__ = 0'))
    .pipe(babel({
      presets: [es2015]
    }))
    .pipe(uglify())
    .on('error', handleError)
    .pipe(gulp.dest('app/js'));
});

gulp.task('build:app:js', function (cb) {
  return gulp.src([
      'app-dev/js/*.js',
      '!app-dev/js/*.min.js',
      '!app-dev/js/pcas-code.js',
    ].concat(DevFilesApp.map(function (file) {
      return '!' + file;
    })))
    .pipe(babel({
      presets: [es2015]
    }))
    .pipe(uglify())
    .on('error', handleError)
    .pipe(gulp.dest('app/js'));
});

gulp.task('build:app:vue', function (cb) {
  return gulp.src([
      'app-dev/vue-component/*.js'
    ])
    .pipe(babel({
      presets: [es2015]
    }))
    .pipe(uglify())
    .on('error', handleError)
    .pipe(gulp.dest('app/vue-component'));
})

gulp.task('build:app:css', function (cb) {
  return gulp.src('app-dev/css/*.css')
    .pipe(minifyCSS())
    .on('error', handleError)
    .pipe(gulp.dest('app/css'));
});

gulp.task('build:app:html', function (cb) {
  return gulp.src('app-dev/*.html')
    .on('error', handleError)
    .pipe(gulp.dest('app'));
});

gulp.task('build:app:lib', function (cb) {
  return gulp.src('app-dev/lib/**')
    .on('error', handleError)
    .pipe(gulp.dest('app/lib'));
});

gulp.task('build:app:agreement', function (cb) {
  return gulp.src([
      'wechat-dev/agreements/*.js'
    ])
    .pipe(babel({
      presets: [es2015]
    }))
    .pipe(uglify())
    .on('error', handleError)
    .pipe(gulp.dest('app/agreements'));
})
gulp.task('build:app:image', function (cb) {
  return gulp.src('app-dev/images/**')
    .on('error', handleError)
    .pipe(gulp.dest('app/images'))
});

gulp.task('build:app:fonts', function (cb) {
  return gulp.src('app-dev/fonts/**')
    .on('error', handleError)
    .pipe(gulp.dest('app/fonts'))
});

gulp.task('build:app:news', function (cb) {
  return gulp.src(['app-dev/news/**'])
    .on('error', handleError)
    .pipe(gulp.dest('app/news'));
});

gulp.task('build:app:qrcode:js', function (cb) {
  return gulp.src([
      'app-dev/scan-qr-code/js/*.js',
      '!app-dev/scan-qr-code/js/jweixin-1.2.0.js',
      '!app-dev/scan-qr-code/js/jquery.sha1.js'
    ])
    .pipe(babel({
      presets: [es2015]
    }))
    .pipe(uglify())
    .on('error', handleError)
    .pipe(gulp.dest('app/scan-qr-code/js'));
});

gulp.task('build:app:qrcode:css', function (cb) {
  return gulp.src('app-dev/scan-qr-code/css/*.css')
    .pipe(minifyCSS())
    .on('error', handleError)
    .pipe(gulp.dest('app/scan-qr-code/css'));
});

gulp.task('build:app:qrcode:html', function (cb) {
  return gulp.src('app-dev/scan-qr-code/*.html')
    .on('error', handleError)
    .pipe(gulp.dest('app/scan-qr-code'));
});

gulp.task('rep:app', function () {
  return gulp.src(vueHtmlApp)
    .pipe(replace('lib/css/onsenui.css', 'lib/css/onsenui.min.css'))
    .pipe(replace('lib/css/onsen-css-components.css', 'lib/css/onsen-css-components.min.css'))
    .pipe(replace('lib/js/vue.js', 'lib/js/vue.min.js'))
    .pipe(replace('lib/js/onsenui.js', 'lib/js/onsenui.min.js'))
    .pipe(replace('lib/js/vue-onsenui.js', 'lib/js/vue-onsenui.min.js'))
    .pipe(replace('lib/js/axios.js', 'lib/js/axios.min.js'))
    .pipe(replace('lib/js/select2.js', 'lib/js/select2.min.js'))
    .pipe(replace('lib/css/select2.css', 'lib/css/select2.min.css'))
    .on('error', handleError)
    .pipe(gulp.dest('app'));
});

gulp.task('rep:css', function () {
  return gulp.src(['app/lib/css/*.css'])
    .pipe(replace('-webkit-overflow-scrolling:touch;', '/*-webkit-overflow-scrolling:touch;*/'))
    .pipe(replace('-webkit-overflow-scrolling: touch;', '/* -webkit-overflow-scrolling: touch; */'))
    .on('error', handleError)
    .pipe(gulp.dest('app/lib/css'));
})