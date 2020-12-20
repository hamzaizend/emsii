<?php
session_start();

if(isset($_SESSION['id'])){
    unset($_SESSION['id']);
    unset($_SESSION['email']);
    unset($_SESSION['name']);
    unset($_SESSION['role']);
    $_SESSION['flash']['success']="Vous avez été deconnecté";
}
else {
    $_SESSION['flash']['danger']="Vous etes deja deconnecté";
}

header("Location:login.php");

?>