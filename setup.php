<?php // setup.php
    require_once 'login.php';
    $connection = new mysqli($hn, $un, $pw, $db);
    if ($connection->connect_error) die($connection->connect_error);

    $query = "CREATE TABLE users (
        username VARCHAR(32) NOT NULL UNIQUE,
        password VARCHAR(32) NOT NULL,
        admin TINYINT(1) NOT NULL
    }";

    $result = $connection->query($query);
    if (!$result) die($connection->error);

    $salt1 = "!z@ak*";
    $salt2 = "*g@@m";

    $username = 'lalex';
    $password = 'Input@Viruschecke!';
    $admin = 1; //Is an admin

    $token = hash('ripemd128', "$salt1$password$salt2");

    add_user($connection, $username, $token, $admin);

    $username = 'tfabio';
    $password = '@passNeedToCheck@2020';
    $admin = 0; //not an admin

    $token = hash('ripemd128', "$salt1$password$salt2");

    add_user($connection, $username, $token, $admin);

    
    $query = "CREATE TABLE virusrecords (
        virusname VARCHAR(32) NOT NULL UNIQUE,
        virus CHAR(20) NOT NULL,
    }";

    $result = $connection->query($query);
    if (!$result) die($connection->error);

    $connection->close();

    function add_user($connection, $fn, $sn, $un, $pw) 
    {
        $query = "INSERT INTO users VALUES('$fn', '$sn', '$un', '$pw')";
        $result = $connection->query($query);
        if (!$result) die($connection->error);
    }
?>