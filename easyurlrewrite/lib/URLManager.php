<?php

class URLManager
{

    private $em = null;
    private static $instance;
    private $urlIdMap;
    private $idUrlMap;

    function __construct()
    {
        $this->em = EnityManager::getInstance();
        $this->urlIdMap = array();
        $this->generateURLs();
    }

    private function generateURLs() {
        $url = "/";

        $artikel2URLMap = $this->em->getArtikelId2URlMap();

        foreach ($artikel2URLMap as $sprache) {
            foreach ($sprache as $artikel) {
                $aId = $artikel->getId();
                $cId = $artikel->getSprache()->getId();
                $url = "/";
                if($artikel != null) {
                    $url = $this::convertValidURL($artikel->getName()) . $url;
                    $kat = $artikel->getKategorie();
                    while($kat != null) {
                        $url = $this::convertValidURL($kat->getName()) . "/" . $url;
                        $kat = $kat->getVaterKategorie();
                    }
                    $url = $this::convertValidURL($artikel->getSprache()->getCode()) . "/" . $url;
                    $url = "/" . $url;
                }
                if(!empty($url)) {
                    $this->urlIdMap[$url]['aId'] = $aId;
                    $this->urlIdMap[$url]['cId'] = $cId;
                    $this->idUrlMap[$cId][$aId] = $url;
                }
            }
        }
    }

    public function getURL($aId, $cId)
    {
        $url = $this->idUrlMap[$cId][$aId];
        if($url != null) {
            $this->urlIdMap[$url]['aId'] = $aId;
            $this->urlIdMap[$url]['cId'] = $cId;
            return $url;
        }
    }

    public function getArtikelId($url) {
        if($url === "/" || $url === "") {
            return rex_addon::get('structure')->getProperty('start_article_id', 1);
        }
        return $this->urlIdMap[$url]['aId'];
    }

    public function getSpracheId($url) {
        if($url === "/" || $url === "") {
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

    public static function convertValidURL($text) {
        $validUrl = "";
        $sonderzeichen = array("Ä" => "Ae", "Ö" => "Oe", "Ü" => "Ue", "ä" => "ae", "ö" => "oe", "ü" => "ue", " " => "-", "." => "-");
        $validUrl = strtr($text, $sonderzeichen);

        return urlencode($validUrl);
    }
}