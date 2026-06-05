<?php

session_start();

if(!isset($_SESSION['seller_id'])){

    header(
        "Location: login.php?message=seller"
    );

    exit;
}

header(
    "Location: register_business.php"
);

exit;