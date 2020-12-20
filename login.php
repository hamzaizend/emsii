<?php
session_start();  
include "connexion.inc.php";

/*if(isset($_SESSION['role'])){
    if($_SESSION['role']=="admin" || $_SESSION['role']=="admin_cours"){
        header('Location:admin/index.php');
        $_SESSION['flash']['danger']="Vous ne pouvez pas accéder au site avec votre compte administrateur";
        exit();
    }
}
*/
if(!isset($_SESSION['id'])){

    if(isset($_POST['signin'])){
        
       
        $email=htmlspecialchars($_POST['email']);
        $password=password_hash($_POST['password'],PASSWORD_BCRYPT);

        if(!empty($email) AND !empty($password)){
            $requser = $bdd->prepare("SELECT * FROM membre WHERE email=?");
            $requser->execute(array($email));
            $userexist = $requser->rowCount();
            $userinfo = $requser->fetch();

         if($userinfo['role'] === 'admin'){
                if($_POST['password']=='root'){
                    $_SESSION['id'] = $userinfo['id'];
                    $_SESSION['name']= $userinfo['name'];
                    $_SESSION['email'] = $userinfo['email'];
                    $_SESSION['role'] = $userinfo['role'];
                    header('Location:admin/index.php');
                    exit();
                }else{
                $erreur = "Veuillez verifier votre mot de passe ou email ";
                }
            }

          

            if($userexist && password_verify($_POST['password'],$userinfo['password'])){       
                if($userinfo['confirmed_at']){
                    $_SESSION['id'] = $userinfo['id'];
                    $_SESSION['email'] = $userinfo['email'];
                    $_SESSION['name'] = $userinfo['name'];
                    $_SESSION['birth'] = $userinfo['dateofbirth'];
                    $_SESSION['phone'] = $userinfo['phone'];
                    $_SESSION['sexe'] = $userinfo['sexe'];
                    $_SESSION['flash']['success']="Vous etes maintenant connecté";
                    header('location:AdminLTE-master/index.html.php');
                    exit();    
                }else{
                    $_SESSION['flash']['success']="Veuillez d'abord confirmez votre compte , veuillez cliquez sur le lien envoyé a votre boite mail";
                }
            }else{
                $erreur = "*Mauvais mail ou Mot de passe ";
            }
        }else{
            $erreur = "*Tous les champs doivent être complétés";
        }
    }
}else{
    $_SESSION['flash']['danger']="Vous n'avais pas accés a cette page car vous etes déja connecté";
    
    header('location:AdminLTE-master/index.html.php');
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="icon" href="img/favicon.png" type="image/png" />
    <title>Sign in</title>
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
                   
                </div>
            </nav>
        </div>
    </header>
    <!--================ End Header Menu Area =================-->

    <!-- Sign up form -->
    <section class="sign-in">
        <div class="container-signup" style="margin-bottom:120px;">
            <div class="signin-content">
                <div class="signin-image">
                    <figure id="figure-login"><img src="img/sign/signin-image.jpg" alt="sing up image"></figure>
                    <a href="register.php" class="signup-image-link btn btn-outline-dark">Create an account</a>
                </div>
                <div class="signin-form">
                    <form method="POST" class="register-form pt-3" id="login-form">
                        <fieldset style="border:3px solid black;" class="p-3 border-dark rounded ">
                            <legend style="width:auto; letter-spacing: 1px;" class="pr-3 pl-3 "> Sign-in </legend>
                            <div class="form-group">
                                <label for="your_mail"><i class="fas fa-envelope"></i></label>
                                <input type="text" name="email" id="your_name" placeholder="E-mail" />
                            </div>
                            <div class="form-group">
                                <label for="your_pass"><i class="fas fa-lock"></i></label>
                                <input type="password" name="password" id="your_pass" placeholder="Password" />
                            </div>

                            <div class="text-right">
                                <span class="text-primary mb-2"> <i class="fas fa-unlock-alt"></i> </span><a style=" font-family: 'Open Sans', sans-serif; font-size:14px; font-weight:bold;" href="recover.php"> Mot de passe oublié ? </a>
                            </div>

                            <?php if(isset($erreur)) : ?>
                            <b style="letter-spacing:1px;" class="text-danger"> <?= $erreur ?> </b>
                            <?php endif; ?>
                            <div class="form-group form-button">
                                <button class="btn btn-lg btn-outline-dark btn-block" type="submit" name="signin"
                                    id="signin"> Login</button>
                            </div>
                        </fieldset>
                    </form>
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
    <script src="https://kit.fontawesome.com/6e8ba3d05b.js" crossorigin="anonymous"></script>
    <script src="js/js-login/main.js"></script>
    <script>
        $(".alert").delay(5000).slideUp(400, function() {
            $(this).alert('close');
        });
    </script>

</body>

</html>