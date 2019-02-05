<?php
class song extends crud {
    protected $dbTable = "song";
    public $arrColumns = array();
    public $arrLabels = array();
    public $arrValues = array();

    public function __construct() {
        parent::__construct($this->dbTable);
        $this->arrColumns["vcAudioFile"]["Formtype"] = parent::INPUT_FILE;
    }
        
    /**
     * Select matching rows from a specific query
     * @param string $strSelect
     * @return array Returns an array with the given data
     */
    public function getall() {     
        $strSelect = "SELECT s.iSongID, s.vcSongTitle, ar.vcArtistName, g.vcGenreTitle " . 
                        "FROM song s, artist ar, genre g " . 
                        "WHERE s.iArtistID = ar.iArtistID " . 
                        "AND s.iGenreID = g.iGenreID " . 
                        "AND s.iDeleted = 0";;
        return $this->db->_fetch_array($strSelect);
    } 
    
    /**
     * Select song by id
     * @param int $iItemID
     * @return array
     */
    public function getItem($iItemID) {
        $this->arrValues = parent::getItem($iItemID);
        
        $artist = new Artist();
        $artist->getItem($this->arrValues["iArtistID"]);
        $this->arrValues["artist"] = $artist->arrValues;
        foreach ($this->arrValues as $key => $value) {
            $this->$key = $value;
        }
    }
    
    /**
     * 
     */
    public function save() {
        return parent::saveItem();
    }
    
    /**
     * Method Delete
     */
    public function delete($iItemID) {
        parent::delete($iItemID);
    } 
    
}
