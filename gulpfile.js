var gulp = require("gulp");

var concat = require("gulp-concat");
var uglify = require("gulp-uglify");
var minifyCss = require("gulp-minify-css");
var autoprefixer = require("gulp-autoprefixer");
var sass = require("gulp-sass");

var fs = require("fs");
var exec = require("child_process").exec;

// Concatenate & Minify JS
var scripts = {
    main: [
        "assets/js/third-party/jquery-3.2.1.min.js",
        "assets/js/third-party/popper.min.js",
        "assets/js/third-party/angular.min.js",
        "assets/js/third-party/decimal.min.js",
        "assets/js/trading-tracker/sticky-footer.js",
    ],
};

var scriptsTasks = [];
var scriptNames = Object.keys(scripts);
scriptNames.forEach(function(key) {
    var scriptTask = "scripts-" + key;
    scriptsTasks.push(scriptTask);
    gulp.task(scriptTask, function() {
        return gulp.src(scripts[key])
                   .pipe(concat(key + ".min.js"))
                   .pipe(uglify())
                   .pipe(gulp.dest("assets/js/"));
    });
});
gulp.task("scripts", scriptsTasks);

// Minify Stylesheets
var stylesheets = {
    main: [
        "assets/css/third-party/bootstrap.min.css",
        "assets/css/trading-tracker/style.css",
    ],
};

var stylesheetTasks = [];
var stylesheetNames = Object.keys(stylesheets);
stylesheetNames.forEach(function(key) {
    var stylesheetTask = "styles-" + key;
    stylesheetTasks.push(stylesheetTask);
    gulp.task(stylesheetTask, function() {
        return gulp.src(stylesheets[key])
                   .pipe(concat(key + ".min.css"))
                   .pipe(
                       autoprefixer({
                           browsers: ["> 0.2%", "ie 8-11"],
                           remove: false,
                       })
                   )
                   .pipe(
                       minifyCss({
                           compatibility: "ie8",
                       })
                   )
                   .pipe(gulp.dest("assets/css/"));
    });
});
gulp.task("styles", stylesheetTasks);

gulp.task("sass", function() {
    return gulp.src("assets/css/trading-tracker/style.scss")
               .pipe(sass().on("error", sass.logError))
               .pipe(gulp.dest("assets/css/trading-tracker"));
});
// Watch Files For Changes
gulp.task("watch", function() {
    gulp.watch("assets/css/trading-tracker/**/*.scss", ["sass"]);
});

gulp.task("store-version", function() {
    var fileName = "assets/version.txt";

    // Try to get current branch name
    exec("git branch | grep \\* | cut -d ' ' -f2", function(branchNameErr, branchName, branchNameStderr) {

        // If name found store in text file
        // If current branch if master we used use tags (As most likely this is in production environment)
        // Else it is one of dev branches so display branch name
        if (branchName && branchName !== "null" && branchName.trim() !== "master") {
            fs.writeFile(fileName, branchName.trim());
        }
        else {
            // Else just log errors & try to store latest tag name string in text file
            console.log(branchNameErr);
            console.log(branchName);
            console.log(branchNameStderr);

            // Try and get the latest tag on current branch
            exec("git describe --abbrev=0 --tags\n", function(tagNameErr, tagName, tagNameStderr) {

                // If found store in text file
                if (tagName && tagName !== "null") {
                    fs.writeFile(fileName, tagName.trim());
                }
                else {
                    // Else log any errors
                    console.log(tagNameErr);
                    console.log(tagName);
                    console.log(tagNameStderr);

                    // Else drop back to branch name if exists else remove version value from file
                    if (branchName && branchName !== "null") {
                        fs.writeFile(fileName, branchName.trim());
                    }
                    else {
                        fs.writeFile(fileName, "");
                    }
                }
            });
        }
    });
});

gulp.task("default", ["scripts", "styles"]);
