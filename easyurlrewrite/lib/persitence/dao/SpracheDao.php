<?php
class SpracheDao extends BaseDao {
    function __construct() {
        parent::__construct();
    }

    public function findAll() {
        $this->dbc->setDBQuery("SELECT id, code, name, status
                                FROM rex_clang  
                                ORDER BY id;");
        $sprachenMap = $this->dbc->getArray();
        $spachenList = array();
        foreach ($sprachenMap as $key => $value) {
            $spachenList[] = new Sprache($value['id'], $value['code'], $value['name'], $value['status']);
        }
        return $spachenList;
    }
}