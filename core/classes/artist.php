<?php
class artist extends crud {
    protected $dbTable = "artist";
    public $arrColumns = array();
    public $arrLabels = array();
    public $arrValues = array();

    public function __construct() {
        parent::__construct($this->dbTable);
        $this->arrColumns["vcImageUrl"]["Formtype"] = parent::INPUT_IMAGEEDITOR;
        $this->arrColumns["vcDocument"]["Formtype"] = parent::INPUT_FILEEDITOR;
    }
        
    /**
     * Select matching rows from a specific query
     * @param string $strSelect
     * @return array Returns an array with the given data
     */
    public function getall() { 
        $this->arrLabels = array(
            "opts" => "Options", 
            "vcArtistName" => $this->arrColumns["vcArtistName"]["Label"]
        );        
        $strSelect = "SELECT * FROM artist WHERE iDeleted = 0 ORDER BY vcArtistName";
        return $this->db->_fetch_array($strSelect);
    } 
    
    /**
     * Select artist by id
     * @param int $iArtistID
     * @return array
     */
    public function getItem($iItemID) {
        $this->arrValues = parent::getItem($iItemID);        
    }
    
    /**
     * 
     */
    public function save() {
        return parent::saveItem();
    }
}
