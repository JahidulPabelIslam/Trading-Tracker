<?php

function addVersion($src, $ver = false) {
    if (!$ver) {
        $root = rtrim($_SERVER["DOCUMENT_ROOT"], "/");
        $file = $root . "/" . ltrim($src, "/");

        if (file_exists($file)) {
            $ver = date("mdYHi", filemtime($file));
        }
        else {
            $ver = "1";
        }
    }
    echo "{$src}?v={$ver}";
}