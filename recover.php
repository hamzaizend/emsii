<?php
session_start();  
include "connexion.inc.php";

if(isset($_SESSION['role'])){
    if($_SESSION['role']=="admin" || $_SESSION['role']=="admin_cours"){
        header('Location:admin/index.php');
        $_SESSION['flash']['danger']="Vous ne pouvez pas accéder au site avec votre compte administrateur";
        exit();
    }
}

function str_random($lenght){

    $alphabet="0123456789azertyuiopqsdfghjklmwxvbnAZERTYUIOPQSDFGHJKLMWXCVBN";

    return substr(str_shuffle(str_repeat($alphabet,$lenght)),0,$lenght); 
}


if(!isset($_SESSION['id'])){

    if(isset($_POST['submit']) && isset($_POST['email'])){
        if(!empty($_POST['email'])){
            $email=htmlentities($_POST['email']);        
            $mailexist=$bdd->prepare("SELECT * FROM membre WHERE email=? ");
            $mailexist->execute([$email]);
            $ok = $mailexist->rowCount();
            if($ok){
                $_SESSION['mail_recover']=$email;
                $token=str_random(9);
                $mail_recover_exist=$bdd->prepare("SELECT id FROM recover WHERE email = ?");
                $mail_recover_exist->execute([$email]);
                $mail_recover_exist = $mail_recover_exist->rowCount();
                if($mail_recover_exist){
                    $recover_insert = $bdd->prepare('UPDATE recover SET token=? WHERE email=?');
                    $recover_insert->execute(array($token,$email));
                    $to=$_POST['email'];
                    $subject="Recuperation de votre mot de passe";
                    $message="Bonjour afin de recuperer votre compte voici le code a entrer :  $token ";
                    $_SESSION['flash']['success']="Un email avec le code vous a été envoyé";
                    header('location: http://localhost/PFE-lastversion/recover.php?section=code');
                    mail($to,$subject,$message);
                }else{
                    $recover_insert = $bdd->prepare('INSERT INTO recover(email,token) VALUES (?,?)');
                    $recover_insert->execute(array($email,$token));
                    $to=$_POST['email'];
                    $subject="Recuperation de votre mot de passe";
                    $message="Bonjour afin de recuperer votre compte voici le code a entrer :  $token ";
                    $_SESSION['flash']['success']="Un email avec le code vous a été envoyé.";
                    header('location: http://localhost/PFE-lastversion/recover.php?section=code');
                    mail($to,$subject,$message);
                }

            }else{
                $_SESSION['flash']['danger']="Aucun compte n'est enregistré avec cette email";
            }
        }else{
            $erreur="*Veuillez entrez votre email pour pouvoir continuer";
        }
    }

    if(isset($_GET['section'])){
        $section=htmlspecialchars($_GET['section']);
    }else{
        $section="";
    }

    if(isset($_POST['submit_recover']) && isset($_POST['token'])){
        if(!empty($_POST['token'])){
            $token = htmlspecialchars($_POST['token']);
            $verificationreq= $bdd->prepare("SELECT * FROM recover WHERE token=? AND email=?");
            $verificationreq->execute(array($token,$_SESSION['mail_recover'])); 
            $verificationreq= $verificationreq->rowCount();
            if($verificationreq){
                $up_req = $bdd->prepare("UPDATE recover SET confirm = 1 WHERE email = ?");
                $up_req->execute(array($_SESSION['mail_recover']));
                header('location:http://localhost/PFE-lastversion/recover.php?section=recoverpwd');
            }else{
                $erreur="Le code que vous avez saisi est invalide";
            }
        }   
        else{
            $erreur="Veuillez entrez votre code de confirmation pour continuez";
        }
    }

    if(isset($_POST['change_pwd'])){
        if(isset($_POST['password']) && isset($_POST['password_confirm'])){
            $verif_confirm=$bdd->prepare("SELECT confirm FROM recover WHERE email=?");
            $verif_confirm->execute(array($_SESSION['mail_recover']));
            $verif_confirm = $verif_confirm->fetch();
            $verif_confirm = $verif_confirm['confirm'];
            if($verif_confirm == 1){
                $password=htmlspecialchars($_POST['password']);
                $password_confirm=htmlspecialchars($_POST['password_confirm']);
                if(!empty($password) AND !empty($password_confirm)){
                    if($password==$password_confirm){
                        $password=password_hash($password,PASSWORD_BCRYPT);
                        $ins_mdp= $bdd->prepare('UPDATE membre SET password = ? WHERE email=?');
                        $ins_mdp->execute(array($password,$_SESSION['mail_recover']));
                        $del_req=$bdd->prepare('DELETE FROM recover WHERE email=?');
                        $del_req->execute(array($_SESSION['mail_recover']));
                        $_SESSION['flash']['success']="Votre mot de passe a été modifié avec succes";
                        header('Location:login.php');
                        exit();
                    }
                }
            }
            else{
                $erreur="Vous n'avez pas saisi le code de confirmation envoyé a votre mail.";
            }
        }
        else{
            $erreur="Veuillez remplire tous les champs";
        }
    }

    
}

else{
    $_SESSION['flash']['danger']="Vous n'avais pas accés a cette page car vous etes déja connecté";
    header('location:index.php');
    exit();
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="icon" href="img/favicon.png" type="image/png" />
    <title>Recover </title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.css" />
    <link rel="stylesheet" href="css/flaticon.css" />
    <link rel="stylesheet" href="css/themify-icons.css" />
    <link rel="stylesheet" href="vendors/owl-carousel/owl.carousel.min.css" />
    <link rel="stylesheet" href="vendors/nice-select/css/nice-select.css" />
   
    <!-- main css -->
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="./css/css-sign/style.css">
    <link rel="stylesheet" href="fonts/material-icon/css/material-design-iconic-font.min.css">
    <script>
    function showHint(str) {
        if (str.length == 0) {
            document.getElementById("txtHint").innerHTML = "";
            return;
        } else {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("txtHint").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "gethint.php?q=" + str, true);
            xmlhttp.send();
        }
    }
    </script>

</head>

<body>



    <!--================ Start Header Menu Area =================-->
    <header class="header_area">
        <div class="main_menu">
            <div class="search_input" id="search_input_box">
                <div class="container">
                    <form class="d-flex justify-content-between" method="" action="">
                        <input type="text" class="form-control" id="search_input" placeholder="Search Here"
                            onkeyup="showHint(this.value)" /><br />
                        <span id="txtHint" class="form-control">
                        </span>
                        <button type="submit" class="btn"></button>
                        <span class="ti-close" id="close_search" title="Close Search"></span>
                    </form>
                </div>
            </div>

            <?php if(isset($_SESSION['flash'])) : ?>

                <?php foreach($_SESSION['flash'] as $type => $message):?>

                    <div class="alert fade show alert-<?= $type ?>">
                        <div style="font-family:Rubik,sans-serif;"
                            class="pt-2 pb-2 lead text-align-center text-center ">
                            <i class="fas fa-exclamation-circle"></i> <?= $message ?>
                            <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true"> <i class="far fa-times-circle" ></i> </span>
                            </button>
                        </div>
                    </div>

                <?php  endforeach ?>

                <?php unset($_SESSION['flash']); ?>

            <?php endif ?>

            <nav class="navbar navbar-expand-lg navbar-light">

                <div class="container">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <a class="navbar-brand logo_h" href="index.html"></a>

                    <button class="navbar-toggler" type="button" data-toggle="collapse"
                        data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="icon-bar"></span> <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <?php if (isset($_SESSION['id'])) : ?>
                    <b class="lead">Bienvenue Monsieur <?= $_SESSION['name'] ?> </b>
                    <?php endif; ?>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse offset" id="navbarSupportedContent">

                        
                        
                    </div>
                </div>
            </nav>
        </div>
    </header>
    <!--================ End Header Menu Area =================-->

    <!-- Sign up form -->
    <section class="sign-in">
        <div class="container-signup" style="margin-bottom:120px;">
            <div class="signin-content">
                <div class="signin-form">
                    <?php if($section === "code") : ?>

                    <form method="POST" class="register-form" id="login-form">
                        <fieldset style="border:3px solid black;" class="p-3 border-dark rounded ">

                            <legend style="width:auto; letter-spacing: 1px;" class="pr-3 pl-3 "> Recover ur password
                            </legend>
                            <span class="lead"> Recuperation du mot de passe pour <b class="text-success">
                                    <?= $_SESSION['mail_recover'] ?> </b> </span>
                            <div class="form-group">
                                <label for="your_mail"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                <input type="text" name="token" id="your_name" placeholder="Code de verification" />
                            </div>

                            <?php if(isset($erreur)) : ?>
                            <b style="letter-spacing:1px;" class="text-danger"> <?= $erreur ?> </b>
                            <?php endif; ?>

                            <div class="form-group form-button">
                                <button class="btn btn-lg btn-outline-dark btn-block" type="submit"
                                    name="submit_recover" id="signin"><i class="fa fa-sign-in"></i> Submit </button>
                            </div>
                        </fieldset>
                    </form>


                    <?php elseif($section === "recoverpwd") : ?>

                    <form method="POST" class="register-form" id="login-form">
                        <fieldset style="border:3px solid black;" class="p-3 border-dark rounded ">

                            <legend style="width:auto; letter-spacing: 1px;" class="pr-3 pl-3 "> Recover ur password
                            </legend>
                            <span class="lead"> Nouveau mot de passe pour <b class="text-success">
                                    <?= $_SESSION['mail_recover'] ?> </b> </span>
                            <div class="form-group">
                                <label for="your_mail"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" name="password" id="your_name"
                                    placeholder="Nouveau mot de passe" />

                            </div>

                            <div class="form-group">
                                <label for="your_mail"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" name="password_confirm" id="your_name"
                                    placeholder="Reentrez votre mot de passe" />
                            </div>

                            <?php if(isset($erreur)) : ?>
                            <b style="letter-spacing:1px;" class="text-danger"> <?= $erreur ?> </b>
                            <?php endif; ?>

                            <div class="form-group form-button">
                                <button class="btn btn-lg btn-outline-dark btn-block" type="submit" name="change_pwd"
                                    id="signin"><i class="fa fa-sign-in"></i> Submit </button>
                            </div>
                        </fieldset>
                    </form>

                    <?php else : ?>

                    <form method="POST" class="register-form" id="login-form">
                        <fieldset style="border:3px solid black;" class="p-3 border-dark rounded ">
                            <legend style="width:auto; letter-spacing: 1px;" class="pr-3 pl-3 "> Recover ur password
                            </legend>
                            <div class="form-group">
                                <label for="your_mail"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                <input type="email" name="email" id="your_name" placeholder="E-mail" />
                            </div>

                            <?php if(isset($erreur)) : ?>
                            <b style="letter-spacing:1px;" class="text-danger"> <?= $erreur ?> </b>
                            <?php endif; ?>
                            <div class="form-group form-button">
                                <button class="btn btn-lg btn-outline-dark btn-block" type="submit" name="submit"
                                    id="signin"><i class="fa fa-sign-in"></i> Submit </button>
                            </div>
                        </fieldset>
                    </form>

                    <?php endif;?>

                </div>
            </div>
        </div>
    </section>
    </div>

    <!--================ Start footer Area  =================-->
        <!-- Footer -->
        <footer class="page-footer font-small indigo bg-light">

        <!-- Footer Links -->
        <div class="container">

            <!-- Grid row-->
            <div class="row text-center d-flex justify-content-center pt-5  mb-3">

            <!-- Grid column -->
            
            <!-- Grid column -->

            </div>
            <!-- Grid row-->
            <hr class="rgba-white-light" style="margin: 0 15%;">

            <!-- Grid row-->
            <div class="row d-flex text-center justify-content-center mb-md-0 mb-4">

            <!-- Grid column -->
           
            <!-- Grid column -->

            </div>
            <!-- Grid row-->
            <hr class="clearfix d-md-none rgba-white-light" style="margin: 10% 15% 5%;">

            </div>
            <!-- Grid row-->

            <!-- Copyright -->
        <div class="footer-copyright text-center py-3"> <b class="text-dark"> © 2020 Copyright: IZEND Hamza && HAMMOU-TAHRA Nore </b> </div>
        <!-- Copyright -->
        </div>
        <!-- Footer Links -->



        </footer>
        <!-- Footer -->
    <!--================ End footer Area  =================--

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/popper.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="vendors/nice-select/js/jquery.nice-select.min.js"></script>
    <script src="vendors/owl-carousel/owl.carousel.min.js"></script>
    <script src="js/owl-carousel-thumb.min.js"></script>
    <script src="js/jquery.ajaxchimp.min.js"></script>
    <script src="js/mail-script.js"></script>
    <!--gmaps Js-->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjCGmQ0Uq4exrzdcL6rvxywDDOvfAu6eE"></script>
    <script src="js/gmaps.min.js"></script>
    <script src="js/theme.js"></script>
    <script src="vendor2/jquery/jquery.min.js"></script>
    <script src="js/js-login/main.js"></script>
    <script>
        $(".alert").delay(5000).slideUp(400, function() {
            $(this).alert('close');
        });
    </script>

</body>

</html>