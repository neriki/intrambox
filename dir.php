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
<title>Liste de 
<?php
/*
*
*	Parcours de l'arborescence
*
*/

include("config.inc.php");
$repertoire=$HTTP_GET_VARS["repertoire"];
$titre=$HTTP_GET_VARS["titre"];
$action=$HTTP_GET_VARS["action"];

/*Ajout dans la playlist*/
if($action=="ajout"){
	$link = mysql_connect($db_host,$db_utilisateur ,$db_passwd )
	or die("Impossible de se connecter");

	mysql_select_db($db_name) or die($db_name." n'existe pas");
	
	$requete="INSERT INTO playlist ( chanson ) VALUES ( '";
	$requete=$requete.$repertoire.$titre."' );";
	mysql_query($requete);
	
	mysql_close($link);

}

$rep_musique=substr($repertoire, strlen($repertoire_fichiers)-1);

echo $rep_musique;
?>
</title>
</head>
<body>
<?php
$handle=opendir($repertoire);
echo "<p>[<a href=\"index.php\">Retour &agrave; la playlist</a>]"; 
/* si on n'est pas à la racine de la liste des fichiers, on affiche un lien vers celle ci*/
if($repertoire!=$repertoire_fichiers){
	echo " &nbsp; ";
	echo "[<a href=\"dir.php?repertoire=".$repertoire_fichiers."&titre=&action=\">";
	echo "Retour &agrave; la liste des albums</a>]";
}	
echo "</p>";

echo "<p><h1>";
echo $rep_musique;
echo "</h1></p>";

echo "<table>";
/*parcours des fichiers et des rep de repertoire courant*/
while($file=readdir($handle))
	if ($file != "." && $file != ".."){
		if(is_dir($repertoire.$file)){
			echo"<tr><td><img src= \"images/repertoire.png\" alt=\"Rep\"></td><td><a href=\"dir.php?repertoire=".$repertoire.$file."/&titre=&action=\">";
			echo $file."</a></td></tr>";
		}
		else{
			echo "<tr><td><img src=\"images/musique.png\" alt=\"Musique\"></td><td><a href=\"dir.php?repertoire=".$repertoire."&titre=".$file."&action=ajout\">";
			echo $file."</a>";
			echo "</td></tr>";
		}
	}
?>
</table>
</body>
</html>
