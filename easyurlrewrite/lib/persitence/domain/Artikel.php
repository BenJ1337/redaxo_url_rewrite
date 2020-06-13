<?php
class Artikel {
    private $id;
    private $kategorie;
    private $sprache;
    private $status;
    private $name;

    function __construct($id, $status, $name, $sprache) {
        $this->status = $status;
        $this->name = $name;
        $this->id = $id;
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
    public function getKategorie()
    {
        return $this->kategorie;
    }

    /**
     * @param mixed $kategorie
     */
    public function setKategorie($kategorie)
    {
        $this->kategorie = $kategorie;
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
}