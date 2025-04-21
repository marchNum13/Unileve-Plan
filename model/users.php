<?php
class users extends conn{
    
    // SET ATTRIBUTE TABLE NAME
    private $table_name = "user";
    
    // CREATE DEFAULT TABLE
    public function __construct(){
        // IF TABLE DOESN'T EXISTS, CREATE TABLE!`
        if($this->checkTable($this->table_name) == 0){
            // SET QUERY
            $sql = "CREATE TABLE $this->table_name (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id VARCHAR(6) NOT NULL UNIQUE,
                referral_code VARCHAR(6) NOT NULL UNIQUE,
                username VARCHAR(12) NOT NULL UNIQUE,
                email VARCHAR(250) NOT NULL UNIQUE,
                password TEXT NOT NULL,
                is_premium ENUM('true', 'false') NOT NULL DEFAULT 'false',
                user_upline_id VARCHAR(6) DEFAULT NULL,
                regist_date TEXT NOT NULL,
                FOREIGN KEY (user_upline_id) REFERENCES $this->table_name(user_id)
            )";
            // EXECUTE THE QUERY
            $this->dbConn()->query($sql);
            // CLOSE THE CONNECTION
            $this->dbConn()->close();
        }
    }

    // create data
    public function create(string $fields, string $value){
        // query
        $sql = "INSERT INTO $this->table_name ($fields) VALUE($value)";
        // EXECUTE THE QUERY
        $exe = $this->dbConn()->query($sql);
        // CLOSE THE CONNECTION
        $this->dbConn()->close();
        return $exe;
    }

    // read data
    public function read(string $fields, string $key, bool $relation = false){
        // query
        if($relation){
            $sql = "SELECT $fields, IFNULL(upline.username, 'none') AS upline_username 
                FROM $this->table_name
                LEFT JOIN $this->table_name AS upline 
                ON $this->table_name.user_upline_id = upline.user_id 
                WHERE $key";
        }else{
            $sql = "SELECT $fields FROM $this->table_name WHERE $key";
        }
        // EXECUTE QUERY
        $exe = $this->dbConn()->query($sql);
        // SET DATA FROM TABLE
        while($rows = $exe->fetch_assoc()){
            $data[] = $rows;
        }
        // GET DATA TABLE
        $result["data"] = $data;
        // GET NUMS ROW TABLE
        $result["row"] = $exe->num_rows;
         // CLOSE THE CONNECTION
        $this->dbConn()->close();
        return $result;
    }
    
    // update data
    public function update(string $dataSet, string $key){
        // query
        $sql = "UPDATE $this->table_name SET $dataSet WHERE $key";
        // EXECUTE THE QUERY
        $exe = $this->dbConn()->query($sql);
        // CLOSE THE CONNECTION
        $this->dbConn()->close();
        return $exe;
    }

    // authentication 
    public function auth(?string $key, ?string $param){
        $conn = $this->dbConn();
        /* create a prepared statement */
        $stmt = mysqli_prepare($conn, "SELECT COUNT(user_id), user_id, password, is_premium FROM $this->table_name WHERE $param = ? LIMIT 1");
        /* bind parameters for markers */
        mysqli_stmt_bind_param($stmt, "s", $key);
        /* execute query */
        mysqli_stmt_execute($stmt);
        /* bind result variables */
        mysqli_stmt_bind_result($stmt, $num, $user_id, $password, $is_premium);
        /* fetch value */
        mysqli_stmt_fetch($stmt);
        // close connection
        $conn->close();
        return [
            "num" => $num,
            "user_id" => $user_id,
            "password" => $password
        ];
    }

}
?>