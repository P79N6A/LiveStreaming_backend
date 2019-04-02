//npm install gulp
//npm install gulp-less gulp-minify-css gulp-concat gulp-notify gulp-livereload gulp-rename --save-dev   gulp 实现less转化监听合并和压缩
//npm install --save-dev gulp del vinyl-paths
//npm install gulp-sourcemaps --save-dev 生成less对应地图
// 引入 gulp
var gulp = require('gulp');
 
// 引入组件
var 
    less = require('gulp-less'),//less 文件转换
    minifycss = require('gulp-minify-css'),//css压缩
    concat = require('gulp-concat'),//文件合并
    rename = require('gulp-rename'),//文件更名
    notify = require('gulp-notify');//提示信息
    livereload = require('gulp-livereload');//网页自动刷新
/*    clean = require('gulp-clean');

 gulp.task('clean', function() {  
  return gulp.src(['dist/assets/css', 'dist/assets/js', 'dist/assets/img'], {read: false})
    .pipe(clean());
});*/
//转换less文件   
gulp.task('less', function() {
    return gulp.src('src/less/*.less')//该任务针对的文件
        .pipe(less())//该任务调用的模块
        .pipe(gulp.dest('src/css'))
        .pipe(livereload())
    .pipe(notify({ message: 'less task ok' }));
});
  
 
// 合并、压缩、重命名css
gulp.task('css', function() {
  return gulp.src('src/css/*.css')
    /*.pipe(concat('fanwe.css'))
    .pipe(gulp.dest('src/css'))
    .pipe(rename({ suffix: '.min' }))*/
    .pipe(minifycss())
    .pipe(gulp.dest('css'))
    .pipe(notify({ message: 'css task ok' }));
});


// 默认任务
gulp.task('default', function(){
 	 gulp.run('less', 'css');
	livereload.listen();
	gulp.watch('src/less/*.less', ['less']);
	gulp.watch('src/css/*.css', ['css']);

});