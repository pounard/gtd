var
  gulp = require('gulp'),
  uglify = require('gulp-uglify');
  rename = require('gulp-rename'),
  cssmin = require('gulp-clean-css'),
  less = require('gulp-less'),
  concat = require('gulp-concat')
;

function errorHandler(error) {
  console.log(error.toString());
  this.emit('end');
}

gulp.task('js', function () {
  var map = require('./app/Resources/public/js/map.json'), list = [];
  for (var i in map) {
    if (map.hasOwnProperty(i) && map[i]) {
      list.push(i);
    }
  }
  return gulp.src(list)
    .pipe(concat('script.min.js'))
//    .pipe(uglify())
    .pipe(gulp.dest('./web/dist/js/'))
    .on('error', errorHandler)
  ;
});

gulp.task('less', function () {
  var pipe = gulp.src('./app/Resources/public/less/style.less');
  pipe = pipe
    .pipe(less())
    // .pipe(cssmin())
    // .pipe(rename({suffix: '.min'}))
  ;
  return pipe
    .pipe(gulp.dest('./web/dist/css/'))
    .on('error', errorHandler)
  ;
});

gulp.task('default', ['js', 'less']);

// Handle the error
function errorHandler(error) {
  console.log(error.toString());
  this.emit('end');
}
