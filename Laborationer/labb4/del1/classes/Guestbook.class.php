<?php
 class Guestbook{

    private $Username;
    private $Message;
    private $Date_Time;

    public function __construct($name ="", $msg = "", $dt = ""){
        $this->Username = $name;
        $this->Message = $msg;
        $this->Date_Time = $dt;
    }
    public function __destruct(){}

    public function getname(){
        return $this ->Username;
    }    
    public function getmessage(){
        return $this->Message;
    }
    public function get_date_time(){
        return $this->Date_Time;
    }

 public function addEntry($name, $message){

    $file = "../../../../writeable/guestbook.txt";

    $entries = [];
    if (file_exists($file)) {
        $entries = unserialize(file_get_contents($file));
        if (!is_array($entries)) $entries = [];
    }

    $entries[] = [
        "name" => $name,
        "message" => $message,
        "date_time" => date("Y-m-d H:i:s")
    ];

    file_put_contents($file, serialize($entries));
}

public function delEntry($index){

    $file = "../../../../writeable/guestbook.txt";

    $entries = [];
    if (file_exists($file)) {
        $entries = unserialize(file_get_contents($file));
        if (!is_array($entries)) $entries = [];
    }

    if (isset($entries[$index])) {
        unset($entries[$index]);
        $entries = array_values($entries);
        file_put_contents($file, serialize($entries));
    }
}

}