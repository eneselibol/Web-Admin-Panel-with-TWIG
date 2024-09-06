<?php
require 'vendor/autoload.php';
include '../../connect-db.php';
session_start();

$loader = new \Twig\Loader\FilesystemLoader(['../../themes/admin']);
$twig = new \Twig\Environment($loader);
$twigarr = [];

$query = $db->prepare("SELECT * FROM users where email=:email");

$query->execute(array(
    'email' => $_SESSION['user_mail']
));

while ($user = $query->fetch()) {

    $twigarr['user_name'] = $user['name'];
    $twigarr['user_surname'] = $user['surname'];
    $twigarr['user_mail'] = $user['email'];
    $twigarr['user_status'] = $user['status'];
    $twigarr['profile_picture_path'] = $user['profile_picture_path'];

}

$querytu = $db->prepare("SELECT * FROM users ");
$querytu->execute();
$total_users = $querytu->fetchAll();

foreach ($total_users as $user) {
    if ($user['approval'] == 0) {
        $twigarr['waitingapproval_user_number'] += 1;
    } elseif ($user['approval'] == 1) {
        $twigarr['approved_user_number'] += 1;
    } else {
        $twigarr['notapproved_user_number'] += 1;
    }
}

$twigarr['total_users_number'] = count($total_users);

if ($twigarr['user_mail']) {
    echo $twig->render('index.html', $twigarr);
} else {
    header("Location:../../index.html");
    exit;
}