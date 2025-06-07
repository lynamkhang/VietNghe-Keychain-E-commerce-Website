<?php
require_once __DIR__ . '/../config/db.php';

class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';

    public function __construct() {
        global $db;
        if (!isset($db)) {
            throw new Exception("Database connection not established");
        }
        $this->db = $db;
    }

    public function findAll() {
        $sql = "SELECT * FROM {$this->table}";
        $result = $this->db->query($sql);
        if (!$result) {
            return [];
        }
        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    public function create($data) {
        $columns = implode(', ', array_keys($data));
        $values = "'" . implode("', '", array_map([$this->db, 'real_escape_string'], array_values($data))) . "'";
        
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($values)";
        if ($this->db->query($sql)) {
            return $this->db->insert_id;
        }
        return false;
    }

    public function update($id, $data) {
        $set = array();
        foreach ($data as $key => $value) {
            $set[] = "$key = '" . $this->db->real_escape_string($value) . "'";
        }
        $set = implode(', ', $set);
        
        $sql = "UPDATE {$this->table} SET $set WHERE {$this->primaryKey} = " . intval($id);
        return $this->db->query($sql);
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = " . intval($id);
        return $this->db->query($sql);
    }

    public function beginTransaction() {
        return $this->db->query("START TRANSACTION");
    }

    public function commit() {
        return $this->db->query("COMMIT");
    }

    public function rollback() {
        return $this->db->query("ROLLBACK");
    }

    public function count() {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $result = $this->db->query($sql);
        if (!$result) {
            return 0;
        }
        $row = $result->fetch_assoc();
        return $row['count'];
    }
} 