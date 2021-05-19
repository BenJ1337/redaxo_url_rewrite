<?php

namespace redaxo_url_rewrite;

class Kategorie {
    private $artikel;
    private $sprache;
    private $id;
    private $vaterKategorie;
    private $path;
    private $kinderKategorie;
    private $name;
    private $status;

    function __construct($id, $status, $name, $sprache, $path) {
        $this->status = $status;
        $this->name = $name;
        $this->id = $id;
        $this->path = $path;
        $this->sprache = $sprache;
    }

    /**
     * @return mixed
     */
    public function getArtikel()
    {
        return $this->artikel;
    }

    /**
     * @param mixed $artikel
     */
    public function setArtikel($artikel)
    {
        $this->artikel = $artikel;
    }

    /**
     * @return mixed
     */
    public function getSprache()
    {
        return $this->sprache;
    }

    /**
     * @param mixed $sprache
     */
    public function setSprache($sprache)
    {
        $this->sprache = $sprache;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getVaterKategorie()
    {
        return $this->vaterKategorie;
    }

    /**
     * @param mixed $vaterKategorie
     */
    public function setVaterKategorie($vaterKategorie)
    {
        $this->vaterKategorie = $vaterKategorie;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getKinderKategorie()
    {
        return $this->kinderKategorie;
    }

    /**
     * @param mixed $kinderKategorie
     */
    public function setKinderKategorie($kinderKategorie)
    {
        $this->kinderKategorie = $kinderKategorie;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }



}