<?php
class CMSManager {
    private $artikelDao;
    private $kategorieDao;
    private $spracheDao;

    private $sprachenMap;

    private $artikelId2URlMap;
    private $artikelURL2IdMap;

    private static $instance = null;

    function __construct() {
        $this->artikelDao = new ArtikelDao();
        $this->kategorieDao = new KategorieDao();
        $this->spracheDao = new SpracheDao();

        $this->artikelId2URlMap = array();
        $this->artikelURL2IdMap = array();
        $this->getCMSStructure();
    }

    public function getArtikelId2URlMap() {
        return $this->artikelId2URlMap;
    }

    public function getArtikelURL2IdMap() {
        return $this->artikelURL2IdMap;
    }

    private function getCMSStructure() {

        $sprachenMap = $this->getSprachenMap();
        $artikelList = $this->artikelDao->findAll();

        $kategorienMap = $this->getKategorienMap($sprachenMap);


        foreach ($kategorienMap as $sprachen) {
            foreach ($sprachen as $kategorie) {
                preg_match_all('/(\d+)/', $kategorie->getPath(), $matches);
                $vaterKatId = $matches[0];
                if (!empty($vaterKatId)) {
                    $katId = $vaterKatId[sizeof($vaterKatId) - 1];
                    $kat = $kategorienMap[$katId][$kategorie->getSprache()->getId()];
                    $kategorie->setVaterKategorie($kat);
                }
            }
        }

        $artikelURLMap = array();
        foreach ($artikelList as $artikel) {
            if(isset($sprachenMap[$artikel->getSprache()])) {
                $artikel->setSprache($sprachenMap[$artikel->getSprache()]);
                // Setze Kategorie, falls der Artikel einer untergeordnet ist.
                if($artikel->getSprache() != null
                        && isset($kategorienMap[$artikel->getId()][$artikel->getSprache()->getId()])
                       && $kategorienMap[$artikel->getId()][$artikel->getSprache()->getId()] != null
                       && $kategorienMap[$artikel->getId()][$artikel->getSprache()->getId()]->getSprache()->getId()
                            == $artikel->getSprache()->getId()) {
                   $artikel->setKategorie($kategorienMap[$artikel->getId()][$artikel->getSprache()->getId()]);
               }
               $this->artikelId2URlMap[$artikel->getSprache()->getId()][$artikel->getId()] = $artikel;
               $this->artikelURL2IdMap[URLManager::convertValidURL($artikel->getSprache()->getCode())][URLManager::convertValidURL($artikel->getName())] = $artikel;
            }
        }
    }

    public function getKategorienMap($sprachenMap) {
        $kategorienList = $this->kategorieDao->findAll();
        $kategorienMap = array();
        foreach ($kategorienList as $kategorie) {
            if(isset($sprachenMap[$kategorie->getSprache()])) {
                $kategorie->setSprache($sprachenMap[$kategorie->getSprache()]);
                if($kategorie->getSprache() != null) {
                    $kategorienMap[$kategorie->getId()][$kategorie->getSprache()->getId()] = $kategorie;
                }
            }
        }
        //dump($kategorienMap);
        return $kategorienMap;
    }

    public function getSprachenMap() {
        if(!isset($sprachenMap) && empty($sprachenMap)) {
            $sprachenList = $this->spracheDao->findAll();
            $sprachenMap = array();
            foreach ($sprachenList as $sprache) {
                $sprachenMap[$sprache->getId()] = $sprache;
            }
        }
        //dump($sprachenMap);
        return $sprachenMap;
    }

    public static function getInstance()
    {
        if (self::$instance === NULL) {
            self::$instance = new CMSManager();
        }
        return self::$instance;
    }
}