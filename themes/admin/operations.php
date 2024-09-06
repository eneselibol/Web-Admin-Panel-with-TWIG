<?php
require 'vendor/autoload.php';
include '../../connect-db.php';
ob_start();
session_start();

if (isset($_POST['sign-out'])) {
    session_destroy();
    header("Location:../../index.html?status=signout");
}

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
    $twigarr['user_city'] = $user['city'];
    $twigarr['user_gender'] = $user['gender'];
    $twigarr['profile_picture_path'] = $user['profile_picture_path'];
}

if ($_GET['do'] == "golist") {
    $querytu = $db->prepare("SELECT * FROM users WHERE id > 1");
    $querytu->execute();
    $all_users = $querytu->fetchAll();
    $wait_approval_users = [];
    $approved_users = [];
    $unapproved_users = [];

    foreach ($all_users as $user) {
        if ($user['status'] == 1) {
            $user['status'] = "Admin";
        } else {
            $user['status'] = "User";
        }
        if ($user['approval'] == 0) {
            $twigarr['wait_approval_users'][] = $user;
        } elseif ($user['approval'] == 1) {
            $twigarr['approved_users'][] = $user;
        } else {
            $twigarr['unapproved_users'][] = $user;
        }
    }
    echo $twig->render('users-table.html', $twigarr);
} elseif ($_GET['do'] == "gomyprofile") {
    echo $twig->render('my-profile.html', $twigarr);
} elseif ($_GET['do'] == "savemyprofile") {

    $uploads_dir = 'images/profile-pictures';
    @$tmp_name = $_FILES['picture_path']["tmp_name"];
    @$name = $_FILES['picture_path']["name"];
    $uniquenumber1 = rand(20000, 32000);
    $uniquenumber2 = rand(20000, 32000);
    $uniquenumber3 = rand(20000, 32000);
    $uniquenumber4 = rand(20000, 32000);
    $uniquename = $uniquenumber1 . $uniquenumber2 . $uniquenumber3 . $uniquenumber4;
    $image_path = $uploads_dir . "/" . $uniquename . $name;
    @move_uploaded_file($tmp_name, "$uploads_dir/$uniquename$name");


    $queryset = $db->prepare("UPDATE users SET
    name=:name,
		surname=:surname,
		gender=:gender,
		city=:city,
		profile_picture_path=:profile_picture_path
		WHERE email='{$_POST['email']}'");

    $insert = $queryset->execute([
        'name' => $_POST['name'],
        'surname' => $_POST['surname'],
        'gender' => $_POST['gender'],
        'city' => $_POST['city'],
        'profile_picture_path' => $image_path
    ]);
    header('location:operations.php?do=gomyprofile');
} elseif ($_GET['approvaluser']){
    $queryapprov = $db->prepare("UPDATE users SET approval=1 WHERE email='{$_GET['approvaluser']}'");
    $queryapprov->execute();
    header('location:operations.php?do=golist');
} elseif ($_GET['disapprovaluser']){
    $queryapprov = $db->prepare("UPDATE users SET approval=2 WHERE email='{$_GET['disapprovaluser']}'");
    $queryapprov->execute();
    header('location:operations.php?do=golist');
}