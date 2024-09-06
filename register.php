<?php
include 'connect-db.php';

$name = htmlspecialchars($_POST['name']);
$surname = htmlspecialchars($_POST['surname']);
$email = htmlspecialchars(trim($_POST['email']));
$password = md5(htmlspecialchars(trim($_POST['password'])));

$query = $db->prepare("select * from users where email=:email");
$query->execute(array(
    'email' => $email
));

$count = $query->rowCount();

if ($count == 0) {

    $registeruser = $db->prepare("INSERT INTO users SET
					name=:name,
					surname=:surname,
					email=:email,
					password=:password,
                    approval=:approval,
                    status=:status,
                    profile_picture_path=:profile_picture_path
					");

    $insert = $registeruser->execute(array(
        'name' => $name,
        'surname' => $surname,
        'email' => $email,
        'password' => $password,
        'approval' => 0,
        'status' => 2,
        'profile_picture_path' => "images/profile-pictures/anonim-picture.jpg"
    ));

    if ($insert) {
        header("Location:index.html?status=waitingapproval");
    } else {
        header("Location:index.html?status=error");
    }

} else {
    header("Location:index.html?status=duplicate");
    exit();
}