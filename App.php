<?php

class App {

    const LIVE_DOMAIN = "https://tradingtracker.000webhostapp.com/";

    const APP_NAME = "Trading Tracker";

    const DEFAULT_ASSET_VERSION = "1";

    private static $instance;

    private $liveURL = "";
    private $localURL = "";
    private $requestRelativeURL = "";

    public static function get() {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getRequestRelativeURL() {
        if (empty($this->requestRelativeURL)) {
            $requestedRelativeURL = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "";
            $requestedRelativeURL = parse_url($requestedRelativeURL, PHP_URL_PATH);
            $requestedRelativeURL = trim($requestedRelativeURL, " /");

            if (!empty($requestedRelativeURL)) {
                $requestedRelativeURL .= "/";
            }

            $requestedRelativeURL = "/{$requestedRelativeURL}";

            $this->requestRelativeURL = $requestedRelativeURL;
        }

        return $this->requestRelativeURL;
    }

    public function getLiveURL() {
        if (empty($this->liveURL)) {
            $liveURL = rtrim(self::LIVE_DOMAIN, " /");
            $liveURL .= $this->getRequestRelativeURL();

            $this->liveURL = $liveURL;
        }

        return $this->liveURL;
    }

    public function getLocalURL() {
        if (empty($this->localURL)) {

            $protocol = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "off") ? "https" : "http";

            $localURL = "{$protocol}://" . rtrim($_SERVER["SERVER_NAME"], " /");

            $localURL .= $this->getRequestRelativeURL();
            $this->localURL = $localURL;
        }

        return $this->localURL;
    }

    public static function addVersion($src, $ver = false) {
        if (!$ver) {
            $root = rtrim($_SERVER["DOCUMENT_ROOT"], "/");
            $file = $root . "/" . ltrim($src, "/");

            $ver = self::DEFAULT_ASSET_VERSION;
            if (file_exists($file)) {
                $ver = date("mdYHi", filemtime($file));
            }
        }

        echo "{$src}?v={$ver}";
    }

    public static function isDebug() {
        $isDebug = isset($_GET["debug"]) && !($_GET["debug"] === "false" || $_GET["debug"] === "0");

        return $isDebug;
    }

}

$app = App::get();
