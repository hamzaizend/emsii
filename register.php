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
    if(isset($_POST['signup'])){
        if(!empty($_POST['name']) AND !empty($_POST['email']) AND !empty($_POST['pass']) AND !empty($_POST['re_pass']) AND !empty($_POST['tel']) AND !empty($_POST['gender']) AND !empty($_POST['date'])){
            if(preg_match("/@emsi-edu.ma/",$_POST['email'])){
                if(isset($_POST['agree-term'])){
                    $email = htmlspecialchars($_POST['email']);
                    $reqmail = $bdd->prepare("SELECT * FROM membre WHERE email=?");
                    $reqmail->execute(array($email));
                    $exist=$reqmail->rowCount();
                    
                    if($exist == 0){
                        if($_POST['pass'] === $_POST['re_pass']){
                            $name = htmlspecialchars($_POST['name']);
                            $pass = password_hash($_POST['pass'],PASSWORD_BCRYPT);
                            $tel = htmlspecialchars($_POST['tel']);
                            $sexe = htmlspecialchars($_POST['gender']);
                            $date= htmlspecialchars($_POST['date']);
                            $token = str_random(60);
                            $insertmbr = $bdd->prepare("INSERT INTO membre(name,email,dateofbirth,phone,sexe,password,confirmation_token,role) VALUES(?,?,?,?,?,?,?,?)");

                            $insertmbr->execute(array($name,$email,$date,$tel,$sexe,$pass,$token,'user'));
                            $user_id=($bdd->lastInsertId());

                            $to=$_POST['email'];
                            $subject="Confirmation de votre compte";
                            $message="Bonjour afin de confirmer votre compte veuillez cliquez sur le lien suivant http://localhost/pfe-lastversion/confirm.php?id=$user_id&token=$token";
                            $_SESSION['flash']['success']="Un email de confirmation a été envoyé pour validé votre compte";
                            
                            mail($to,$subject,$message);
                            
                            header('location:login.php');
                            exit();
                        }
                        else{
                            $erreur = "Mots de passes non identiques ";
                        }
                    }
                    else{
                        $erreur = "Ce Mail a dèja été utilisée ";
                    }
                }else{
                    $erreur ="Veuillez accepter les conditions ";
                }
            }else{
                $erreur ="Veuillez entrez une adresse mail de l'emsi !! ";
            }
        } else {
            $erreur = "Tous les champs doivent être complétés ";
        }
    }
}

else{
    $_SESSION['flash']['danger']="Vous n'avais pas accés a cette page , Veuillez vous deconnecter si vous voulez creé un autre compte";
    header('location:login.php');
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="icon" href="img/favicon.png" type="image/png" />
    <title>Sign up</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.css" />
    <link rel="stylesheet" href="css/flaticon.css" />
    <link rel="stylesheet" href="css/themify-icons.css" />
    <link rel="stylesheet" href="vendors/owl-carousel/owl.carousel.min.css" />
    <link rel="stylesheet" href="vendors/nice-select/css/nice-select.css" />
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
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
                        <b style="font-family:verdana, helvetica, sans-serif;"class="lead">Bonjour Monsieur <?= $_SESSION['name'] ?> </b>
                    <?php endif; ?>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    
                </div>
            </nav>
        </div>
    </header>
    <!--================ End Header Menu Area =================-->

    <!-- Sign up form -->
    <section class="signup">
        <div class="container-signup">
            <div class="signup-content">
                <div class="signup-form">
                    <form method="POST" class="register-form" id="register-form">
                        <fieldset style="border:3px solid black;" class="p-3 border-dark rounded ">
                            <legend style="width:auto; letter-spacing: 2px;" class="pr-3 pl-3"> Sign-up </legend>

                            <div class="form-group">
                                <label for="name"><i class="fas fa-user-graduate"></i></label>
                                <input type="text" name="name" id="name" placeholder="Enter your name" value="<?php if(isset($_POST['name'])){
                                    echo $_POST['name'];
                                } ?>" />
                            </div>

                            <div class="form-group">
                                <label for="email"><i class="fas fa-envelope"></i></label>
                                <input type="email" name="email" id="email" placeholder="Enter your e-mail" style="font-size:17px;" value="<?php if(isset($_POST['email'])){
                                    echo $_POST['email'];
                                } ?>" />
                            </div>

                            <div class="form-group" >
                                <label for="date"><i class="fas fa-calendar-week"></i></label>
                                <input type="text" name="date" placeholder="Entrez votre date de naissance" onfocus="(this.type='date')" onblur="(this.type='text')" >
                            </div>

                            <div class="form-group">
                                <label for="tel"><i class="fas fa-phone-alt"></i></label>
                                <input type="text" name="tel" id="tel" placeholder="Enter your phone number" value="<?php if(isset($_POST['tel'])){
                                    echo $_POST['tel'];
                                } ?>" />
                            </div>
                                               
                            <div class="form-check-inline mb-3" style="border-bottom: 1px solid #999; width:100%;">   
                                
                                <span><i class="fas fa-venus-mars"></i></span> 

                                <div class="mr-3"></div>
                                <div class="mr-3"></div>
                                
                               <input type="radio" name="gender" value="homme" class="form-check-input"> <span style="margin-right:80px;">Homme</span> 
                               <div class="mr-3"></div>    
                               <input type="radio" name="gender" value="femme" class="form-check-input"> <span style="margin-right:80px;">Femme</span>    
                            
                            </div>

                            <div class="form-group">
                                <label for="pass"><i class="fas fa-lock"></i></label>
                                <input type="password" name="pass" id="pass" placeholder="Enter your password" />
                            </div>

                            <div class="form-group">
                                <label for="re-pass"><i class="fas fa-lock"></i></label>
                                <input type="password" name="re_pass" id="re_pass" placeholder="Confirm your password" />
                            </div>

                            <div class="form-group">
                                <input type="checkbox" name="agree-term" id="agree-term" class="agree-term" />
                                <label for="agree-term" class="label-agree-term"><span><span></span></span> I agree all
                                    statements in <a href="#" class="term-service">Terms of service</a></label><br>
                                    <?php if(isset($erreur)) : ?>
                                        <div class="text-center bg-danger mt-3">
                                        <b style=" font-family: 'Open Sans', sans-serif;" class="text-white  "> <i class="fas fa-exclamation-circle"></i> <?= $erreur ?> <i class="fas fa-exclamation-circle"></i> </b>
                                        </div>
                                    <?php endif; ?>
                            </div>
                            
                            <div class="form-group form-button">
                            <button class="btn btn-lg btn-outline-dark btn-block" type="submit" name="signup" id="signup"> Register </button>
                            </div>
                    </form>
                </div>
                <div class="signup-image">
                    <figure id="figure-register"><img src="img/sign/signup-image.jpg" alt="sign up image"></figure>
                    <a href="login.php" class="signup-image-link btn btn-outline-dark">I am already member</a>
                </div>
            </div>
    </section>

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
    <script src="https://kit.fontawesome.com/6e8ba3d05b.js" crossorigin="anonymous"></script>
    <script>
        $(".alert").delay(5000).slideUp(400, function() {
            $(this).alert('close');
        });
    </script>

</body>

</html>