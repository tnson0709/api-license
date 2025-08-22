
<?php
class User {
    private $conn;
    private $table_name = "user";

    public function __construct($db){
        $this->conn = $db;
    }

    public function create($data){
        $query = "INSERT INTO " . $this->table_name . " SET
            user_id=:user_id, first_name=:first_name, last_name=:last_name, email=:email, mobile=:mobile,
            password=:password, is_two_fa=:is_two_fa, role=:role, token=:token, two_factor_secret=:two_factor_secret,
            created_at=:created_at, updated_at=:updated_at, count_login=:count_login, last_login=:last_login";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($data);
        return $stmt;
    }

    public function read(){
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function update($data){
        $query = "UPDATE " . $this->table_name . " SET
            first_name=:first_name, last_name=:last_name, email=:email, mobile=:mobile,
            password=:password, is_two_fa=:is_two_fa, role=:role, token=:token, two_factor_secret=:two_factor_secret,
            updated_at=:updated_at, count_login=:count_login, last_login=:last_login
            WHERE user_id=:user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($data);
        return $stmt;
    }

    public function delete($user_id){
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id=:user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        return $stmt;
    }
}
?>