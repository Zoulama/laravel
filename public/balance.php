<?php
$filename = 'logs.txt'; // Logs
$now = new DateTime();
$content = $now->format('Y-m-d H:i:s') . " : " . $_SERVER['QUERY_STRING'] . "\n\r";

/**
 * Connexion avec la base de données
 */
function databaseConnect($host, $database, $user, $password) {
	return new PDO("mysql:host={$host};dbname={$database}", $user, $password);
}

try {
	// $bdd = databaseConnect("localhost", "ruches", "root", "");
	$bdd = databaseConnect("db696345767.db.1and1.com", "db696345767", "dbo696345767", "SB33HForTheWin!");
} catch (Exception $e) {
	die("Erreur : {$e->getMessage()}");
}

// Fichier de log
//SI fichier est accessible en écriture
if (is_writable($filename)) {
	// Ouverture fichier $filename en mode d'ajout, le pointeur est placé à la fin et $content sera placé
	if (!$handle = fopen($filename, 'a+')) {
		echo "Impossible d'ouvrir le fichier ($filename)";
		exit;
	}

	// Ecriture dans le fichier
	if (fwrite($handle, $content) === FALSE) {
		echo "Impossible d'écrire dans le fichier ($filename)";
		exit;
	} fclose($handle);
} else {
	echo "Le fichier $filename n'est pas accessible en écriture.";
}
// header("Location: http://{$_SERVER['HTTP_HOST']}/feed?{$_SERVER['QUERY_STRING']}");
// die();

/*
 Réception de la requete envoyée par les balances
 imei:imeil / p:poids / t:temperature  / h:hygrometrie / vb:tension
 sbeeh.fr/balance.php?imei=867567021200559&p=1&t=2&h=3&vb=4

 Simulation envoit des données de la balance
 	127.0.0.1:8000/balance.php?imei=867959030028283&h=30&vb=4&t=20&p=10
 
	 // Balance 33
	127.0.0.1:8000/balance.php?imei=867959030003677&h=30&vb=4&t=20&p=10
*/

// SI l'imei existe on le récupère
if (isset($_GET["imei"])) {
	$scales = $bdd->prepare("SELECT * FROM `scales`
	WHERE `imei` = :imei");

	$scales->execute([
		"imei" => $_GET["imei"]
	]);
	// tableau de tous les enregistrements
	$row = $scales->fetchAll(); 

	// Si le tableau n'est pas vide
	if (!empty($row)) {
		$scale = $row[0]; // ligne courante

		// SI Tare pas initialisée OU coefficient de poids, on les initialise
		if (is_null($scale['tare']) || is_null($scale['weight_coefficient'])) {
			// Tare à 0
			if(is_null($scale['tare']) ) {
				$tare = "0.00";
			}
			
			// Coefficient de poids à 0
			if(is_null($scale['weight_coefficient'])) {
				$weight_coefficient = "1.000";
			}

			$update = $bdd->prepare("UPDATE `scales`
			SET `tare` = :tare,
				`weight_coefficient` = :weight_coefficient
			WHERE `id` = :id");

			$update->execute([
				"id" => $scale['id'],
				"tare" => $tare,
				"weight_coefficient" => $weight_coefficient
			]);
		}

		/*
		 * Calcul du poids (Tare)
		 * Tn: rien de bien spécifique hormis la correction du poids
		 * Pn(réel) = Pn(reçu)*C + (Tn-T0)*0.02
		 * corrrection Pn(réel)=Pn(reçu)*C + tare
		 */
		else {
			// Dernière requête
			$tare = $scale['tare'];
			$weight_coefficient = $scale['weight_coefficient'];

			$weight = floatval($_GET["p"]); // poids reçu par la balance
			//$weight *= $scale['weight_coefficient'] - $tare  * 0.02; // poids * coeff Droite
			
			// Poids * coefficient - tare
			$weight = ($weight * $weight_coefficient) - $tare;
		
			// Table scale_reports;
			$insert = $bdd->prepare("INSERT INTO `scale_reports`
			(`scale_id`, `at`, `hygrometry`, `battery_level`, `temperature`, `weight`)
			VALUES (:scale_id, NOW(), :hygrometry, :battery_level, :temperature, :weight)");

			$insert->execute([
				"scale_id" => $scale['id'],
				"hygrometry" => floatval($_GET["h"]),
				"battery_level" => floatval($_GET["vb"]),
				"temperature" => floatval($_GET["t"]),
				"weight" => $weight
			]);
		}
	}
	else {
		die("L'IMEI '$_GET[imei]' ne correspond à rien de connu");
	}
}

// try
// {
//  // $bdd = new PDO('mysql:host=db583173186.db.1and1.com;dbname=db583173186','dbo583173186','//B00tzam41');
//   // $bdd = new PDO('mysql:host=db672634604.db.1and1.com;dbname=db672634604','dbo672634604','517158706');
// 	$bdd = new PDO('mysql:host=db672634604.db.1and1.com;dbname=db672634604','dbo672634604','SB33HForTheWin!');
// }
// catch(Exception $e)
// {
//         die('Erreur : '.$e->getMessage());
// }

// $req = $bdd->prepare("INSERT INTO balance VALUES('',?,?,?,?,?,now());");
// $req->execute(array(
// 	         $_GET["imei"],
//              $_GET["p"],
// 	         $_GET["h"],
// 	         $_GET["t"],
// 	         $_GET["vb"]
//       //	'P8' => $_GET["p8"]
// 	));

// $reponse->closeCursor();
// $bdd=null;
?>
