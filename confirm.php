<?php 
session_start();

$user_id=$_GET['id'];

$token=$_GET['token'];

include "connexion.inc.php";

$requete=$bdd->prepare('SELECT * FROM membre WHERE id= ?');

$requete->execute([$user_id]);

$user=$requete->fetch();

if($user && $user['confirmation_token'] == $token){

    $bdd->prepare('UPDATE membre set confirmation_token = NULL,confirmed_at = NOW() WHERE id = ?')->execute([$user_id]);

    $_SESSION['flash']['success']="Votre compte a bien été validé vous pouvez vous connectez";
    
    header('Location:login.php');

}else{
    $_SESSION['flash']['danger']="Ce token est utilisable une seul fois seulement";
    header('location: index.php');
}

