<?php // main.php
    require_once 'login.php';
    $connection = new mysqli($hn, $un, $pw, $db);
    if ($connection->connect_error) die($connection->connect_error);
    if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']))
    {
        $un_temp = mysql_entities_fix_string($connection, $_SERVER['PHP_AUTH_USER']);
        $pw_temp = mysql_entities_fix_string($connection, $_SERVER['PHP_AUTH_PW']);
        $query = "SELECT * FROM users WHERE username='$un_temp'";
        $result = $connection->query($query);
        if (!$result) die(mysql_fatal_error("Could not connect."));
        elseif ($result->num_rows)
        {
            $row = $result->fetch_array(MYSQLI_NUM);
            $result->close();
            $salt1 = "!z@ak*";
            $salt2 = "*g@@m";
            $token = hash('ripemd128', "$salt1$pw_temp$salt2");
            if ($token == $row[1]) {
                echo "Hello $row[0], you are now logged in.";
                echo <<<_UPLOADCHECK
<br>Check for Virus<br>
<form action="sqltest.php" method="post"><pre>
File: <input type="file" name="viruscheck">
<input type="submit" value="CHECKVIRUS">
</pre></form>
<br>
                _UPLOADCHECK;
                if (isset($_FILES['viruscheck']))
                {
                    $myfile = fopen($_FILES['viruscheck'], "rb");
                    $loops = ceil(filesize($filename)/20); //checks every 20 bytes and sees if they match anything in the database
                    $found = false;
                    for ($i = 0; $i < loops; $i++) {
                        $firsttwenty = fread($myfile, "20");
                        $querycheck = "SELECT virusname FROM virusrecords WHERE virus='$firsttwenty'";
                        $result = $conn->query($querycheck);
                        $rows = $result->num_rows;
                        if ($rows > 0) {
                            $result->data_seek(0);
                            $row = $result->fetch_array(MYSQLI_ASSOC);
                            echo '<br>File is infected with a virus named ' . $row['virusname'] . '<br>';
                            $found = true;
                        }
                    }
                    fclose($_FILES['viruscheck']);
                    if ($found == false) {
                        echo '<br>No virus detected for file.';
                    }
                    if (!$result) mysql_fatal_error("Upload failed.");
                }
                if ($row[2] != 0){ //Checks if Admin, 0 = false, anything else = true
                    echo <<<_UPLOADVIRUS
<br>Upload Virus to Database<br>
<form action="sqltest.php" method="post"><pre>
Name: <input type="text" name="virusname">
File: <input type="file" name="virusupload">
<input type="submit" value="UPLOADVIRUS">
</pre></form>
<br>
_UPLOADVIRUS;
                    if (isset($_FILES['virusupload']))
                    {
                        $name = mysql_entities_fix_string($conn, $_POST['virusname'];
                        if (($name !== '') {
                            $myfile = fopen($_FILES['virusupload'], "rb");
                            $firsttwenty = fread($myfile, "20");
                            fclose($_FILES['virusupload']);
                            $queryvirus = "INSERT INTO virusrecords VALUES('$name', '$firsttwenty')";
                            $result = $conn->query($queryvirus);
                            if (!$result) mysql_fatal_error("Upload failed.");
                        } else {
                            mysql_fatal_error("No name for virus.");
                        }
                    }
                }
            }
            else die("Invalid username/password combination");
        }
        else die("Invalid username/password combination");
    }
    else  { // if ($_SERVER['PHP_AUTH_USER’])  and  ($_SERVER['PHP_AUTH_PW’]) are not set
        header('WWW-Authenticate: Basic realm="Restricted Section"');
        header('HTTP/1.0 401 Unauthorized');
        die ("Please enter your username and password");
    }
    $connection->close();
    
    function mysql_entities_fix_string($connection, $string) {
        return htmlentities(mysql_fix_string($connection, $string));
    }
    function mysql_fix_string($connection, $string) {
        if (get_magic_quotes_gpc()) $string = stripslashes($string);
        return $connection->real_escape_string($string);
    }
    function mysql_fatal_error($msg, $conn)
    {
        echo <<<_ERROR
<p>$msg<p>
We are sorry, but it was not possible to complete
the requested task. We are sorry for the inconvenience.
_ERROR;
    }
?>