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
<meta http-equiv="refresh" content="1; url=index.php">
<script>
<!--
function efface()
{
	if(confirm('Effacer la playlist?'))self.open('index.php?numero=&action=drop');
}
//-->
</script>
<?php
include("config.inc.php");

$link = mysql_connect($db_host,$db_utilisateur ,$db_passwd )
	or die("Impossible de se connecter");

mysql_select_db($db_name) or die($db_name." n'existe pas");


function kill_chanson($num_chanson,$signal)
{
	$requete="SELECT * FROM playlist WHERE cle_playlist=".$num_chanson;

	$res_requete=mysql_query($requete);
	$resultat=mysql_fetch_array($res_requete);
	$chanson=$resultat[1];
	$tab_ext=explode(".",$chanson);
	$extension=$tab_ext[(count($tab_ext))-1];
	
	$requete="SELECT commande FROM lecteur WHERE extension='".$extension."'";
	$resultat=mysql_fetch_array(mysql_query($requete));
	$commande=$resultat[0];
	$tab_proc=explode("/",$commande);
	$processus=$tab_proc[(count($tab_proc))-1];
	switch($signal){
		case "kill":
			exec("pkill -CONT ".$processus);
			exec("pkill ".$processus);
			break;
		case "cont":
			exec("pkill -CONT ".$processus);
			break;
		case "stop":
			exec("pkill -STOP ".$processus);
			break;
	}
		
}

function chanson_courante(){
	/*selection du morceau sur lequel l'utilisateur a clique*/

	/*recuperation du numero de chanson en cours de lecture*/
	$requete="SELECT cle_lecture FROM lecture";
	$res_requete=mysql_query($requete);
	$resultat=mysql_fetch_array($res_requete);
	$num_courante=$resultat[0];
	return $num_courante;
}
	
/*actions a effectues en fonction des parametres*/
if(count($HTTP_GET_VARS)>0){
	$numero=$HTTP_GET_VARS["numero"];
	$action=$HTTP_GET_VARS["action"];
	switch($action){
		case "del":
			/*suppression d'un titre de la playlist*/
			$requete="DELETE FROM playlist WHERE cle_playlist=".$numero;
			mysql_query($requete);
	
			break;
		case "play":
			/*lancement de la lecture de la playlist*/
			exec ($repertoire_shell."go.sh");	
			break;
		case "plusvol":
		case "moinsvol":
			/* modification du volume*/
			exec ($repertoire_shell.$action.".sh");
			break;
		case "suivant":
			/* sauter au titre suivant, en arretant de la lecture du titre en cours*/
			$numero=chanson_courante();
			kill_chanson($numero,"kill");
			break;
		case "precedent":
			/* sauter au titre precedent */
			$numero=chanson_courante();

			/*recuperation de la chanson precedente*/
			$requete="SELECT MAX(cle_playlist) FROM playlist WHERE cle_playlist<".$numero;
			$res_requete=mysql_query($requete);
			$resultat=mysql_fetch_array($res_requete);
			$num=$resultat[0];
			
			/*recuperation de la chanson precedent la chanson precedente*/
			$requete="SELECT MAX(cle_playlist) FROM playlist WHERE cle_playlist<".$num;
			$res_requete=mysql_query($requete);
			$resultat=mysql_fetch_array($res_requete);
			if($resultat[0]==""){
				$num=-1;
			}else{ 
				$num=$resultat[0];
			}

			/*mise à jour de la table lecture*/
			$requete="UPDATE lecture SET cle_lecture=".$num;
			mysql_query($requete);
	
			kill_chanson($numero,"kill");

			break;
		case "goto":
			/*selection du morceau sur lequel l'utilisateur a clique*/

			/*recuperation du numero de chanson en cours de lecture*/
			$requete="SELECT cle_lecture FROM lecture";
			$res_requete=mysql_query($requete);
			$resultat=mysql_fetch_array($res_requete);
			$num_courante=$resultat[0];
		
			/*recuperation de la chanson precedente de la chanson selectionnée*/
			$requete="SELECT MAX(cle_playlist) FROM playlist WHERE cle_playlist<".$numero;
			$res_requete=mysql_query($requete);
			$resultat=mysql_fetch_array($res_requete);
			if($resultat[0]=="")$num=-1;
			else $num=$resultat[0];
			
			/*mise à jour de la table lecture*/
			$requete="UPDATE lecture SET cle_lecture=".$num;
			mysql_query($requete);
	
			kill_chanson($num_courante,"kill");	
			break;
		case "stop":
			$numero=chanson_courante();
			exec ("pkill python");
			kill_chanson($numero,"kill");
			break;
		case "drop":

			$numero=chanson_courante();
			if($numero!=-1){
				exec ("pkill python");
				kill_chanson($numero,"kill");
			}

			$requete="TRUNCATE TABLE playlist;";
			mysql_query($requete);
			break;
		case "sauve":
			$timestamp=getdate();			

			$nom_playlist="Playlist du ".$timestamp["mday"]."/".$timestamp["month"]."/".$timestamp["year"];
			$nom_playlist=$nom_playlist." ".$timestamp["hours"].":".$timestamp["minutes"].":".$timestamp["seconds"];

			$requete="insert into playlists (libplaylists) values (\"".$nom_playlist."\")";
			mysql_query($requete);

			$requete="select idplaylists from playlists where libplaylists=\"".$nom_playlist."\"";
			$res_requete=mysql_query($requete);
			$resultat=mysql_fetch_array($res_requete);

			$idplaylists=$resultat[0];

			$requete="insert into chanson_playlists(chanson, idplaylists) select chanson, ".$idplaylists." as toto from playlist;";
			mysql_query($requete);

			break;
		case "repete":
			$requete="select repetition from lecture";
			$res_requete=mysql_query($requete);
			$resultat=mysql_fetch_array($res_requete);

			$repetition=$resultat[0];

			if($repetition==0)$repetition=1;
			else $repetition=0;

			
			$requete="update lecture set repetition=".$repetition;
			mysql_query($requete);

			break;
		case "pause":
			$numero=chanson_courante();

			$requete="select pause from lecture";
			$res_requete=mysql_query($requete);
			$resultat=mysql_fetch_array($res_requete);

			$pause=$resultat[0];

			if($pause==0){
				$pause=1;
				kill_chanson($numero,"stop");
			}else{ 
				$pause=0;
				kill_chanson($numero,"cont");
			}
			
			$requete="update lecture set pause=".$pause;
			mysql_query($requete);
			break;

		default:
			break;
	}

}else{

	/*recuperation du numero de chanson en cours de lecture*/
	$requete="SELECT cle_lecture FROM lecture";
	$res_requete=mysql_query($requete);
	$resultat=mysql_fetch_array($res_requete);
	$numero=$resultat[0];
		
	/*recuperation de la chanson precedente de la chanson selectionnée*/
	$requete="SELECT chanson FROM playlist WHERE cle_playlist=".$numero;
	$res_requete=mysql_query($requete);
	$resultat=mysql_fetch_array($res_requete);
	$titre=$resultat[0];
}
	
echo "<title>";
if($titre=="")echo "Intrambox";
else
	echo substr($titre, strlen($repertoire_fichiers)-1);
?>
</title>
</head>
<body>
<?php

$requete="SELECT * FROM playlist ORDER by cle_playlist;";
$playliste=mysql_query($requete);

$requete="SELECT * FROM lecture;";
$ligne=mysql_fetch_array(mysql_query($requete));
$numero=$ligne[0];

echo "[<a href=\"dir.php?repertoire=".$repertoire_fichiers."&titre=&action=\">";
echo "Parcourir les fichiers</a>]";
echo " &nbsp; &nbsp; [<a href=\"javascript:efface();\">Effacer la playlist</a>]";
echo " &nbsp; &nbsp; [<a href=\"liste.php\">Les playlists</a>]";
echo " &nbsp; &nbsp; [<a href=\"index.php?numero=&action=sauve\">Sauvegard&eacute; la playlist</a>]";
echo "<br /><br />";

if($numero!=-1){
	echo "[<a href=\"index.php?numero=&action=stop\">Stop</a>] &nbsp; ";
	echo "[<a href=\"index.php?numero=&action=suivant\">Suivant</a>] &nbsp; ";
	echo "[<a href=\"index.php?numero=&action=precedent\">Pr&eacute;c&eacute;dent</a>] &nbsp; ";
	echo " &nbsp; Volume: ";
	echo "[<a href=\"index.php?numero=&action=plusvol\">+</a>] &nbsp; ";
	echo "[<a href=\"index.php?numero=&action=moinsvol\">-</a>] &nbsp; ";

	$requete="select repetition, pause from lecture";
	$res_requete=mysql_query($requete);
	$resultat=mysql_fetch_array($res_requete);

	$repetition=$resultat[0];
	$pause=$resultat[1];

	if($repetition)$repete="En boucle";
	else $repete="Une fois";

	echo " &nbsp; R&eacute;p&eacute;tition: ";
	echo "[<a href=\"index.php?numero=&action=repete\">";
	echo $repete;
	echo "</a>]";

	if($pause)$enpause="Continuer";
	else $enpause="Pause";

	echo " &nbsp; ";
	echo "[<a href=\"index.php?numero=&action=pause\">";
	echo $enpause;
	echo "</a>]";
		
}else{
	echo "[<a href=\"index.php?numero=&action=play\">Jouer la playlist</a>]";
}

/* affichage de la playlist */
echo "<h1>Playlist:</h1>";
echo "<table>";
while($ligne=mysql_fetch_array($playliste)){
	echo "<tr>";
	/* affichage sur fond rouge du titre jouee*/
	$titre=substr($ligne[1], strlen($repertoire_fichiers)-1);
	if($ligne[0]==$numero){
		echo "<td><img src=\"images/play.png\" alt=\"Lecture\"></td>";
		echo "<td bgcolor=\"red\" >";
		echo $titre;
	}else{
		echo "<td><a href=\"index.php?numero=".$ligne[0]."&action=del\">";
		echo "<img src=\"images/poubelle.png\" alt=\"Effacer\"></a></td><td>";
		if($numero!=-1){
			echo "<a href=\"index.php?numero=".$ligne[0]."&action=goto\">";
			echo $titre."</a>";
		}else{
			echo $titre;
		}
	}
	echo "</td></tr>";
}

mysql_close($link);

?>
</table>
</body>
</html>
