<?php

    session_start();

    require ('src/log.php');

    if (!empty($_POST["email"]) && !empty($_POST["password"])) {

        require ('src/connect.php');

        //var
        $email = htmlspecialchars($_POST["email"]);
        $password = htmlspecialchars($_POST["password"]);

        //email control

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: index.php?error=1&message=Votre adresse email est invalide.');
            exit();
        }

        //Chiffrage password

        $password = "aq1".sha1($password."12345")."25";

        //email data base control

        $req = $bdd->prepare('SELECT COUNT(*) as numberEmail FROM netflix.user WHERE email=?');
        $req->execute(array($email));

        while ($email_contol= $req->fetch()){

            if ($email_contol['numberEmail']!=1){

                header('Location: index.php?error=1&message=Impossible de vous authentifier.');
                exit();
            }

        }

        // email password match

        $req = $bdd->prepare('SELECT * FROM netflix.user WHERE email = ?');
        $req->execute(array($email));

        while ($user= $req->fetch()){

            if ($password == $user['password'] && $user['blocked'] == 0){

                $_SESSION['connect'] = 1;
                $_SESSION['email'] = $user['email'];

                if (isset($_POST['auto'])) {
                    setcookie('auth', $user['secret'], time() + 365 * 24 * 3600, '/',null,false,true);
                }

                header('Location: index.php?success=1');
                exit();
            }
            else {

                header('Location: index.php?error=1&message=Impossible de vous authentifier.');
                exit();
                
            }
        }
    }
    ?>









<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<title>Netflux</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="design/default.css">
	<link rel="icon" type="image/pngn" href="img/favicon.png">
    <script src="https://kit.fontawesome.com/6009bf91cb.js" crossorigin="anonymous"></script>
</head>
<body>

	<?php include('src/header.php'); ?>

    <div id="portfolio">
        <span><a href="https://kherraz-med-achraf.com/" target="_blank">
            <i class="fas fa-arrow-circle-left"></i> Retourner au portfolio</a></span>
        <a href="https://kherraz-med-achraf.com/" target="_blank"><i class="fas fa-arrow-circle-left" id="responsive"></i>
        </a>
    </div>
	<section>
		<div id="login-body">
                <?php
                    if (isset($_SESSION['connect'])) { ?>

                        <h1>Bienvenue !</h1>
                        <?php if (isset($_GET['success'])) {
                            echo '<div class="alert success">Vous êtes maintenant connecté !</div>';
                        }?>
                        <p>Qu'allez-vous regarder aujourd'huit ?</p>
                        <small><a href="logout.php">Déconnexion</a></small>

                <?php    } else { ?>
                    <h1>S'identifier</h1>
                    <?php if (isset($_GET['error'])) {
                        if (isset($_GET['message'])) {
                            echo '<div class="alert error">' . htmlspecialchars($_GET['message']) . '</div>';
                        }
                      }
                    ?>

                    <form method="post" action="index.php">
                        <input type="email" name="email" placeholder="Votre adresse email" required />
                        <input type="password" name="password" placeholder="Mot de passe" required />
                        <button type="submit">S'identifier</button>
                        <label id="option"><input type="checkbox" name="auto" checked />Se souvenir de moi</label>
                    </form>


                    <p class="grey">Première visite sur Netflix ? <a href="inscription.php">Inscrivez-vous</a>.</p>
                <?php  } ?>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>
</html>