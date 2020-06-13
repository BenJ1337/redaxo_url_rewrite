<?php

if (!rex::isBackend()) {
    rex_extension::register('URL_REWRITE', function (rex_extension_point $rex_extension_point) {

        $um = URLManager::getInstance();

        $params = $rex_extension_point->getParams();
        $cId = $params['clang'];
        $aId = $params['id'];

        $url = $um->getURL($aId, $cId);
        if (!empty($url)) {
            return $url;
        }
    }, rex_extension::EARLY);

    rex_extension::register('PACKAGES_INCLUDED', function (rex_extension_point $rex_extension_point) {

        $getParamArtikelId = "article_id=";
        $getParamSpracheId = "clang=";
        $getParams = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        if (!strpos($getParams, $getParamArtikelId) !== false
            && !strpos($getParams, $getParamSpracheId) !== false) {

            $um = URLManager::getInstance();

            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $aId = $um->getArtikelId($path);
            $cId = $um->getSpracheId($path);
            if ($aId == null || $cId == null) {
                //TODO Sprache abhängig von Prio
                $cId = 1;
                $aId = rex_addon::get('structure')->getProperty('notfound_article_id', $cId);
            }

            try {
                rex_clang::setCurrentId($cId);
            } catch (Exception $e) {
                exit("Sprache nicht gefunden. Bitte den Administrator informieren: " . rex::getErrorEmail() . ". Vielen Dank!");
            }
            if ($aId) {
                rex_addon::get('structure')->setProperty('article_id', $aId);
            }
        }

    }, rex_extension::EARLY);

}

