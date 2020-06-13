<?php
class ArtikelDao extends BaseDao {
    function __construct() {
        parent::__construct();
    }

    public function findAll() {
        $this->dbc->setDBQuery("SELECT DISTINCT id, name, status, clang_id
                                      FROM rex_article  
                                      ORDER BY id;");
        $artikelMap = $this->dbc->getArray();
        $artikelList = array();
        foreach ($artikelMap as $key => $value) {
            $artikelList[] = new Artikel($value['id'], $value['status'], $value['name'],
                $value['clang_id']);
        }
        return $artikelList;
    }

}
