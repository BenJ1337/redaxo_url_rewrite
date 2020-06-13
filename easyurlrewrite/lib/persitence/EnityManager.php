<?php
class EnityManager {
    private $artikelDao;
    private $kategorieDao;
    private $spracheDao;

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
        $kategorienList = $this->kategorieDao->findAll();
        $artikelList = $this->artikelDao->findAll();

        $kategorienMap = array();
        foreach ($kategorienList as $kategorie) {
            $kategorie->setSprache($sprachenMap[$kategorie->getSprache()]);
            $kategorienMap[$kategorie->getId()][$kategorie->getSprache()->getId()] = $kategorie;
        }

        foreach ($kategorienMap as $sprachen) {
            foreach ($sprachen as $kategorie) {
                preg_match_all('/(\d+)/', $kategorie->getPath(), $matches);
                $parrentKatIds = $matches[0];
                if (!empty($parrentKatIds)) {
                    $katId = $parrentKatIds[sizeof($parrentKatIds) - 1];
                    $kat = $kategorienMap[$katId][$kategorie->getSprache()->getId()];
                    $kategorie->setVaterKategorie($kat);
                }
            }
        }

        $artikelURLMap = array();
        foreach ($artikelList as $artikel) {
            $artikel->setSprache($sprachenMap[$artikel->getSprache()]);
           if($kategorienMap[$artikel->getId()][$artikel->getSprache()->getId()] != null
                && $kategorienMap[$artikel->getId()][$artikel->getSprache()->getId()]->getSprache()->getId()
                        == $artikel->getSprache()->getId()) {
               $artikel->setKategorie($kategorienMap[$artikel->getId()][$artikel->getSprache()->getId()]);
           }
            $this->artikelId2URlMap[$artikel->getSprache()->getId()][$artikel->getId()] = $artikel;
            $this->artikelURL2IdMap[URLManager::convertValidURL($artikel->getSprache()->getCode())][URLManager::convertValidURL($artikel->getName())] = $artikel;
        }
    }

    private function getSprachenMap() {
        $sprachenList = $this->spracheDao->findAll();
        $sprachenMap = array();
        foreach ($sprachenList as $sprache) {
            $sprachenMap[$sprache->getId()] = $sprache;
        }
        return $sprachenMap;
    }

    public static function getInstance()
    {
        if (self::$instance === NULL) {
            self::$instance = new EnityManager();
        }
        return self::$instance;
    }

}
