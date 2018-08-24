var gulp = require("gulp");

var concat = require("gulp-concat");
var uglify = require("gulp-uglify");
var minifyCss = require("gulp-minify-css");
var autoprefixer = require("gulp-autoprefixer");
var sass = require("gulp-sass");

var fs = require('fs');
var exec = require('child_process').exec;

// Concatenate & Minify JS
var scripts = {
	main: [
		"assets/js/third-party/jquery-3.2.1.min.js",
		"assets/js/third-party/popper.min.js",
		"assets/js/third-party/angular.min.js",
		"assets/js/third-party/decimal.min.js",
		"assets/js/trading-tracker/stickyFooter.js"
	]
};
var scriptNames = Object.keys(scripts);
scriptNames.forEach(function (key, i) {
	gulp.task("scripts-" + key, function () {
		return gulp.src(scripts[key])
				.pipe(concat(key + ".min.js"))
				.pipe(uglify())
				.pipe(gulp.dest("assets/js/"));
	});
});
gulp.task("scripts", ["scripts-main"]);

// Minify Stylesheets
var stylesheets = {
	main: [
		"assets/css/third-party/bootstrap.min.css",
		"assets/css/trading-tracker/style.css"
	]
};
var stylesheetNames = Object.keys(stylesheets);
stylesheetNames.forEach(function (key, i) {
	gulp.task("styles-" + key, function () {
		return gulp.src(stylesheets[key])
				.pipe(concat(key + ".min.css"))
				.pipe(autoprefixer({
					browsers: ["> 0.5%", "ie 8-11"],
					remove: false
				}))
				.pipe(minifyCss({
					compatibility: "ie8"
				}))
				.pipe(gulp.dest("assets/css/"));
	});
});
gulp.task("styles", ["styles-main"]);

gulp.task("sass", function () {
	return gulp.src("assets/css/trading-tracker/style.scss")
			.pipe(sass().on("error", sass.logError))
			.pipe(gulp.dest("assets/css/trading-tracker"));
});
// Watch Files For Changes
gulp.task("watch", function () {
	gulp.watch("assets/css/trading-tracker/**/*.scss", ["sass"]);
});

gulp.task("store-version", function() {

	var fileName = "assets/version.txt";

	// Try and get the latest tag on current branch
	exec("git describe --abbrev=0 --tags\n", function (err, stdout, stderr) {

		// If found store in text file
		if (stdout && stdout !== "null")
		{
			fs.writeFile(fileName, stdout);
		}
		else
		{
			// Else log any errors
			console.log(err);
			console.log(stdout);
			console.log(stderr);

			// Then try to get current branch name
			exec("git branch | grep \\* | cut -d ' ' -f2", function (err, stdout, stderr) {
				// If name found store
				if (stdout && stdout !== "null")
				{
					fs.writeFile(fileName, stdout);
				}
				else
				{
					// Else just log errors & store empty string in text file
					console.log(err);
					console.log(stdout);
					console.log(stderr);
					fs.writeFile(fileName, "");
				}
			});
		}
	});
});

gulp.task("default", ["scripts", "styles"]);