<?php
/**
 * Class data_update
 * Skrevet af Heinz K Aug 30 2018
 *
 * Klasse der kan hente, parse og update database med faglokale data fra csv fil
 */
class mh_getdata {

    //Prop for DB Klasse
	private $db;

	//Prop for datafil med csv data
    private $datafile;

    //Prop for brugernavn + password til fil placering
    private $username;
    private $password;

    //Prop til key names på data array
    private $keys;

    //Prop for data array
    private $arr_data;

    //Prop for sql
    private $sql;

    //Prop for activity table
	private $dbtable;

	/**
	 * data_update constructor.
	 */
    public function __construct() {
    	//Gør db klasse til member
        global $db;
        $this->db = $db;
        $this->dbtable = "mh_activity";

        //Sætter url og creds til csv fil
        $this->datafile = "https://www.uddata.dk/download/Z-dat/aalbots/lokastat/Lokastat.csv";
        $this->username = "heka";
        $this->password = "43296Leicht";

        //Definerer data keys
        $this->keys = ["name", "date", "time", "subject", "class", "num_room", "num_class"];


        //
        $this->arr_data = [];

    }

	/**
	 * Kører alle metoder
	 * @return bool
	 */
    public function run_update() {
    	if($data = $this->fetch_file()) {
			return $this->updatedb($data);
	    }
    }

	/**
	 * Henter fil ved brug af cUrl
	 * @return array|bool
	 */
    private function fetch_file() {
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $this->datafile);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_HEADER, false);
	    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	    curl_setopt($curl, CURLOPT_FAILONERROR, true);
	    curl_setopt($curl, CURLOPT_USERPWD, "$this->username:$this->password");
	    $data = curl_exec($curl);
	    curl_close($curl);
	    if($data) {
		    return $this->parse_data($data);
	    }
    }

	/**
	 * Parser data til db venligt array med named keys
	 * @param $data
	 *
	 * @return array|bool
	 */
    private function parse_data($data) {
    	$arr_data = [];
	    foreach ( str_getcsv( $data, "\n" ) as $key => $array_values ) {
		    $array_values         = array_combine( $this->keys, str_getcsv( $array_values, ";" ) );
		    $array_values["date"] = $this->date2dbformat( $array_values["date"] );
		    $array_values["time"] = $this->time2dbformat( $array_values["time"] );
		    $array_values["timestamp"] = $this->date2timestamp( $array_values["date"], $array_values["time"] );
		    $arr_data[]     = $array_values;
	    }
	    if(is_array($arr_data)) {
			return $arr_data;
	    } else {
	    	return false;
	    }
    }


	/**
	 * Konverterer csv dato til timestamp
	 * @param $date
	 *
	 * @return false|string
	 */
	private function date2timestamp($date, $time) {
		$date = explode("-", $date);
		$time = explode(":", $time);
		return mktime($time[0],$time[1], 0, $date[1], $date[2], $date[0]);
	}

	/**
	 * Konverterer csv dato til dbvenligt format
	 * @param $date
	 *
	 * @return false|string
	 */
    private function date2dbformat($date) {
	    $date = explode("-", $date);
	    return date("Y-m-d", mktime(0,0, 0, $date[1], $date[0], $date[2]));
    }

	/**
	 * Konverterer csv time til dbvenligt format
	 * @param $time
	 *
	 * @return false|string
	 */
	private function time2dbformat($time) {
		$time = explode(":", $time);
		return date("H:i:s", mktime($time[0],$time[1], 0, 0,0,0));
	}

	/**
	 * Tømmer tabel med metode truncate og updater derefter med friske data
	 * @param $data
	 *
	 * @return bool|mixed
	 */
	private function updatedb($data) {

		$this->sql = "TRUNCATE TABLE " . $this->dbtable;
		$this->db->_query($this->sql);

	    //Start SQL insert statement
	    $this->sql = "INSERT INTO " . $this->dbtable . "(vcSubject, vcClassroom, vcClass, daTime) VALUES ";

	    //Definerer array til insert values
	    $arr_inserts = [];

	    foreach ( $data as $key => $arr_values ) {
		    $arr_inserts[] = "(" .
		                     "'" . $arr_values["subject"] . "'," .
		                     "'" . $arr_values["name"] . "'," .
		                     "'" . $arr_values["class"] . "'," .
		                     "'" . $arr_values["timestamp"] . "'" .
		                     ")";
	    }

	    //Bygger SQL statement
	    $this->sql .= implode( ", ", $arr_inserts );

	    //Eksekverer SQL
	    return $this->db->_query(mb_convert_encoding($this->sql, "UTF-8", "ISO-8859-1"));
    }

}