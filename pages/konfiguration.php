<?php

function copyHtaccess()
{
    $newHtaccessHinterlegt = rex_file::copy(rex_path::addon("redaxo_url_rewrite", "var/.htaccess"), rex_path::frontend(".htaccess"));
    if ($newHtaccessHinterlegt) {
        rex_delete_cache();
    }
    return $newHtaccessHinterlegt;
}

function backupHtaccess(&$filename, &$index)
{
    while (file_exists(rex_path::frontend($filename . ($index++ < 1 ? "" : $index) . ".txt"))) {
    }
    return rex_file::copy(rex_path::frontend(".htaccess"), rex_path::frontend($filename . ($index <= 1 ? "" : $index) . ".txt"));
}
?>
<?php

$html = "";
$filename = "htaccess_backup";
$index = 0;

if (isset($_POST["htaccess"])) {
    if (is_file(rex_path::frontend(".htaccess"))) {
        if (backupHtaccess($filename, $index)) {
            $html .= "<div class=\"alert alert-success\">";
            $html .= "<p><b>Erfolg:</b> Der ursprüngliche Inhalt von .htaccess wurde in \"" . $filename . ($index <= 1 ? "" : $index) . ".txt" . "\" kopiert</p>";
            $html .= "</div>";

            if (copyHtaccess()) {
                $html .= "<div class=\"alert alert-success\">";
                $html .= "<p><b>Erfolg:</b> " . rex_i18n::msg("page_konfiguration_erfolgtext_konfiguration_hinterlegen") . "</p>";
            } else {
                $html .= "<div class=\"alert alert-danger\">";
                $html .= "<p><b>Fehler:</b> .htaccess konnte nicht hinterlegt werden!</p>";
            }
            $html .= "</div>";
        } else {
            $html .= "<div class=\"alert alert-danger\">";
            $html .= "<p><b>Fehler:</b> Die aktuelle im Wurzelverzeichnis (Vaterverzeichnis von /redaxo) hinterlegte .htaccess konnte nicht kopiert/gesichert werden. Keine Änderungen vorgenommen</p>";
            $html .= "</div>";
        }
    } else {
        if (copyHtaccess()) {
            $html .= "<div class=\"alert alert-success\">";
            $html .= "<p>" . rex_i18n::msg("page_konfiguration_erfolgtext_konfiguration_hinterlegen") . "</p>";
        } else {
            $html .= "<div class=\"alert alert-danger\">";
            $html .= "<p><b>Fehler:</b> .htaccess konnte nicht hinterlegt werden!</p>";
        }
        $html .= "</div>";
    }
}

echo $html;

?>

<div class="panel panel-default">
    <div class="panel-body">
        <h1><?php echo rex_i18n::msg("page_konfiguration_titel") ?></h1>
        <div class="panel panel-default">
            <div class="panel-heading">Allgemeine Hinweise</div>
            <div class="panel-body">
                Das Addon baut auf dem Modul <span class="label label-default">mod_rewrite</span> vom Apache Webserver auf, durch das angefragte URLs umgeleitet werden können.<br>
                Das Addon schreibt die URLs anhand der Kategorie und Artikelnamen um. Der Link zum Artikel <span class="label label-default">Redaxo</span> in der Kategorie <span class="label label-default">CMS</span> erscheint als <span class="label label-default">/cms/redaxo/</span><br><br>
                Per default werden die URLs von HTTP Requests auf das Pfad im Dateisystem abgebildet, das hier jedoch nicht gewünscht ist, da die Pfade nicht auf Dateien referenzieren.<br>
                Deshalb sollen Anfragen immer an die index.php im Wurzelverzeichnis umgeleitet werden, wodurch diese Anfragen an das Redaxo-CMS verarbeitet werden können.<br>
                Jedoch sollen nicht alle URL umgeleitet werden! URLs die mit <span class="label label-default">/media/...</span> oder <span class="label label-default">/redaxo/...</span> beginnen dürfen nicht umgeleitet werden. <br><br>
                Die HTTP-Requests mit der URL <span class="label label-default">/media/...</span> beziehen sich auf die Bilder, welche vom Redaxo CMS unter diesem Pfad abgelegt wurden und somit auch im Dateisystem augelöst werden müssen.<br>
                HTTP-Requests mit der URL /redaxo/... beziehen sich auf das Backend, welches sonst nicht aufgerufen werden könnte, da das Frontend nicht zum Backend weiterleitet.
                Als weitere Ausnahme sind URLS zu nennen, die Punkte <span class="label label-default">.</span> enthalen, bspw. /index.php. Dadurch ist es mögliche sonstige Seiten außerhalb normalen "Besucher-Frontends" des Redaxo-CMS aufzurufen. <br>
                Dadurch kann beispielsweise die Rest-API funktionalität weiterhin benutzt werden.
                Damit dieses URL-Rewrite-Addon funktioniert, muss die .htaccess im Wurzelverzeichnis angepasst werden.
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">Inhalt der .htaccess zum selberkopieren</div>
            <div class="panel-body">
                <?php echo nl2br(htmlspecialchars(rex_file::get(rex_path::addon("redaxo_url_rewrite", "var/.htaccess"), 'Datei nicht gefunden.')));

                ?>
            </div>
        </div>
        <div class="panel panel-danger">
            <div class="panel-heading">
                Apache Webserver:
            </div>
            <div class="panel-body">
                Damit dieses Addon genutzt werden kann, muss das Modul "mod_rewrite" installiert und aktiviert sein.
                <a href="https://httpd.apache.org/docs/current/mod/mod_rewrite.html">siehe httpd.apache.org/docs/current/mod/mod_rewrite.html></a>
            </div>
        </div>
        <p><?php echo rex_i18n::msg("page_konfiguration_text_2") ?></p>

        <form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
            <button name="htaccess" type="submit" class="btn btn-success">
                <?php echo rex_i18n::msg("page_konfiguration_button_konfiguration_hinterlegen") ?>
            </button>
        </form>
    </div>
</div>