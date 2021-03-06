<?php
/**
 * Created by PhpStorm.
 * User: filip
 * Date: 23.04.18.
 * Time: 10:31
 */

define('__APP__', TRUE);

session_start();

include("../dbconn.php");

if(isset($_POST['login'])) {
    $login = $_POST['login'];
    if ($login == "login") {
        $username = $_POST['uname'];
        $password = md5($_POST['psw']);
        $query = "SELECT * FROM Users WHERE Users.korisnickoIme='$username' AND Users.lozinka='$password'";
        $result = mysqli_query($dbc, $query);
        $query2 = "SELECT * FROM Users WHERE Users.korisnickoIme='$username'";
        $result2 = mysqli_query($dbc, $query2);
        $row = mysqli_fetch_array($result);
        if (mysqli_num_rows($result) > 0) {
           echo '
           <div class="alert">
                <span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span> 
                <strong>Uspjeh!</strong> Uspješan login '. $username .'
            </div>
           ';

            $_SESSION['username'] = $username;
            $_SESSION['user']['valid'] = "true";
            $_SESSION['accessLevel'] = $row['level'];
            $_SESSION['id'] = $row['id'];
            if ($row['level'] < 2) {
                $Poruka = $username . ', nemate dovoljno prava za pristup ovoj stranici';
                $id = "";
                include("template.php");
                exit();
            }
        }else if(mysqli_num_rows($result2) > 0){
            $Poruka = 'Kriva šifra! Ponovite login <a href="../login.html">ovdje</a>';
            $id = "";
            include("template.php");
            exit();
        }
        else {
            $Poruka = 'Korisnik ' . $username . ' ne postoji. Registirirajte se <a href="../registracija.html">ovdje</a>';
            $id = "";
            include("template.php");
            exit();
        }
    }
}
$query = "SELECT * FROM Clanci ORDER BY Datum DESC, VrijemeIzrade DESC";
$result = mysqli_query($dbc, $query);

if(isset($_SESSION['accessLevel']) &&  $_SESSION['accessLevel'] >= 2 ) {
    echo '
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="../style.css" />
    <link rel="icon" href="../../res/circle_logo_fav.png" type="image/gif" sizes="16x16">
    <title>Vijesti 747</title>
</head>
<body>
';

    $id = "admin";
    include("../header.php");

    echo '
<div id="main_content">
    <main>
        <h1 class="center">Administracija news portala</h1>
        <br>
        <p><a class="button float_left" href="unos.html">Unos vijesti</a></p>
        <button onclick="document.getElementById(\'id01\').style.display=\'block\'" class="float_left">Brzi unos vijesti</button>
        <span class="clear_floating"></span>
        
        <div id="id01" class="modal">
        <span onclick="document.getElementById(\'id01\').style.display=\'none\'"
            class="close" title="Zatvori brzi unos vijesti">&times;</span>

  <!-- Modal Content -->
  <form enctype="multipart/form-data" name="unos" action="skripta.php" method="post" class="forma modal-content animate" onsubmit="return provjera()">
    <div class="container">
      <label for="naslov">Naslov vijesti:</label>
        <input id="naslov" type="text" name="naslov"/>
        <br>
        <span style="color:red" id="naslovPoruka"></span>
        <br>
        <textarea name="kratkiSadrzaj" id="kratkiSadrzaj" style="width: 95%; height: 100px;"
                  placeholder="Kratki sadžaj vijesti"></textarea>
        <br>
        <span style="color:red" id="kratkiSadrzajPoruka"></span>
        <br>
        <textarea name="tekst" id="glavniTekst" style="width: 95%; height: 200px;" placeholder="Tekst nove vijesti."></textarea>
        <br>
        <span style="color:red" id="glavniTekstPoruka"></span>
        <br>
    </div>

    <div class="container" style="background-color:#f1f1f1">
        <input type="hidden" name="MAX_FILE_SIZE" value="1048576"/>
        <label for="picture">Priloži sliku:</label>
        <input type="file" id="picture" name="picture"/>
        <br>
        <br>
        <select name="vrstaVijesti">
            <option value="Putovanja">Putovanja</option>
            <option value="ClickBait">ClickBait</option>
        </select>
        <input type="checkbox" id="checkBoxSakrij" name="sakrijVijest" value="sakrijVijest" title="Sakrij Vijest"/>Sakrij
        Vijest
        <input id="gumb" type="submit" value="Submit">
    </div>
  </form>
</div>
<br>
        ';
    echo '<div class="clear_floating" style="overflow-x:auto;"><table id="administratorTable">';
    echo '
<tr>
    <th>Naslov</th>
    <th>Datum</th>
    <th>Vrijeme</th>
    <th>Autor</th>
    <th>Akcija</th>
</tr>
';
    while ($row = mysqli_fetch_array($result)) {
        $color = 'value="Prikaži"';
        if ($row["Sakrivena"] == 0) {
            $color = 'value="Sakrij" style="background-color: gray; width: 79px;"';
        }

        $idUser = $row['idUser'];
        $query2 = "SELECT * FROM Users WHERE id='$idUser'";
        $result2 = mysqli_query($dbc, $query2);
        $row2 = mysqli_fetch_array($result2);

        echo '<tr>';
        echo '<td><a href="../clanak.php?id=' . $row['id'] . '" target="_blank">' . urldecode($row['Naslov']) . '</a></td>';
        echo '<td>' . $row['Datum'] . ' </td>';
        echo '<td>' . $row['VrijemeIzrade'] . '</td>';
        echo '<td><a href="../autor.php?autorId='. $idUser .'">' . $row2['korisnickoIme'] . '</a></td>';
        echo '<td>';
        echo '<div class="float_left" style="padding: 5px;"><form  name="sakrij" action="sakrij.php" method="post">
						<input type="hidden" name="sakrij" value="' . $row['id'] . '" />
						<input type="hidden" name="sakriveno" value="' . $row['Sakrivena'] . '" />
						<input type="submit"  ' . $color . '>
				 </form></div>
			';
        echo '<div class="float_left" style="padding: 5px;"><form  name="izbrisi" action="delete.php" method="post">
						<input type="hidden" name="izbrisi" value="' . $row['id'] . '" />
						<input type="submit" value="Izbriši" style="background-color: red;">
				 </form></div>
			';

        echo '</td></tr>';
    }
    echo '</table></div>';
    echo '
    </main>
</div>
';
    include("../footer.php");
    echo '
</body>
<script>
        // Get the modal
        let modal = document.getElementById(\'id01\');
        
        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    </script> 
    <script src="validacija.js"></script>
</html>

';
}
else{
    $Poruka = 'Nemate dovoljno prava za pristup ovoj stranici';
    $id = "";
    include("template.php");
    exit();
}
mysqli_close($dbc);
