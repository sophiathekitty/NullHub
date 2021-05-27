<?php 
// exit if stand alone

if(realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
	exit;
if(!defined('MYSQL_CLASS')){
	define('MYSQL_CLASS',true);
	class clsDB{
		
		// class properties
		var $db;
		private $database;
		private $id;
		public $last_sql;
		
		public static $db_g;

		function __construct($dbname, $username, $password){
			$this->database = $dbname;
			$this->db = @mysqli_connect('localhost', $username, $password) 
				or die("Unable to connect to the DB server! ERROR: " . mysqli_errno($this->db) . " <b>" . mysqli_error($this->db) . "</b");
			mysqli_select_db($this->db, $dbname)
				or die("Unable to select DB! ERROR: " . mysqli_errno($this->db) . " <b>" . mysqli_error($this->db) . "</b");
			
			clsDB::$db_g = $this;
		}
		
		function _query($sql){
			//$sql = preg_replace("/;/","",$sql);
			$this->last_sql = $sql;
			$result = @mysqli_query($this->db, $sql);
			return $result;
		}
		
		function insert($sql){ // runs query without processing or returning results
			$result = $this->_query($sql);
			@$id = mysqli_insert_id($this->db);
			@mysqli_free_result($result);
			return $id;
		}
		
		function select($sql){ // runs query and returns results as an array.
			$data = array();
			$result = $this->_query($sql);
			try{
				while($row = mysqli_fetch_array($result)){
					// clean row...
					$row_clean = array();
					foreach($row as $key => $value)
						if(!is_integer($key))
							$row_clean[$key] = $value;
					$data[] = $row_clean;
				}
				mysqli_free_result($result);
			} catch (Exception $e){
				
			}
			return $data;
		}
		function where_safe_string($where){
			$regex = array("/\"/","/\'/");
			$replace = array("&quot;","&apos;");
			$where = preg_replace($regex,$replace,$where);
			$first = true;
			$sql = "";
			foreach($where as $key => $value){
				if(!$first) $sql .= " AND";
				$sql .= " `$key` = '$value'";
				$first = false;
			}
			return $sql;
		}
		function safe_select($table,$where = null, $order = null){
			// sanitize input
			$sql = "SELECT * FROM `$table`";
			if(!is_null($where)){
				$regex = array("/\"/","/\'/");
				$replace = array("&quot;","&apos;");
				$where = preg_replace($regex,$replace,$where);
				$sql .= " WHERE";
				$first = true;
				foreach($where as $key => $value){
					if(!$first) $sql .= " AND";
					$sql .= " `$key` = '$value'";
					$first = false;
				}
			}
			if(!is_null($order)){
				$sql .= " ORDER BY ";
				$first = true;
				foreach($order as $key => $value){
					if(!$first){
						$sql .= ", ";
					}
					$sql .= "`$key` $value";
					$first = false;
				}
			}
			$sql .= ";";
			//echo "SafeSelect ::: $sql";
			return $this->select($sql);
		}
		function safe_insert($table, $data, $where = null){ // generates a sanitized insert command 
			/*
				$date is required to be a keyed array. 
				
				array('first_name' => "sophia", 'last_name' => "daniels")
				
			*/
			
			// sanitize input
			$regex = array("/\"/","/\'/");
			$replace = array("&quot;","&apos;");
			//print_r($data);
			$data = preg_replace($regex,$replace,$data);
			//print_r($data);
			// generate sql
			$structure = ""; $values = ""; $i = 1;
			foreach($data as $key => $value){
				if($i++ < count($data)){
					$structure .= "`$key`,";
					if($value == "NOW()")
						$values .= "NOW(),";
					elseif(is_null($value) || $value == "")
						$values .= "NULL,";
					else
						$values .= "'$value',";
				} else {
					$structure .= "`$key`";
					if($value == "NOW()")
						$values .= "NOW()";
					else
						$values .= "'$value'";
				}
			}
			$where_txt = "";
			if(!is_null($where)){
				$where = preg_replace($regex,$replace,$where);
				$first = true;
				foreach($where as $key => $value){
					if(!$first) $where_txt .= " AND ";
					$where_txt .= "`$key` = '$value'";
					$first = false;
				}
			}
			if($where_txt == "")
				$sql = "INSERT INTO `$table` ($structure) VALUES ($values)";
			else 
				$sql = "REPLACE INTO `$table` ($structure) VALUES ($values) WHERE $where_txt";
			//echo "$sql\n";
			return $this->insert($sql);
		}
		function safe_update($table, $data, $where = NULL){ // generates a sanitized update command 
			/*
				$date is required to be a keyed array. 
				
				array('first_name' => "Carl", 'last_name' => "Sagan")
				
			*/
			
			// sanitize input
			$regex = array("/\"/","/\'/");
			$replace = array("&quot;","&apos;");
			$data = preg_replace($regex,$replace,$data);
			
			// generate sql
			$sql = "UPDATE `$table` SET \n";
			$c = 1;
			foreach($data as $key => $value){
				if(is_null($value) || $value == "")
					$sql .= "	`$key` = NULL";
				else
					$sql .= "	`$key` = '$value'";
				if($c++ < count($data))
					$sql .= ",";
				$sql .= " \n";
			}
			$sql .= "WHERE";
			if($where){
				$where = preg_replace($regex,$replace,$where);
				$first = true;
				foreach($where as $key => $value){
					if(!$first) $sql .= " AND";
					$sql .= " `$key` = '$value'";
					$first = false;
				}
			} else {
				$sql .= "`id` = '".$data['id']."'";
			}
			$sql .= ";";
			//echo $sql;
			return $this->insert($sql);
		}
		function last_insert(){
			return $this->id;
		}
		
		function get_err(){
			return mysqli_error($this->db);
		}
		function has_table($name){
			$sql = "SELECT COUNT(*)
FROM information_schema.tables 
WHERE table_schema = '$this->database' 
AND table_name = '$name';";
			$count = $this->select($sql);
			//print_r($count);
			if(isset($count[0][0]) && $count[0][0] == 1){
				return true;
			}
			if(isset($count[0]["COUNT(*)"]) && $count[0]["COUNT(*)"] == 1){
				return true;
			}
			return false;
		}
		function describe_table($name){
			return $this->select("DESCRIBE $name");
		}
		function install_table($name,$fields){
			$sql = "CREATE TABLE IF NOT EXISTS `$name` (\n";
			$first = true;
			foreach($fields as $field){
				if($first){
					$first = false;
					$sql .= $this->field_sql($field);
				} else {
					$sql .= ", \n".$this->field_sql($field);
				}
			}
			$sql .= ") ENGINE = InnoDB;";
			//echo $sql;
			$this->insert($sql);
			echo $this->get_err();
		}
		function add_field($table_name,$field,$after){
			$sql = "ALTER TABLE `$table_name` ADD ".$this->field_sql($field);
			if(!is_null($after) && $after != "") $sql .= " AFTER `$after`";
			$sql .= ";";
			$this->insert($sql);
		}
		function remove_field($table_name,$field){
			$sql = "ALTER TABLE `$table_name` DROP `".$field['Field']."`;";
			$this->insert($sql);
		}
		function update_field($table_name,$field){
			$sql = "ALTER TABLE `$table_name` CHANGE `".$field['Field']."` ".$this->field_sql($field).";";
			echo "$sql\n";
			$this->insert($sql);
		}
		function field_sql($field){
			$sql = "`".$field['Field']."` ".strtoupper($field['Type']);
			if(isset($field['Collate'])){
				$sql .= " ".$field['Collate'];
			}
			if($field['Null'] == "NO"){
				$sql .= " NOT NULL";
			} else {
				$sql .= " NULL";
			}
			if($field['Default'] != ""){
				if($field['Default'] == "current_timestamp()" || $field['Default'] == "current_timestamp"){
					$sql .= " DEFAULT CURRENT_TIMESTAMP";
				} elseif(is_null($field['Default']) || $field['Default'] == "NULL"){
					$sql .= " DEFAULT NULL";
				} else {
					$sql .= " DEFAULT '".$field['Default']."'";
				}
			}
			if($field['Extra'] == "auto_increment" || $field['Extra'] == "AUTO_INCREMENT"){
				$sql .= " AUTO_INCREMENT";
			}
			if($field['Extra'] == "on update current_timestamp()" || $field['Extra'] == "on update CURRENT_TIMESTAMP"){
				$sql .= " on update CURRENT_TIMESTAMP";
			}
			if($field['Key'] == "PRI"){
				$sql .= " PRIMARY KEY";
			}
			return $sql;
		}
		function create_table($name,$data,$comment,$set_defaults = false){
			// create the sql for this!!! :O
			$sql = "CREATE TABLE IF NOT EXISTS `$name` (\n";
			$sql .= " `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,\n";
			$sql .= " `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,\n";
			$sql .= " `modified` TIMESTAMP ";
			foreach($data as $key => $value){
				$sql .=", \n";
				$vType = gettype($value);
				// translate php types to sql types
				switch($vType){
					case "boolean":
						$type = 'BOOL';
						break;
					case "integer":
						$type = 'INT';
						break;
					case "double":
						$type = 'FLOAT';
						break;
					case "string":
						$type = 'VARCHAR( 100 )';
						break;
					default:
						// errors! omg
				}
				$sql .= " `$key` $type";
				if($set_defaults)
					$sql .= " NOT NULL DEFAULT  '$value'";
			}
			$sql .= ") ENGINE = MYISAM COMMENT =  '$comment';";
			$this->insert($sql);
		}
	
	}// end of class
} // end of defined

?>