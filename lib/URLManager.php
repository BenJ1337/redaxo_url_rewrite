<?php

namespace redaxo_url_rewrite;

use rex_addon,

    rex_config;

class URLManager
{

    private $em = null;
    private static $instance;
    private $urlIdMap;
    private $idUrlMap;

    function __construct()
    {
        $this->em = CMSManager::getInstance();
        $this->urlIdMap = array();
        $this->idUrlMap = array();
        $this->generateURLs();
    }

    private function generateURLs()
    {
        $url = "/";
        $artikel2URLMap = $this->em->getArtikelId2URlMap();
        $sprachenMap = $this->em->getSprachenMap();

        foreach ($artikel2URLMap as $sprache) {
            foreach ($sprache as $artikel) {
                $aId = $artikel->getId();
                $cId = $artikel->getSprache()->getId();
                $url = "/";
                if ($artikel != null) {
                    $url = $this::convertValidURL($artikel->getName()) . $url;
                    if ($artikel->getKategorie() !== null && $artikel->getName() !== $artikel->getKategorie()->getName()) {
                        $kat = $artikel->getKategorie();
                        $prevKat = null;
                        while ($kat != null) {
                            if ($prevKat !== null && $prevKat->getName() !== $kat->getName()) {
                                $url = $this::convertValidURL($kat->getName()) . "/" . $url;
                            }
                            $prevKat = $kat;
                            $kat = $kat->getVaterKategorie();
                        }
                    }
                    if (!empty($sprachenMap) && sizeof($sprachenMap) > 1) {
                        $url = $this::convertValidURL($artikel->getSprache()->getCode()) . "/" . $url;
                    }

                    $url = "/" . $url;
                }
                if (!empty($url)) {
                    while (array_key_exists($url, $this->urlIdMap) || array_key_exists($url, $this->idUrlMap)) {
                        $url .= '-';
                    }
                    $url = strtolower($url);
                    $this->urlIdMap[$url]['aId'] = $aId;
                    $this->urlIdMap[$url]['cId'] = $cId;
                    $this->idUrlMap[$cId][$aId] = $url;
                }
            }
        }
    }

    public function getURL($aId, $cId)
    {
        $redaxoRoot = URLManager::getSubdirectory();
        while (str_ends_with($redaxoRoot, '/')) {
            $redaxoRoot = substr($redaxoRoot, 0, strlen($redaxoRoot) - 1);
        }
        if (isset($this->idUrlMap[$cId][$aId])) {
            $url = $this->idUrlMap[$cId][$aId];
            if ($url != null) {
                $this->urlIdMap[$url]['aId'] = $aId;
                $this->urlIdMap[$url]['cId'] = $cId;
                return $redaxoRoot . $url;
            } 
        }
        return $redaxoRoot . "/index.php?article_id=" . $aId . "&clang=" . $cId;
    }

    public static function getSubdirectory()
    {
        $redaxoRoot = rex_config::get(rex_addon::get('redaxo_url_rewrite')->getName(), 'redaxo_root', '/');
        if(empty($redaxoRoot) || $redaxoRoot === '/') {
            return '/';
        }
        if (!str_ends_with($redaxoRoot, '/')) {
            $redaxoRoot .= '/';
        }
        if (!str_starts_with($redaxoRoot, '/')) {
            $redaxoRoot = '/' . $redaxoRoot;
        }
        return $redaxoRoot;
    }

    public function getArtikelId($url)
    {
        $aId = -1;
        $redaxoRoot = URLManager::getSubdirectory();
        if ($url === $redaxoRoot || $url === "") {
            return rex_addon::get('structure')->getProperty('start_article_id', 1);
        } else if (isset($this->urlIdMap[$url]['aId'])) {
            $aId = $this->urlIdMap[$url]['aId'];
        } else if (isset($this->urlIdMap[$redaxoRoot . $url]['aId'])) {
            $aId = $this->urlIdMap[$redaxoRoot . $url]['aId'];
        } else {
            $tmpURL = $url;
            // dump($tmpURL);
            // dump($this->urlIdMap);
            while (!empty($tmpURL) && $aId == -1 && strpos($tmpURL, '/') !== false) {
                $indexFirstSlash = strpos($tmpURL, '/');
                $tmpURL = substr($tmpURL, $indexFirstSlash + 1);
                // dump($tmpURL);
                if (isset($this->urlIdMap['/' . $tmpURL]['aId'])) {
                    $aId = $this->urlIdMap['/' . $tmpURL]['aId'];
                }
            }
            if ($aId == -1) {
                //TODO Sprache abhängig von Prio
                $cId = 1;
                return rex_addon::get('structure')->getProperty('notfound_article_id', $cId);
            }
        }
        return $aId;
    }

    public function getSpracheId($url)
    {
        if ($url === "/" || $url === "" || !isset($this->urlIdMap[$url]['cId'])) {
            return 1;
        }
        return $this->urlIdMap[$url]['cId'];
    }


    public static function getInstance()
    {
        if (self::$instance === NULL) {
            self::$instance = new URLManager();
        }
        return self::$instance;
    }

    public static function convertValidURL($text)
    {
        $validUrl = "";
        $sonderzeichen = array("Ä" => "Ae", "Ö" => "Oe", "Ü" => "Ue", "ä" => "ae", "ö" => "oe", "ü" => "ue", " " => "-", "." => "-");
        $validUrl = strtr($text, $sonderzeichen);

        return urlencode($validUrl);
    }
}
