<?php

define('DEBUG', false);

class MySQL{

    private $host;
    private $username;
    private $password;
    private $database;
    private $conn_link;


    /*use:
        Syntax:
            var_name->db_connect(DATABASE_NAME);
        Example:
            $db->db_connect('user_data');
        
        Returns true/false        
    */
    function db_connect($database){
        $this->database = $database;
        if(!$this->conn_link){
            if(DEBUG)
            	echo 'Failed to connect to MySQL : ' . mysqli_connect_error();
            return false;
        }
        $dc = mysqli_select_db($this->conn_link, "$this->database");
        if(!$dc){
            mysqli_close($this->conn_link);
            unset($dc);
            if(DEBUG)
            	echo 'Unable to connect to database.';
            return false;
        }
        return true;
    }   

    /*use:
        Syntax:
            var_name = new MySQL(HOST NAME, USERNAME, PASSWORD);
        Example:
            $db = new MySQL('localhost', 'root', '');
            
        Returns true/false
    */
    function __construct($host, $username, $password){
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->conn_link = mysqli_connect("$this->host", "$this->username", "$this->password");
        if(!$this->conn_link){
            if(DEBUG)
            	echo 'Failed to connect to MySQL : ' . mysqli_connect_error();
            return false;
        }
        return true;
    }

    /*use:
        Syntax:
            var_name->create_table(TABLE_NAME, 'FIELD1 DATA TYPE(DATA_SIZE) [AUTO_INCREMENT] [PRIMARY KEY]','FIELD2 DATA TYPE(DATA_SIZE) [AUTO_INCREMENT] [PRIMARY KEY]'...);
        Example:
            $db->create_table('users', 'id int(6) AUTO_INCREMENT PRIMARY KEY', 'name varchar(50)', 'email varchar(150)');s
    
        Returns true/false        
    */
    function create_table(){
			$arg_num = func_num_args();
		$arguments = func_get_args();
		
		if(empty($arguments)){
            if(DEBUG)
            	echo 'Check the supplied arguments.';
			return false;
        }
		$sql = 'CREATE TABLE ' . $arguments[0] . '(';
		
		$arguments = array_slice($arguments, 1);
		if(empty($arguments)){
            if(DEBUG)
            	echo 'Check the supplied arguments.';
			return false;
        }
		$sql_args = [];

		foreach($arguments as $word)
			array_push($sql_args, $word);
		
		$sql .= implode(', ', $sql_args) . ')';
        if(mysqli_query($this->conn_link, $sql))
            return true;
        else{
            if(DEBUG){
            	echo 'Error creating MySQL table:' . mysqli_error($this->conn_link);
            	echo '<br>';
            	echo 'Your query : ' . $sql;
            }
            return false;
        }
        return false;
    }

    /*use:
        Syntax:
            var_name->alter_table(TABLE_NAME, 'DROP/ADD/MODIFY', 'FIELD1 DATA TYPE(DATA_SIZE) [AUTO_INCREMENT] [PRIMARY KEY]','FIELD2 DATA TYPE(DATA_SIZE) [AUTO_INCREMENT] [PRIMARY KEY]'...);
        Example:
            ADD column
                $db->alter_table('users', 'ADD', 'id int(6) AUTO_INCREMENT PRIMARY KEY);
            DROP column
                $db->alter_table('users', 'DROP', 'id');
            MODIFY column
                $db->alter_table('users', 'MODIFY', 'id int(10)');
        
        Returns true/false
    */
    function alter_table(){
		$arguments = func_get_args();
	
		if(empty($arguments)){
			if(DEBUG)
            	echo 'Check supplied arguments.';
			return false;
		}
		
		$sql = 'ALTER TABLE ' . $arguments[0] . ' ';
		$opt = strtoupper($arguments[1]);
		$arguments = array_slice($arguments, 2);
		
		if($opt == 'DROP'){
		$sql .= $opt . ' ';
			foreach($arguments as $args){
				$list = explode(' ', $args);
				if(sizeof($list) > 1){
					if(DEBUG)
            			echo 'Check supplied fields. DROP parameter shouldn\'t contain more than one field.';
					return false;
				}
			}
			
			$sql .= implode(', ', $arguments); 
		}else if($opt == 'ADD'){
			$sql .= $opt . ' ';
			foreach($arguments as $args){
				$list = explode(' ', $args);
				if(sizeof($list) <= 1){
					if(DEBUG)
            			echo 'Check supplied fields. ADD parameter should contain more than one field.';
					return false;
				}
			}
			$sql .= implode(', ', $arguments); 
		}else if($opt == 'MODIFY'){
			$sql .= $opt . ' ';
			foreach($arguments as $args){
				$list = explode(' ', $args);
				if(sizeof($list) <= 1){
					if(DEBUG)
            			echo 'Check supplied fields. MODIFY parameter should contain more than one field.';
					return false;
				}
			}
			$sql .= implode(', ', $arguments);
		}
		
        if(mysqli_query($this->conn_link, $sql))
            return true;
        else{
            if(DEBUG){
            	echo 'Error modifying MySQL table:' . mysqli_error($this->conn_link);
            	echo '<br>';
            	echo 'Your query : ' . $sql;
            }
            return false;
        }
        return false;
    }

    /*use:
        Syntax:
            var_name->drop_table(TABLE_NAME);
        Example:
            $db->drop_table('users');

        Returns true/false
    */
    function drop_table($table_name){
    	if(empty($table_name)){
    		if(DEBUG)
            	echo 'Check supplied arguments.';
    		return false;
    	} 	
    	$sql = 'DROP TABLE '. $table_name;
        if(mysqli_query($this->conn_link, $sql))
            return true;
        else{
            if(DEBUG){
            	echo 'Error dropping MySQL table:' . mysqli_error($this->conn_link);
            	echo '<br>';
            	echo 'Your query : ' . $sql;
           	}
           	return false;
        }
        return false;
    }

    /*use:
        Syntax:
            var_name->truncate_table(TABLE_NAME);
        Example:
            $db->truncate_table('users');

        Returns true/false
    */
    function truncate_table($table_name){
    	if(empty($table_name)){
    		echo 'Check supplied arguments.';
    		return false;
    	}
    	$sql = 'TRUNCATE TABLE '. $table_name;
        if(mysqli_query($this->conn_link, $sql))
            return true;
        else{
            if(DEBUG){
            	echo 'Error dropping MySQL table:' . mysqli_error($this->conn_link);
            	echo '<br>';
            	echo 'Your query : ' . $sql;
            }return false;
        }
        return false;
    }

    /*use:
        Syntax:
            var_name->insert(TABLE_NAME, '[field1, field2, field3...]', [value1, value2, value3...]);
        Example:
            $db->insert('users', 'name, roll, email', 'admin, 1, admin@example.com');

        Returns number of affected rows/false
    */
    function insert(){
		$arg_num = func_num_args();
    	$arguments = func_get_args();
            
		if($arg_num < 3){
			echo 'Check the supplied fields.';
			return false;
		}
		
		$sql = 'INSERT INTO ' . $arguments[0]. ' (' . $arguments[1]. ') VALUES ';
		
		$arguments = array_slice($arguments, 2);
		
		$sql_args = [];
		
		foreach($arguments as $row){
			$s = '(' . $row . ')';	
			array_push($sql_args, $s);
		}
		$sql .= implode(', ', $sql_args);

		if(mysqli_query($this->conn_link,$sql)){
            $res = mysqli_affected_rows($this->conn_link);
            if($res > 0)
                return $res;
            else{
            	if(DEBUG){
            		echo 'Error inserting into MySQL table:' . mysqli_error($this->conn_link);
            	    echo '<br>';
            	    echo 'Your query : ' . $sql;
            	}
            	return false;
            }
        }else{
        	if(DEBUG){   	
		        echo 'Error inserting into MySQL table:' . mysqli_error($this->conn_link);
		        echo '<br>';
		        echo 'Your query : ' . $sql;
            }
            return false;
        }
	}

    /*use:
        Syntax:
            var_name->select(TABLE_NAME, '[field1, field2, field3...]', CONDITION);
        Example:
            $db->select('users', 'name, roll, email', 'username = "admin"');
            $db->select('users', '*', 'username = "admin"');
            $db->select('users', 'name, email', 'username LIKE "%min"');
            $db->select('users', '*', 'username LIKE "%min" LIMIT 1');

        Returns associative array of the fetched row;
    */
    function select(){
		$arg_num = func_num_args();
    	$arguments = func_get_args();
            
		if($arg_num < 2){
			if(DEBUG)
            	echo 'Check the supplied fields.';
			return false;
		}
		
		$sql = 'SELECT ' . $arguments[1]. ' FROM ' . $arguments[0];
		
		if($arg_num == 3)
			$sql .= ' WHERE ' . $arguments[2];
		$result = mysqli_query($this->conn_link, $sql);
        if($result){
            $rows = [];
            while($row = mysqli_fetch_assoc($result))
                array_push($rows, $row);
            return $rows;
        }else{
            if(DEBUG){
            	echo 'Error in SELECT function in MySQL table:' . mysqli_error($this->conn_link);
            	echo '<br>';
            	echo 'Your query : ' . $sql;
            }
            return false;
        }
	}
	
    /*use:

        Syntax:
            var_name->delete(TABLE_NAME, CONDITION);
        Example:
            $db->delete('users', 'id > 5');
            $db->delete('users', 'id > 5 LIMIT 1');
        Returns number of affected rows/false
    */
    function delete(){
		$arg_num = func_num_args();
    	$arguments = func_get_args();
            
		if($arg_num < 2){
			if(DEBUG)
            	echo 'Check the supplied fields.';
			return false;
		}
			
		$sql = 'DELETE FROM ' . $arguments[0]. ' WHERE ' . $arguments[1];
		
        if(mysqli_query($this->conn_link,$sql)){
            $res = mysqli_affected_rows($this->conn_link);
            if($res > 0)
                return $res;
            else{
				if(DEBUG){
		        	echo 'No row found matching your condition.';
		            echo '<br>';
		            echo 'Your query : ' . $sql;
                }
                return false;
            }
        }else{
            if(DEBUG){
            	echo 'Error deleting from MySQL table:' . mysqli_error($this->conn_link);
		        echo '<br>';
		        echo 'Your query : ' . $sql;
            }
            return false;
        }
	}

    /*use:

        Syntax:
            var_name->update(TABLE_NAME, [field1, field2, field3...], [value1, value2, value3...], CONDITION);
        Example:
            $db->update('users', 'roll=5, score=100', 'id > 5');
            $db->update('users', 'roll=5, score=100', 'id > 5 LIMIT 1');

        Returns number of affected rows/false
    */
    function update(){
		$arg_num = func_num_args();
    	$arguments = func_get_args();
            
		if($arg_num < 2){
			if(DEBUG)
            	echo 'Check the supplied fields.';
			return false;
		}
			
		$sql = 'UPDATE ' . $arguments[0]. ' SET ' . $arguments[1];
		if($arg_num == 3)
			$sql .= ' WHERE ' . $arguments[2];
		
        if(mysqli_query($this->conn_link,$sql)){
            $res = mysqli_affected_rows($this->conn_link);
            if($res > 0)
                return $res;
            else{
				if(DEBUG){
		        	echo 'No row found matching your condition.';
		            echo '<br>';
		            echo 'Your query : ' . $sql;
		        }
                return false;
            }
        }else{
            if(DEBUG){
            	echo 'Error updating MySQL table:' . mysqli_error($this->conn_link);
		        echo '<br>';
		        echo 'Your query : ' . $sql;
            }
            return false;
        }
	}
}

?>
