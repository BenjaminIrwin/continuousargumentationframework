<?php

/* Funzione di connessione al DB. */

	function dbConnect(){
		$connection = ($GLOBALS["___mysqli_ston"] = mysqli_connect("localhost",  "root",  "root", NULL, 8889))
			or die("Errore nella connessione al server del DB: " . mysqli_error($GLOBALS["___mysqli_ston"]));
		mysqli_select_db($GLOBALS["___mysqli_ston"], QSD) or die("Errore nell'accesso al DB: " . mysqli_error($GLOBALS["___mysqli_ston"]));
		return $connection;
		}
		
//--------------------------------------------------------------------------------------------------------------------------		
	
	/* Funzione che ritorna vero o falso a seconda che le credenziali siano presenti nel DB. */
		
		function checkCredential($username,$password){

		
		$connection = dbConnect();
		// Effettuo l'update.
		$b=false;
		
		$username = empty($username) ? null : $username;
		$password = empty($password) ? null : $password;
		
		$sql = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM users WHERE (username='$username' OR email='$username') AND password=sha1('$password')") or die("Errore durante la query SQL: " . mysqli_error($GLOBALS["___mysqli_ston"]));
		
		
		while($row = mysqli_fetch_array($sql)){
			$b=$row['id'];
			
		}
		
		((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);
		
		return $b;
		
		}
		
//--------------------------------------------------------------------------------------------------------------------------

/* Funzione che ritorna un array con tutte le informazioni dell'utente. */

function getInformations($username){

		
		$connection = dbConnect();
		
		$username = empty($username) ? null : $username;

		$sql = "SELECT nome,cognome,immagine FROM utenti WHERE username=\"".$username."\"";
		mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die("Errore durante la query SQL: " . mysqli_error($GLOBALS["___mysqli_ston"]));
		
		$risposta = mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die("Errore durante la query SQL: " . mysqli_error($GLOBALS["___mysqli_ston"]));
		
		
		while($riga = mysqli_fetch_row($risposta)){
			
			$informazioni = array('nome'=>$riga[0], 'cognome'=>$riga[1],
                'immagine'=>$riga[2]);
		}
		
		((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);
		
		return $informazioni;
		
		}


//--------------------------------------------------------------------------------------------------------------------------
	
	/* Funzione che inserisce un dato punteggio per un dato utente nel DB. */
		
		function insertScore($username, $points){
		
			$position=getPosition($points);
			
			$connection = dbConnect();
		
			$username = empty($username) ? null : $username;

			$sql = "INSERT INTO punteggi( utente, punti, posizione ) VALUES(\"".$username."\",\"".$points."\",\"".$position."\")";
			mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die("Errore durante la query SQL: " . mysqli_error($GLOBALS["___mysqli_ston"]));
			((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);
			
			printScores($username, $points);
			
			}
		
//---------------------------------------------------------------------------------------------------------------------------

/* Funzione che ritorna un array con i risultati dell'utente. */

		function getUserScores($username){
		
			$points = array();
			
			$connection = dbConnect();
		
			$username = empty($username) ? null : $username;

			$sql = "SELECT id, posizione, punti, data FROM punteggi WHERE utente=\"".$username."\" ORDER BY posizione ASC";
			mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die("Errore durante la query SQL: " . mysqli_error($GLOBALS["___mysqli_ston"]));
		
			$risposta = mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die("Errore durante la query SQL: " . mysqli_error($GLOBALS["___mysqli_ston"]));
		
		
			while($riga = mysqli_fetch_row($risposta)){
				
				
				$arr = array('id'=>$riga[0], 'posizione'=>$riga[1], 'punti'=>$riga[2], 'data'=>$riga[3]);
				array_push($points, $arr);
			}
		
            ((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);
		
            return $points;

		}


//---------------------------------------------------------------------------------------------------------------------------

/* Funzione che ritorna tutti i risultati. */

		function getFinalScore(){
			$score = array();
			
			$connection = dbConnect();
		
			$username = empty($username) ? null : $username;

			$sql = "SELECT DISTINCT posizione, utente, punti FROM punteggi ORDER BY punti DESC";
			mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die("Errore durante la query SQL: " . mysqli_error($GLOBALS["___mysqli_ston"]));
		
			$risposta = mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die("Errore durante la query SQL: " . mysqli_error($GLOBALS["___mysqli_ston"]));
		
		
			while($riga = mysqli_fetch_row($risposta)){
			
				$arr = array('posizione'=>$riga[0], 'utente'=>$riga[1], 'punti'=>$riga[2]);
            	array_push($score, $arr);   
            	}
            ((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);
		
            return $score;
		
		}

//---------------------------------------------------------------------------------------------------------------------------

/* Funzione che aumenta di una posizione tutti i punteggi inferiori al parametro passato. */

		function updatePosition($points){
		
			$connection = dbConnect();

			$sql = "UPDATE punteggi SET posizione=posizione+1 WHERE punti<".$points."";
			mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die("Errore durante la query SQL: " . mysqli_error($GLOBALS["___mysqli_ston"]));
			((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);
		}

//---------------------------------------------------------------------------------------------------------------------------

/* Funzione che calcola la posizione di un dato punteggio e che chiama updatePosition. */

		function getPosition($points){
			$score = getFinalScore();
			$pos = 0;
			
			foreach($score as $elemento){
				$pos = $elemento['posizione'];
				if($points>$elemento['punti']){
					updatePosition($points);
					return $pos;
					}
					else
						if($points==$elemento['punti'])return $pos;
							
			}
			
			return $pos+1;
			
		}
		
	



//---------------------------------------------------------------------------------------------------------------------------

/* Funzione che recupera la migliore posizione di un dato utente. */

		function getBestPosition($username){
		
			$position = 0;
			
			$connection = dbConnect();
		
			$username = empty($username) ? null : $username;

			$sql = "SELECT MIN(posizione) FROM punteggi WHERE utente=\"".$username."\"";
			mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die("Errore durante la query SQL: " . mysqli_error($GLOBALS["___mysqli_ston"]));
		
			$risposta = mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die("Errore durante la query SQL: " . mysqli_error($GLOBALS["___mysqli_ston"]));
		
		
			while($riga = mysqli_fetch_row($risposta)){
			
				$position=$riga[0];
			}
			
            ((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);
            
            return $position;
			
		}
		
//---------------------------------------------------------------------------------------------------------------------------

/* Controlla se un dato utente è nel DB. */

		function checkUserInDB($username){
		
			$b=false;
		
			$connection = dbConnect();
		
			$username = empty($username) ? null : $username;

			$sql = "SELECT username FROM users WHERE username=\"".$username."\"";
			mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die("Errore durante la query SQL: " . mysqli_error($GLOBALS["___mysqli_ston"]));
		
			$answer = mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die("Errore during the SQL query: " . mysqli_error($GLOBALS["___mysqli_ston"]));
			
            ((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);
            
            while($row = mysqli_fetch_row($answer)){
			
				$b=true;
            	
			}
			
			return $b;
            
       }

//---------------------------------------------------------------------------------------------------------------------------

/* Controlla se un dato utente è nel DB. */

		function checkEMailInDB($email){
		
			$b=false;
		
			$connection = dbConnect();
		
			$email = empty($email) ? null : $email;

			$sql = "SELECT email FROM users WHERE email=\"".$email."\"";
			mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die("Errore durante la query SQL: " . mysqli_error($GLOBALS["___mysqli_ston"]));
		
			$answer = mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die("Errore during the SQL query: " . mysqli_error($GLOBALS["___mysqli_ston"]));
			
            ((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);
            
            while($row = mysqli_fetch_row($answer)){
			
				$b=true;
            	
			}
			
			return $b;
            
       }

//---------------------------------------------------------------------------------------------------------------------------

/* Controlla se un dato punteggio è nel DB. */

		function checkPointsInDB($points){
		
			$b=false;
		
			$connection = dbConnect();


			$sql = "SELECT punti FROM punteggi WHERE punti=\"".$points."\"";
			mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die("Errore durante la query SQL: " . mysqli_error($GLOBALS["___mysqli_ston"]));
		
			$risposta = mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die("Errore durante la query SQL: " . mysqli_error($GLOBALS["___mysqli_ston"]));
			
            ((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);
            
            while($riga = mysqli_fetch_row($risposta)){
			
				$b=true;
            	
			}
			
			return $b;
            
       }
		
//---------------------------------------------------------------------------------------------------------------------------

/* Inserisce un nuovo utente nel DB. */

		function insertUser($username, $email, $password, $expertise){
			
			$connection = dbConnect();
		
			$username = empty($username) ? null : $username;

			$sql = "INSERT INTO users( username, email, password, expertise) VALUES(\"".$username."\",\"".$email."\",sha1(\"".$password."\"),\"".$expertise."\")";
			mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die("Errore durante la query SQL: " . mysqli_error($GLOBALS["___mysqli_ston"]));
			return ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
			((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);
			
		}
		

//---------------------------------------------------------------------------------------------------------------------------

/* Cancella un dato punteggio dal DB. */

		function deleteScore($id){
			$connection = dbConnect();
		
			$points = 0;
			
			
			// Rescalo tutti i punteggi.
			
			$sql = "SELECT punti FROM punteggi WHERE id=\"".$id."\"";
			mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die("Errore durante la query SQL: " . mysqli_error($GLOBALS["___mysqli_ston"]));
		
			$risposta = mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die("Errore durante la query SQL: " . mysqli_error($GLOBALS["___mysqli_ston"]));
			
            
            
            while($riga = mysqli_fetch_row($risposta)){
			
				$points = $riga[0];
            	
			}
			
			$sql = "DELETE from punteggi WHERE id=\"".$id."\"";
			mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die("Errore durante la query SQL: " . mysqli_error($GLOBALS["___mysqli_ston"]));
			((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);
			
			// Se non c'è nessun altro punteggio simili rescalo gli altri punteggi.

			if(!checkPointsInDB($points))downgradePosition($points); 			
			
			
		}
//---------------------------------------------------------------------------------------------------------------------------

/* Funzione che diminuisce di una posizione tutti i punteggi minori del parametro passato. */

		function downgradePosition($points){
		
			$connection = dbConnect();

			$sql = "UPDATE punteggi SET posizione=posizione-1 WHERE punti<".$points."";
			mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die("Errore durante la query SQL: " . mysqli_error($GLOBALS["___mysqli_ston"]));
			((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);
		}

//---------------------------------------------------------------------------------------------------------------------------

/* Effettua la modifica del campo immagine del DB. */

		function uploadImage($username, $path){
			$connection = dbConnect();
		
		
			$sql = "UPDATE utenti SET immagine=\"".$path."\" WHERE username=\"".$username."\";";
			mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die("Errore durante la query SQL: " . mysqli_error($GLOBALS["___mysqli_ston"]));
			((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);
		}
		
//---------------------------------------------------------------------------------------------------------------------------

/* Ritorna l'immagine dell'utente. */

		function getUserImage($username){
			$b=false;
			
			$connection = dbConnect();
			
			$sql = "SELECT immagine FROM utenti WHERE username=\"".$username."\"";
			mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die("Errore durante la query SQL: " . mysqli_error($GLOBALS["___mysqli_ston"]));
		
			$risposta = mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die("Errore durante la query SQL: " . mysqli_error($GLOBALS["___mysqli_ston"]));
			
            
            
            while($riga = mysqli_fetch_row($risposta)){
			
				return $riga[0];
            	
			}
			
			return $b;
		}
		
//---------------------------------------------------------------------------------------------------------------------------

/* Stampa una tabella con la classifica. */

		function printScores($username, $points){
			
			$score = getFinalScore();
		
			echo "
					<table id=\"box-table-a\">
					<thead><tr>
						<td>Posizione</td>
						<td>Utente</td>
						<td>Punteggio</td>
					</tr></thead>";
					
			$i=0;
			foreach($score as $elemento) {
			
				if($i<8000){
					if($elemento["utente"]==$username && $elemento["punti"]==$points) echo "<tr class=\"selected_tr\">";
						else
							echo "<tr>";
						echo "<td>".$elemento['posizione']."</td>
						<td>".$elemento["utente"]."</td>
						<td>".$elemento["punti"]."</td>
						</tr>";
						
						$i++;
						}		
				}

			echo "</table>";
		}


// Funzione che non accede al DB. Effettua il replace con codice HTML di alcuni caratteri speciali.

function changeChar($dato){
	$dato = str_replace("€", "&euro;",   $dato);
	$dato = str_replace("è", "&egrave;", $dato);
	$dato = str_replace("é", "&eacute;", $dato);
	$dato = str_replace("à", "&agrave;", $dato);
	$dato = str_replace("À", "&Agrave;", $dato);
	$dato = str_replace("á", "&aacute;", $dato);
	$dato = str_replace("ò", "&ograve;", $dato);
	$dato = str_replace("ó", "&oacute;", $dato);
	$dato = str_replace("ì", "&igrave;", $dato);
	$dato = str_replace("í", "&iacute;", $dato);
	$dato = str_replace("ù", "&ugrave;", $dato);
	$dato = str_replace("ú", "&uacute;", $dato);
	$dato = str_replace("ñ", "&ntilde;", $dato);
	$dato = str_replace("ç", "&ccedil;", $dato);
	$dato = str_replace("'", "&#039;",   $dato);
	$dato = str_replace('"', "&quot;",   $dato);
	$dato = str_replace('°', "&deg;",    $dato);
	$dato = str_replace('’', "&acute;",  $dato);
	$dato = str_replace('»', "&raquo;",  $dato);
	$dato = str_replace('«', "&laquo;",  $dato);
	$dato = str_replace('“', "&quot;",   $dato);
	$dato = str_replace('”', "&quot;",   $dato);
	$dato = str_replace('®', "&reg;",    $dato);
	$dato = str_replace("™", "&trade;",  $dato);

	return $dato;
}



?>