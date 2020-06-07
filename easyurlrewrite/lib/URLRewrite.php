<?php

class URLRewrite
{
    private static $instance = null;
    private $articleId2UrlMap = [];
    private $url2ArticleIdMap = [];
    private $langMap = [];
    private $multilang = false;

    /**
     * Setup URLRewriter
     * URLRewrite constructor.
     * @throws rex_sql_exception
     */
    function __construct()
    {
        dump("EasyUrlRewrite-Addon active");

        $sql = rex_sql::factory();
        $sql->setDBQuery("SELECT r_article.id as a_id, r_article.name as a_name, r_article.catname as a_cat_name
                                , r_article.path as a_path, r_clang.code as cl_code, r_clang.id as cl_id, r_clang.status as cl_status
                                FROM rex_article r_article 
                                LEFT JOIN rex_clang r_clang ON (r_article.clang_id = r_clang.id)
                                WHERE r_clang.status = 1 
                                ORDER BY a_path");
        $tableMap = $sql->getArray();

        foreach ($tableMap as $key => $value) {
            $this->articleId2UrlMap[$value['cl_id']][$value['a_id']]['name'] = urlencode($this->umlauteumwandeln($value['a_name']));
            $this->articleId2UrlMap[$value['cl_id']][$value['a_id']]['cat_name'] = urlencode($this->umlauteumwandeln($value['a_cat_name']));
            $this->url2ArticleIdMap[$value['cl_code']][urlencode($this->umlauteumwandeln($value['a_name']))] = $value['a_id'];

            $parents = array_filter(
                explode("|", $value['a_path']),
                function ($value) {
                    return $value !== '';
                });

            if (isset($parents) && sizeof($parents) > 0) {
                $this->articleId2UrlMap[$value['cl_id']][$value['a_id']]['cat_ids'] = $parents;
            } else {
                $this->articleId2UrlMap[$value['cl_id']][$value['a_id']]['cat'] = [];
            }

            if (!in_array($value['cl_id'], $this->langMap)) {
                $this->langMap[$value['cl_id']]['code'] = $value['cl_code'];
                $this->langMap[$value['cl_id']]['status'] = $value['status'];
            }
        }
        if (sizeof($this->langMap) > 1) {
            $this->multilang = true;
        }

        dump($this->articleId2UrlMap);
        dump($this->url2ArticleIdMap);

    }

    /**
     * Rewrites URLs in html markup
     * @param $rex_extension_point {@link rex_extension_point}
     * @return mixed|string Path and optional GET-Parameters
     */
    public function rewriteURL($rex_extension_point)
    {
        if (isset($params['subject']) && $params['subject'] != '') {
            return $params['subject'];
        }
        $params = $rex_extension_point->getParams();
        $url = "";
        if ($this->multilang) {
            $url .= "/" . $this->langMap[$params['clang']]['code'];
        }

        if (isset($this->articleId2UrlMap[$params['clang']][$params['id']]['cat_ids'])
            && sizeof($this->articleId2UrlMap[$params['clang']][$params['id']]['cat_ids']) > 0) {

            foreach ($this->articleId2UrlMap[$params['clang']][$params['id']]['cat_ids'] as $key => $value) {
                $url .= "/" . $this->articleId2UrlMap[$params['clang']][$value]['cat_name'];

                $this->url2ArticleIdMap[$params['clang']][$params['id']]['cats'][]
                    = $this->articleId2UrlMap[$params['clang']][$value]['cat_name'];

            }
        }

        $url .= "/" . $this->articleId2UrlMap[$params['clang']][$params['id']]['name'];
        $params['subject'] = $url;

        // params
        $urlparams = '';
        if (isset($params['params'])) {
            $urlparams = rex_string::buildQuery($params['params'], $params['separator']);
        }

        return $url . ($urlparams ? '?' . $urlparams : '');
    }

    /**
     * Maps the path of the requested url to article_id and if necessary the clang_id
     * @param $rex_extension_point {@link rex_extension_point}
     */
    public function mapURL2Article($rex_extension_point)
    {
        // Pfad aus der URL entnehmen
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        dump($path);
        // Pfadabschnitte als Array
        $path_dirs = array_filter(
            explode("/", $path),
            function ($value) {
                return $value !== '';
            });
        dump($path_dirs);

        $clang_id = 1;
        // Bestimmen der Sprache / CLang aus der URl
        foreach ($this->langMap as $key => $value) {
            if ($this->multilang && $value['code'] == $path_dirs[1]) {
                $clang_id = $key;
                break;
            } else if ($value['status'] == 1) {
                $clang_id = $key;
                break;
            }
        }
        // Bestimmen der Artikel Id aus der URL
        if ($path == "/") {
            $article_id = rex_addon::get('structure')->getProperty('start_article_id', $clang_id);
        } else if ($this->multilang) {
            if(sizeof($path_dirs) === 1) {
                $article_id = rex_addon::get('structure')->getProperty('start_article_id', $clang_id);
            } else {
                $article_id = $this->url2ArticleIdMap[$path_dirs["1"]][$path_dirs[sizeof($path_dirs)]];
            }
        } else {
            if (isset($this->url2ArticleIdMap[$this->langMap[$clang_id]['code']][$path_dirs[sizeof($path_dirs)]])) {
                $article_id = $this->url2ArticleIdMap[$this->langMap[$clang_id]['code']][$path_dirs[sizeof($path_dirs)]];
            }
        }
        // Wenn URL keinem Artikel zugeordnet werden konnte
        if (!isset($article_id)) {
            $article_id = rex_addon::get('structure')->getProperty('notfound_article_id', $clang_id);
        }

        // Setzt die Sprache
        try {
            rex_clang::setCurrentId($clang_id);
        } catch (Exception $e) {
            exit("Sprache nicht gefunden. Bitte den Administrator informieren: " . rex::getErrorEmail() . ". Vielen Dank!");
        }

        // Setzt die Artikel Id
        if (isset($article_id)) {
            rex_addon::get('structure')->setProperty('article_id', $article_id);
        }
    }

    public static function getInstance()
    {
        if (self::$instance === NULL) {
            self::$instance = new URLRewrite();
        }
        return self::$instance;
    }

    private function umlauteumwandeln($str)
    {
        $tempstr = array("Ä" => "Ae", "Ö" => "Oe", "Ü" => "Ue", "ä" => "ae", "ö" => "oe", "ü" => "ue", " " => "-");
        return strtr($str, $tempstr);
    }
}