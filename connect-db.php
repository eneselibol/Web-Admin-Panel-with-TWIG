<?php
try {
    $db = new PDO("mysql:host=localhost;dbname=webadminpanel;charset=utf8", 'username', 'password');
} catch (PDOExpception $e) {
    echo $e->getMessage();
}