<?php

if (!rex::isBackend()) {
    rex_extension::register('URL_REWRITE', function (rex_extension_point $ep) {
        $myclass = MyClass::getInstance();
         return $myclass->rewriteURL($ep);
    }, rex_extension::EARLY);

    rex_extension::register('PACKAGES_INCLUDED', function ($params) {
        $myclass = MyClass::getInstance();
        $myclass->mapURL2Article($params);
    }, rex_extension::EARLY);

}