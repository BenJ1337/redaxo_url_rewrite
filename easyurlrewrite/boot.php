<?php

if (!rex::isBackend()) {
    rex_extension::register('URL_REWRITE', function (rex_extension_point $rex_extension_point) {
        $myclass = URLRewrite::getInstance();
         return $myclass->rewriteURL($rex_extension_point);
    }, rex_extension::EARLY);

    rex_extension::register('PACKAGES_INCLUDED', function (rex_extension_point $rex_extension_point) {
        $myclass = URLRewrite::getInstance();
        $myclass->mapURL2Article($rex_extension_point);
    }, rex_extension::EARLY);

}