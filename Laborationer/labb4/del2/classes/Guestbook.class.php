<?php

class Guestbook {

    private $conn;
    private $Username;
    private $Message;
    private $Date_Time;

    public function __construct($name = "", $msg = "", $dt = "") {
        $this->Username = $name;
        $this->Message  = $msg;
        $this->Date_Time = $dt;

        // Create DB connection directly inside the class
        $this->conn = new mysqli('localhost', 'root', '', 'labb4', '3306');
        if ($this->conn->connect_error) {
            die("DB error: " . $this->conn->connect_error);
        }
    }

    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    public function getname()      { return $this->Username; }
    public function getmessage()   { return $this->Message; }
    public function get_date_time(){ return $this->Date_Time; }

    public function addEntry($name, $message) {
        $dt   = date("Y-m-d H:i:s");
        $name    = $this->conn->real_escape_string($name);
        $message = $this->conn->real_escape_string($message);
        $sql  = "INSERT INTO guestbooktable (Username, Post, PostDate) VALUES ('$name', '$message', '$dt')";
        $this->conn->query($sql);
    }

    public function loadEntries() {
        $result = $this->conn->query("SELECT id, Username, Post, PostDate FROM guestbooktable ORDER BY id ASC");
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function delEntry($id) {
        $id  = (int)$id; // cast to int for safety
        $sql = "DELETE FROM guestbooktable WHERE id=$id";
        $this->conn->query($sql);
    }
}
