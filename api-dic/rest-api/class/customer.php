<?php
// api-dic/rest-api/class/customer.php
class Customer {
    private $conn;
    private $table_name = "customer";

    public function __construct($db){
        $this->conn = $db;
    }

    public function create($data){
        $query = "INSERT INTO " . $this->table_name . " SET
            customer_id=:customer_id, customer_code=:customer_code, customer_name=:customer_name,
            customer_address=:customer_address, customer_type=:customer_type, district=:district,
            province=:province, tel=:tel, email=:email, taxcode=:taxcode, contract_code=:contract_code,
            sign_date=:sign_date, start_date=:start_date, expiry_date=:expiry_date, amount=:amount,
            amount_debit=:amount_debit, description=:description, note_last=:note_last, created_at=:created_at,
            updated_at=:updated_at, is_system=:is_system, inactive=:inactive, is_customer=:is_customer,
            is_supply=:is_supply";
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
            customer_code=:customer_code, customer_name=:customer_name, customer_address=:customer_address,
            customer_type=:customer_type, district=:district, province=:province, tel=:tel, email=:email,
            taxcode=:taxcode, contract_code=:contract_code, sign_date=:sign_date, start_date=:start_date,
            expiry_date=:expiry_date, amount=:amount, amount_debit=:amount_debit, description=:description,
            note_last=:note_last, updated_at=:updated_at, is_system=:is_system, inactive=:inactive,
            is_customer=:is_customer, is_supply=:is_supply
            WHERE customer_id=:customer_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($data);
        return $stmt;
    }

    public function delete($customer_id){
        $query = "DELETE FROM " . $this->table_name . " WHERE customer_id=:customer_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":customer_id", $customer_id);
        $stmt->execute();
        return $stmt;
    }
}
?>