<?php

    if (isset($_COOKIE['auth']) && !isset($_SESSION['connect'])) {

        //var
        $secret = htmlspecialchars($_COOKIE['auth']);

        //control
        require ('src/connect.php');

        $req = $bdd->prepare('SELECT COUNT(*) as numberAccount FROM netflix.user WHERE secret=?');
        $req->execute(array($secret));

        while ($userSecret= $req->fetch()){

            if ($userSecret['numberAccount'] == 1) {

                $reqUser = $bdd->prepare('SELECT * FROM netflix.user WHERE secret=?');
                $reqUser->execute(array($secret));
                while ($userAccount= $reqUser->fetch()) {
                    $_SESSION['connect'] = 1;
                    $_SESSION['email'] = $userAccount['email'];
                }

            }

        }

    }

    if (isset($_SESSION['connect'])) {
        require ('src/connect.php');
        $reqUser = $bdd->prepare('SELECT * FROM netflix.user WHERE email=?');
        $reqUser->execute(array($_SESSION['email']));
        while ($userAccount= $reqUser->fetch()) {
            if ($userAccount['blocked']==1){
                header('Location: ../logout.php');
                exit();
            }
        }

    }

?>