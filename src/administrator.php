<?php
/**
 * Created by PhpStorm.
 * User: filip
 * Date: 23.04.18.
 * Time: 10:31
 */

$dbc = mysqli_connect('localhost', 'root', '', 'PWA') or die('Error connecting to MySQL/MariaDB server.');
$query = "SELECT * FROM Clanci";
$result = mysqli_query($dbc, $query);

echo '

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="style.css" />
    <link rel="icon" href="../res/circle_logo_fav.png" type="image/gif" sizes="16x16">
    <title>Vijesti 747</title>
</head>
<body>
<nav>
    <figure class="float_left">
        <a href="index.php">
            <img src="../res/circle_logo.png" alt="Ovo je nevidljivi opis jedne slike">
        </a>
    </figure>
    <ul>
        <li><a href="index.php">Povratak na stranicu</a></li>
        <li><a href="administrator.php " class="active">Administracija</a></li>
    </ul>
</nav>
<div id="main_content">
    <main>
        <h1>Administracija news portala</h1>
        <br>
        <br>
        ';
        while($row = mysqli_fetch_array($result)){
            echo '<span>Naslov: '.$row['Naslov'].'  </span>';
            echo '<span>Datum: '.$row['Datum'].'  </span>';
            echo '<span>Sakrivena: '.$row['Sakrivena'].'  </span>';
            echo '<br><br>';
        }

      echo  '
    </main>
</div>
<footer class="clear_floating">
    <p>Kreirao: Filip M.</p>
    <p>Kontakt: <a href="mailto:fmilkovic@tvz.hr?Subject=Kontakt%20sa%20weba" target="_top">fmilkovic@tvz.hr</a></p>
    <p id="last"><a href="unos.html">Unos vijesti</a></p>
</footer>
</body>
</html>

';

mysqli_close($dbc);