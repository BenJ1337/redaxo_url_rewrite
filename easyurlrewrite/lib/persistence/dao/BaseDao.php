<?php
class BaseDao {
    protected $dbc;

    function __construct()
    {
        $this->dbc = rex_sql::factory();
    }

}
