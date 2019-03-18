<?php

class App {

    const LIVE_DOMAIN = "https://tradingtracker.000webhostapp.com/";

    const APP_NAME = "Trading Tracker";

    private static $instance = null;

    private $getData = "";
    private $postData = "";
    private $serverData = "";

    private $liveURL = "";
    private $localURL = "";
    private $requestRelativeURL = "";

    public function __construct() {
        $this->getData = $_GET ?? [];
        $this->postData = $_POST ?? [];
        $this->serverData = $_SERVER ?? [];
    }

    public static function get() {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getRequestRelativeURL() {
        if (empty($this->requestRelativeURL)) {
            $requestedRelativeURL = isset($this->serverData["REQUEST_URI"]) ? $this->serverData["REQUEST_URI"] : "";
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

            $protocol = (!empty($this->serverData["HTTPS"]) && $this->serverData["HTTPS"] != "off") ? "https" : "http";

            $localURL = "{$protocol}://" . rtrim($this->serverData["SERVER_NAME"], " /");

            $localURL .= $this->getRequestRelativeURL();
            $this->localURL = $localURL;
        }

        return $this->localURL;
    }

    public function addVersion($src, $ver = false) {
        if (!$ver) {
            $root = rtrim($this->serverData["DOCUMENT_ROOT"], "/");
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

    public function isDebug() {
        $isDebug = isset($this->getData["debug"]) && !($this->getData["debug"] === "false" || $this->getData["debug"] === "0");

        return $isDebug;
    }

}

$app = App::get();