<?php
session_start();
require_once("inc/commonFunction.inc");
define("DB_ERROR_NONE", 0);
define("DB_ERROR_CONNECTION", -1);

define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'www');
define('DB_USER', 'www');
define('DB_PASSWORD', 'www');

$sess = new PostgresDB(DB_HOST, DB_PORT, DB_USER, DB_PASSWORD, DB_NAME);

class PostgresDB {
	var $host;
	var $port;
	var $user;
	var $password;
	var $name;
	var $conn;

	function PostgresDB($host, $port, $user, $password, $name) {
		$this->host     = $host;
		$this->port     = $port;
		$this->user     = $user;
		$this->password = $password;
		$this->name     = $name;
		
		$this->connect();
		
		if ($this->conn) {
			$this->error = DB_ERROR_NONE;
		} else {
			$this->error = DB_ERROR_CONNECTION;
		}
	}

	function connect() {
		if ($this->conn) {
			pg_close($this->conn);
		}
		if ($this->host == 'localhost') {
			$this->conn = pg_connect("user=".$this->user." password=".$this->password." dbname=".$this->name);
		} else {
			$this->conn = pg_connect("host=".$this->host." port=".$this->port." user=".$this->user." password=".$this->password." dbname=".$this->name);
		}
	}

	function execSql($sql) {
		$ary_tmp = array();
		$i = 0;
	
		$result   =   pg_query($this->conn,$sql);
		while($row = @pg_fetch_object($result)) {
			$ary_tmp[$i] = $row;
			$i++;
		}
		//cleanUp($result,$dbconn);
	
		return $ary_tmp;
	}
	
	function cleanUp($result,$dbconn) {
		// free result
		pg_free_result($result); 
		
		// close db 
		pg_close($dbconn); 
	
		return 0;
	}
	
	function getVar($qstr=null,$x=0,$y=0) {
		$values = array();
		if($qstr) {
			$last_result = $this->execSql($qstr);
		}
		if($last_result[$y]) {
			$values = array_values(get_object_vars($last_result[$y]));
		}
	
		return (isset($values[$x]) && $values[$x]!=='') ? $values[$x] : null;
	}
	
	function getResult($qstr=null, $output = OBJECT) {
		if($qstr) {
			$last_result = $this->execSql($qstr);
		}
		if($output == OBJECT) {
			return $last_result;
		} elseif($output == ARRAY_A || $output == ARRAY_N) {
			if($last_result) {
				$i=0;
				foreach($last_result as $row) {
					$new_array[$i] = get_object_vars($row);
					if($output == ARRAY_N) {
						$new_array[$i] = array_values($new_array[$i]);
					}
					$i++;
				}
				return $new_array;
			} else {
				return null;
			}
		}
	}
}
?>