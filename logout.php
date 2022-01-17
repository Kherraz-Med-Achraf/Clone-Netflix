<?php

    session_start();
    session_unset();
    session_destroy();
    setcookie('auth', '', time()-1, '/', null, false , true);//donner un temps negative detruit le cookie

    header('Location: index.php');
    exit();

?>
