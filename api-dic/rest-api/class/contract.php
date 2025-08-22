<?php
// api-dic/rest-api/class/contract.php
class Contract {
    private $conn;
    private $table_name = "contract";

    public function __construct($db){
        $this->conn = $db;
    }

    public function create($data){
        $query = "INSERT INTO " . $this->table_name . " SET
            contract_id=:contract_id, contract_code=:contract_code, contract_name=:contract_name,
            customer_id=:customer_id, contract_type=:contract_type, contract_amount=:contract_amount,
            contract_status=:contract_status, contract_start_date=:contract_start_date, contract_exp=:contract_exp,
            contract_sign_date=:contract_sign_date, contract_create_date=:contract_create_date,
            updated_at=:updated_at, contract_note=:contract_note, contract_create_by=:contract_create_by";
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
            contract_code=:contract_code, contract_name=:contract_name, customer_id=:customer_id,
            contract_type=:contract_type, contract_amount=:contract_amount, contract_status=:contract_status,
            contract_start_date=:contract_start_date, contract_exp=:contract_exp, contract_sign_date=:contract_sign_date,
            contract_create_date=:contract_create_date, updated_at=:updated_at, contract_note=:contract_note,
            contract_create_by=:contract_create_by
            WHERE contract_id=:contract_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($data);
        return $stmt;
    }

    public function delete($contract_id){
        $query = "DELETE FROM " . $this->table_name . " WHERE contract_id=:contract_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":contract_id", $contract_id);
        $stmt->execute();
        return $stmt;
    }
}
?>