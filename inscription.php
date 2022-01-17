<?php

    session_start();

    require ('src/log.php');

    if (isset($_SESSION['connect'])) {
        header('Location: index.php');
        exit();
    }

    if (!empty($_POST["email"]) && !empty($_POST["password"]) && !empty($_POST["password_conf"]) ) {

        require ('src/connect.php');

        //var

        $email = htmlspecialchars($_POST["email"]);
        $password = htmlspecialchars($_POST["password"]);
        $password_conf = htmlspecialchars($_POST["password_conf"]);

        //password match

        if ( $password != $password_conf) {
            header('Location: inscription.php?error=1&message=Vos de mot de passe ne sont pas identiques.');
            exit();

        }

        //email control

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: inscription.php?error=1&message=Votre adresse email est invalide.');
            exit();
        }

        //email data base control

        $req = $bdd->prepare('SELECT COUNT(*) as numberEmail FROM netflix.user WHERE email=?');
        $req->execute(array($email));

        while ($email_contol= $req->fetch()){

            if ($email_contol['numberEmail']!=0){

                header('Location: inscription.php?error=1&message=cette adresse email est déja utilisée par un 
                        autre utilisateur.');
                exit();
                }

        }

        //hash

        $secret = sha1(sha1($email).time()).time();

        //Chiffrage password

        $password = "aq1".sha1($password."12345")."25";

        //envoie

        $req = $bdd->prepare('INSERT INTO netflix.user(email, password, secret) VALUES (?,?,?)');

        $req->execute(array($email, $password, $secret));

        header('Location: inscription.php?success=1');
        exit();
    }
?>




<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<title>Netflix</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="design/default.css">
	<link rel="icon" type="image/pngn" href="img/favicon.png">
    <script src="https://kit.fontawesome.com/6009bf91cb.js" crossorigin="anonymous"></script>
</head>
<body>

	<?php include('src/header.php'); ?>

    <div id="portfolio">
        <span><a href="https://kherraz-med-achraf-portfolio.go.yo.fr/" target="_blank">
            <i class="fas fa-arrow-circle-left"></i> Retourner au portfolio</a></span>
        <a href="https://kherraz-med-achraf.com/" target="_blank"><i class="fas fa-arrow-circle-left" id="responsive"></i>
        </a>
    </div>
	<section>
		<div id="login-body">
			<h1>S'inscrire</h1>

            <?php if (isset($_GET['error'])) {
                if (isset($_GET['message'])) {
                    echo '<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';
                }
            } else if (isset($_GET['success'])) {
                echo'<div class="alert success">Vous êtes maintenant inscrit !</div>
                     <div id="connect-div"><a id="connect" href="index.php">Connectez-vous.</a></div>';
            } ?>

			<form method="post" action="inscription.php">
				<input type="email" name="email" placeholder="Votre adresse email" required />
				<input type="password" name="password" placeholder="Mot de passe" required />
				<input type="password" name="password_conf" placeholder="Confirmer votre mot de passe" required />
				<button type="submit">S'inscrire</button>
			</form>

			<p class="grey">Déjà sur Netflix ? <a href="index.php">Connectez-vous</a>.</p>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>
</html>