<?php
class KategorieDao extends BaseDao {
    function __construct() {
        parent::__construct();
    }

    public function findAll() {
        $this->dbc->setDBQuery("SELECT DISTINCT id, catname, path, status, clang_id
                                      FROM rex_article  
                                      WHERE catname != ''
                                      ORDER BY path;");
        $kategorieMap = $this->dbc->getArray();
        $kategorieList = array();
        foreach ($kategorieMap as $key => $value) {
            $kategorieList[] = new Kategorie($value['id'], $value['status'], $value['catname'],
                                            $value['clang_id'], $value['path']);
        }
        return $kategorieList;
    }
}
