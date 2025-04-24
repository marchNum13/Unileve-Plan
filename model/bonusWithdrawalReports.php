<?php
class bonusWithdrawalReports extends conn{
    
    // SET ATTRIBUTE TABLE NAME
    private $table_name = "bonus_withdrawal_report";
    
    // CREATE DEFAULT TABLE
    public function __construct(){
        // IF TABLE DOESN'T EXISTS, CREATE TABLE!`
        if($this->checkTable($this->table_name) == 0){
            // SET QUERY
            $sql = "CREATE TABLE $this->table_name (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id VARCHAR(6) NOT NULL,
                amount DOUBLE NOT NULL,
                address TEXT NOT NULL,
                status ENUM('pending','accepted','rejected') NOT NULL 'pending',
                date TEXT NOT NULL,
                FOREIGN KEY (user_id) REFERENCES user(user_id)
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
    public function read(string $fields, string $key, bool $relation = false, bool $is_member = false){
        // query
        if($relation){
            $sql = "SELECT $fields,
                    user.username AS username
                FROM $this->table_name AS bwr
                LEFT JOIN user ON bwr.user_id = user.user_id
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

    // delete data
    public function delete(string $key){
        // query
        $sql = "DELETE FROM $this->table_name WHERE $key";
        // EXECUTE THE QUERY
        $exe = $this->dbConn()->query($sql);
        // CLOSE THE CONNECTION
        $this->dbConn()->close();
        return $exe;
    }
}
?>