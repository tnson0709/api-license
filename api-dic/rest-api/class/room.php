<?php
// api-dic/rest-api/class/room.php
class Room {
    private $conn;
    private $table_name = "room";

    public function __construct($db){
        $this->conn = $db;
    }

    public function create($data){
        $query = "INSERT INTO " . $this->table_name . " SET
            room_id=:room_id, room_code=:room_code, room_name=:room_name, room_address=:room_address,
            room_type=:room_type, room_price=:room_price, room_status=:room_status, room_unit=:room_unit,
            create_at=:create_at, updated_at=:updated_at, room_note=:room_note, room_create_by=:room_create_by";
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
            room_code=:room_code, room_name=:room_name, room_address=:room_address, room_type=:room_type,
            room_price=:room_price, room_status=:room_status, room_unit=:room_unit, updated_at=:updated_at,
            room_note=:room_note, room_create_by=:room_create_by
            WHERE room_id=:room_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($data);
        return $stmt;
    }

    public function delete($room_id){
        $query = "DELETE FROM " . $this->table_name . " WHERE room_id=:room_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":room_id", $room_id);
        $stmt->execute();
        return $stmt;
    }
}
?>