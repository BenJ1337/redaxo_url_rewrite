<?php
$html = "<h1>".rex_i18n::msg('easyurlrewrite_page_konfiguration_titel')."</h1>";
$html .= "<p>".rex_i18n::msg('easyurlrewrite_page_konfiguration_text_1')."</p>";
$html .= "<p>".rex_i18n::msg('easyurlrewrite_page_konfiguration_text_2')."</p>";
$html .= "<form action='".$_SERVER['REQUEST_URI']."' method='post'><button name='htaccess' type='submit'>".rex_i18n::msg('easyurlrewrite_page_konfiguration_button_konfiguration_hinterlegen')."</button> </form>";

if(isset($_POST["htaccess"])) {
    rex_file::copy(rex_path::frontend('.htaccess'), rex_path::frontend('htaccess_backup.txt'));
    rex_file::copy(rex_path::addon('easyurlrewrite', 'var/.htaccess'), rex_path::frontend('.htaccess'));
    rex_delete_cache();
    $html .= "<p style='color: #079a07'>".rex_i18n::msg('easyurlrewrite_page_konfiguration_erfolgtext_konfiguration_hinterlegen')."</p>";
}

echo $html;
