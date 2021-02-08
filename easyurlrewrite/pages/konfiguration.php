<?php
$html = '';

if(isset($_POST["htaccess"])) {
    $filename = 'htaccess_backup';
    $index = 0;
    while(file_exists(rex_path::frontend($filename . ($index++ < 1 ? '' : $index) . '.txt'))) {}
    rex_file::copy(rex_path::frontend('.htaccess'), rex_path::frontend($filename . ($index <= 1 ? '' : $index) . '.txt'));
    rex_file::copy(rex_path::addon('easyurlrewrite', 'var/.htaccess'), rex_path::frontend('.htaccess'));
    rex_delete_cache();
    $html .= "<div class=\"alert alert-success\">";
    $html .= "<p>".rex_i18n::msg('easyurlrewrite_page_konfiguration_erfolgtext_konfiguration_hinterlegen')."</p>";
    $html .= "<p>Der ursprüngliche Inhalt von .htaccess wurde in '".$filename . ($index <= 1 ? '' : $index) . '.txt'."' kopiert</p>";
    $html .= "</div>";
}

$html .= "<h1>".rex_i18n::msg('easyurlrewrite_page_konfiguration_titel')."</h1>";
$html .= "<p>".rex_i18n::msg('easyurlrewrite_page_konfiguration_text_1')."</p>";
$html .= "<p>".rex_i18n::msg('easyurlrewrite_page_konfiguration_text_2')."</p>";
$html .= "<form action='".$_SERVER['REQUEST_URI']."' method='post'>"
         ."<button name='htaccess' type='submit'>".rex_i18n::msg('easyurlrewrite_page_konfiguration_button_konfiguration_hinterlegen')."</button>"
         ."</form>";


echo $html;
