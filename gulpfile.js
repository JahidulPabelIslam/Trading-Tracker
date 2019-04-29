const gulp = require("gulp");

const concat = require("gulp-concat");
const uglify = require("gulp-uglify");
const cleanCSS  = require("gulp-clean-css");
const autoprefixer = require("gulp-autoprefixer");
const sass = require("gulp-sass");

const fs = require("fs");

const { exec } = require("child_process");

let defaultTasks = [];

// Concatenate & Minify JS
const scripts = {
    main: [
        "assets/js/third-party/jquery-3.2.1.min.js",
        "assets/js/third-party/popper.min.js",
        "assets/js/third-party/angular.min.js",
        "assets/js/third-party/decimal.min.js",
        "assets/js/third-party/bootstrap.min.js",
    ],
};

let scriptsTasks = [];
const scriptNames = Object.keys(scripts);
scriptNames.forEach(function(key) {
    const scriptTask = "scripts-" + key;
    scriptsTasks.push(scriptTask);
    gulp.task(scriptTask, function() {
        return gulp.src(scripts[key])
                   .pipe(concat(key + ".min.js"))
                   .pipe(uglify())
                   .pipe(gulp.dest("assets/js/"));
    });
});
gulp.task("scripts", gulp.parallel(scriptsTasks));
defaultTasks.push("scripts");

// Minify Stylesheets
const stylesheets = {
    main: [
        "assets/css/trading-tracker/style.css",
    ],
};

let stylesheetTasks = [];
const stylesheetNames = Object.keys(stylesheets);
stylesheetNames.forEach(function(key) {
    const stylesheetTask = "styles-" + key;
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
                       cleanCSS({
                           compatibility: "ie8",
                       })
                   )
                   .pipe(gulp.dest("assets/css/"));
    });
});
gulp.task("styles", gulp.parallel(stylesheetTasks));
defaultTasks.push("styles");

gulp.task("sass", function() {
    return gulp.src("assets/css/trading-tracker/style.scss")
               .pipe(sass().on("error", sass.logError))
               .pipe(gulp.dest("assets/css/trading-tracker"));
});
// Watch Files For Changes
gulp.task("watch", function() {
    gulp.watch("assets/css/trading-tracker/**/*.scss", gulp.parallel(["sass"]));
});

const errorCallback = function(err) {
    if (err) {
        console.log(err);
    }
};

const runCommand = function(command, callback) {
    return exec(command, function(err, res, stdErr) {
        // If found store in text file
        if (res && res.trim() !== "null") {
            callback(res.trim());
            return;
        }
        // Else log any errors
        console.log(err, res, stdErr);
        callback(null);
    });
};

defaultTasks.push("store-version");
gulp.task("store-version", function() {
    const githubBaseUrl = "https://github.com/jahidulpabelislam/trading-tracker/";
    const fileName = "assets/version.txt";
    let versionText = "";

    // Try to get current branch name
    return runCommand("git branch | grep \\* | cut -d ' ' -f2", function(branchName) {
        /*
         * If name found store in text file
         * If current branch if master we used use tags (As most likely this is in production environment)
         * Else it is one of dev branches so display branch name
         */
        if (branchName && branchName !== "master") {
            versionText = `<a href="${githubBaseUrl}tree/${branchName}/" target="_blank">${branchName}</a>`;
            fs.writeFile(fileName, versionText, errorCallback);
            return;
        }

        // Try and get the latest tag on current branch
        return runCommand("git describe --abbrev=0 --tags", function(tagName) {
            // If found store in text file
            if (tagName) {
                versionText = `<a href="${githubBaseUrl}releases/tag/${tagName}/" target="_blank">${tagName}</a>`;
            }
            // Else drop back to branch name if exists else remove version value from file
            else if (branchName) {
                versionText = `<a href="${githubBaseUrl}tree/${branchName}/" target="_blank">${branchName}</a>`;
            }

            fs.writeFile(fileName, versionText, errorCallback);
        });
    });
});

gulp.task("default", gulp.parallel(defaultTasks));
