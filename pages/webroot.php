<?php
$html = '';

if (isset($_POST['webroot'])) {
    if (!empty($_POST['webroot'])) {
        $newSubdirectory = redaxo_url_rewrite\URLManager::convertValidURL($_POST['webroot']);
        $newSubdirectory = str_replace('%2F', '/', $newSubdirectory);
        rex_config::set($this->getName(), 'redaxo_root', $newSubdirectory);

        $html .= "<div class=\"alert alert-success\">";
        $html .= "<p><b>Unterverzeichnis (\"" . $newSubdirectory . "\") wurde erfolgreich als Wurzelverzeichnis gesetzt!</p>";
        $html .= "</div>";
    } else {
        $html .= "<div class=\"alert alert-danger\">";
        $html .= "<p><b>Unterverzeichnis (\"" . $_POST['webroot'] . "\") ist nicht valide!</p>";
        $html .= "</div>";
    }
}
// rex_addon::get('redaxo_url_rewrite') is the alternative among other to $this in install.php
$currentSubdirectory = rex_config::get($this->getName(), 'redaxo_root', '/');

echo $html;
?>

<p>Hier kann das Unterverzeichnis konfiguriert werden, wenn sich die Redaxo Installation nicht im Wurzel Verzeichnis (/) befinden sollte.</p>
<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
    <label>
        Unterverzeichnis:
        <input for="webroot" type="text" name="webroot" class="form-control" placeholder="<?php echo $currentSubdirectory; ?>" />
    </label>
    <button id="webroot" name="htaccess" type="submit" class="btn btn-success">
        Übernehmen
    </button>
</form>