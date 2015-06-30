<?php
/*
*
*    Copyright 2004 Eric Boniface
*    This file is part of Intrambox.
*
*    Intrambox is free software; you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation; either version 2 of the License, or
*    (at your option) any later version.
*
*    Intrambox is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with Intrambox; if not, write to the Free Software
*    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*
*/
?>
<html>
<head>
<style type="text/css" media="screen">
@import url( player.css );
</style>
<?php
include("config.inc.php");
$link = mysql_connect($db_host,$db_utilisateur ,$db_passwd )
or die("Impossible de se connecter");

mysql_select_db($db_name) or die($db_name." n'existe pas");

if(count($HTTP_GET_VARS)>0){
	$numero=$HTTP_GET_VARS["numero"];
	$action=$HTTP_GET_VARS["action"];
	switch($action){
		case "del":
			/*suppression d'un titre de la playlist*/
			$requete="DELETE FROM playlists WHERE idplaylists=".$numero;
			mysql_query($requete);
	
			$requete="DELETE FROM chanson_playlists WHERE idplaylists=".$numero;
			mysql_query($requete);
			break;
		case "copie":
			/*copie de la playlist dans la playlist courante*/
			$requete="TRUNCATE TABLE playlist;";
			mysql_query($requete);
			
			$requete="insert into playlist(chanson) select chanson from chanson_playlists where idplaylists=".$numero." order by idchanson";
			mysql_query($requete);
			break;
		case "ajoute":
			/*ajoute la playlist a la playlist courante*/
			$requete="insert into playlist(chanson) select chanson from chanson_playlists where idplaylists=".$numero." order by idchanson";
			mysql_query($requete);
			break;
		default :
			break;
	}

}
?>
<title>
Playlists
</title>
</head>
<body>
<?php

echo "<p>[<a href=\"index.php\">Retour &agrave; la playlist</a>]</p>"; 

$requete="SELECT * FROM playlists ORDER by idplaylists;";
$playliste=mysql_query($requete);

/* affichage des playlists */
echo "<h1>Playlists:</h1>";
echo "<table>";
while($ligne=mysql_fetch_array($playliste)){
	echo "<tr>";
	echo "<td><a href=\"liste.php?numero=".$ligne[0]."&action=del\">";
	echo "<img src=\"images/poubelle.png\" alt=\"Effacer\"></a></td>";
	
	if($action=="select" && $numero==$ligne[0]){
		echo "<td><a href=\"liste.php\">".$ligne[1]."</a></td>";
		echo "<td>[<a href=\"liste.php?numero=".$ligne[0]."&action=copie\">S&eacute;lectionne</a>]</td>";
		echo "<td>[<a href=\"liste.php?numero=".$ligne[0]."&action=ajoute\">Ajoute</a>]</td>";
		$requete="select * from chanson_playlists where idplaylists=".$numero." order by idchanson";
		$chansons=mysql_query($requete);
		while($chanson=mysql_fetch_array($chansons)){
			echo "<tr><td></td><td>";
			$titre=substr($chanson[1], strlen($repertoire_fichiers)-1);
			echo $titre;	
			echo "</td><td></td></tr>";
		}
	}else{
		echo "<td><a href=\"liste.php?numero=".$ligne[0]."&action=select\">".$ligne[1]."</a></td>";
		echo "<td>[<a href=\"liste.php?numero=".$ligne[0]."&action=copie\">S&eacute;lectionne</a>]</td>";
		echo "<td>[<a href=\"liste.php?numero=".$ligne[0]."&action=ajoute\">Ajoute</a>]</td>";
	}
	echo "</tr>";
}

mysql_close($link);

?>
</table>
</body>
</html>
