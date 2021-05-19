<?php

namespace redaxo_url_rewrite;

use rex_sql;

class BaseDao {
    protected $dbc;

    function __construct()
    {
        $this->dbc = rex_sql::factory();
    }

}
