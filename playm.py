#
#    Copyright 2004 Eric Boniface
#    This file is part of Intrambox.
#
#    Intrambox is free software; you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation; either version 2 of the License, or
#    (at your option) any later version.
#
#    Intrambox is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with Intrambox; if not, write to the Free Software
#    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
import MySQLdb
import os
import string
import signal

from config import *

def sortie():
	#fonction execute à la sortie du programme
	global db
	
	res=db.cursor()
	requete="UPDATE lecture SET cle_lecture=-1, pause=0"
	res.execute(requete)
	res.close()
	
	db.close()

def capture_signal(signal,frame):
	#focntion execute en cas d'envoi d'un signal sigkill
	global lecture; lecture=0

def lecture_playlist():
	global lecture, db
	res=db.cursor()
	res.execute("SELECT * FROM playlist ORDER BY cle_playlist;")
	retour=res.fetchone()
	res.close()
	while retour!=None and lecture:
		cle=retour[0]

		#mise à jour du num de fichier en cours de lecture
		res=db.cursor()
		requete="UPDATE lecture SET cle_lecture="+str(cle)
		res.execute(requete)
		res.close()
	
		pospoint=string.rfind(retour[1],".")
		if pospoint!=-1 and pospoint!=len(retour[1]):
			#recuperation de l'extension du fichier
			extension=retour[1][(pospoint+1):]
			res=db.cursor()
			#recherche de lecteur associé à cette extension
			requete="SELECT commande FROM lecteur WHERE extension='"+extension+"'"
			res.execute(requete)
			retourlecteur=res.fetchone()
			res.close
			if retourlecteur!=None:
				#execution de la lecture
				os.system(rep_script+"playm.sh '"+retourlecteur[0]+"' '"+retour[1]+"'")

		#recherche du numero de fichier en cour de lecture
		res=db.cursor()
		requete="SELECT * FROM lecture"
		res.execute(requete)
		retour=res.fetchone()
		cle=retour[0]
		res.close()
	
		#recherche du prochain fichier à jouer
		res=db.cursor()
		requete="SELECT * FROM playlist WHERE cle_playlist>"+str(cle)
		requete=requete+" ORDER BY cle_playlist;"
		res.execute(requete)
		retour=res.fetchone()
		res.close()
	

lecture=1
repetition=1
db=MySQLdb.connect(host=db_host, user=db_user, passwd=db_passwd, db=db_name)
signal.signal(signal.SIGTERM, capture_signal)
while repetition and lecture:
	lecture_playlist()
	res=db.cursor()
	res.execute("SELECT repetition FROM lecture;")
	repetition=res.fetchone()[0]
	res.close()


sortie()
