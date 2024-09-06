<?php
include 'connect-db.php';
session_start();

$email = $_POST['email'];
$password = md5($_POST['password']);

$query = $db->prepare("SELECT * FROM users where email=:email and password=:password");

$query->execute(array(
    'email' => $email,
    'password' => $password
));
$user = $query->fetch();

if ($user) {
    if ($user['approval'] == 1) {
        $_SESSION['user_mail'] = $email;
        header("Location:themes/admin/index.php");
    } elseif ($user['approval'] == 0) {
        header("Location:index.html?status=waitingapproval");
    } else {
        header("Location:index.html?status=unapproved");
    }
} else {
    header("Location:index.html?status=incorrectuser");
}
