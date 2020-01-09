<?php

namespace App\Traits;

trait ImeiTrait
{
	# liste des Reporting Body Identifier:
	# http://en.wikipedia.org/wiki/Reporting_Body_Identifier
	public static $RBI = array(
		"01", "10", "30", "33", "35",
		"44", "45", "49", "50", "51",
		"52", "53", "54", "86", "91",
		"98", "99"
	);

	# 20 consonnes
	public static $CONSONANTS = array("B","C","D","F","G","H","J","K","L","M","N","P","Q","R","S","T","V","W","X","Z");

	# 256 syllabes via http://www.lexique.org/listes/liste_syllabes.txt notamment
	public static $SYLLABLES = array("la","lai","te","dai","pa","si","de","re","dy","di","ci","se","ti","sai","sa","tai","ma","ra","co","li","mai","ta","vai","mi","ne","ri","ca","rai","vi","tu","pro","me","po","nu","ni","fi","le","pe","pre","cu","mo","to","ty","so","vu","ve","na","su","fai","pou","ze","va","sy","no","nai","vo","che","do","zi","pri","fa","ba","ny","ro","ji","ly","lo","ja","pu","tra","bi","deu","ga","pi","fe","bo","fo","py","za","zai","cha","da","bu","be","my","ce","cri","by","ry","tro","tru","cre","bli","pli","zo","ru","chi","pai","veu","tri","gra","sla","gne","pla","spe","gre","cla","du","ple","bra","bri","gu","go","mu","ho","seu","cai","ble","gy","cho","ju","vay","gri","gni","sra","je","sta","tre","geo","fu","cro","reu","gro","fra","cle","fre","vri","cra","ha","cli","pra","ria","bai","fle","psi","feu","gli","bry","fly","blo","gna","ste","pru","zy","bla","fri","lu","glo","gi","gno","vlo","clo","sti","bre","dra","flo","vre","fla","gai","try","he","neu","bru","spo","vra","plo","gla","ray","gle","sty","spi","sci","chu","sco","fro","fli","bro","teu","lia","sri","leu","gru","cru","meu","dre","sre","blu","ceu","fia","pia","clu","hai","bie","pay","hi","sle","nia","may","wa","vry","sca","day","glu","sto","spa","hu","gry","slo","hy","say","zri","bay","flu","puy","fay","beu","boy","zu","dui","dro","vro","buy","lay","zeu","dry","vli","gay","ksi","dri","heu","sno","we","vla","guy","scu","smo","tsi","fti","mne","tmo","fru","mau","pso","tsa","wo","kse");

	/**
	 * Par exemple avec l'IMEI 351557010202731
	 * 
	 * 1/ mélange de l'IMEI, on forme une chaine ainsi: le premier, le dernier, le second, l'avant dernier
	 * $imei = 315317525072001
	 * 
	 * 2/ on ajoute un 9 devant
	 * $imei = 9315317525072001
	 * 
	 * 3/ convertion en base 16, la chaine fera forcément 14 de long (entre 1ff973cafa8000 et 2386f26fc0ffff)
	 * $imei = 21183b600d4881
	 * 
	 * 4/ création de la référence (chaque nombre est divisé en pair de nombre qui fait référence à une syllabe)
	 * $reference = NU NE PRI CHI SAI GA FU
	 * 
	 * 5/ on inverse le tout et on concatène
	 * $reference = FUGA-SAICHIPRI-NENU
	 */
	protected function createReferenceFromIMEI($imei)
	{
		$takeFromHead = false;
		$arrIMEI = str_split($imei);
		$newIMEI = ["9"];

		while (count($arrIMEI) > 0) {
			$takeFromHead = ! $takeFromHead;

			if ($takeFromHead) {
				$newIMEI[] = array_shift($arrIMEI);
			}
			else {
				$newIMEI[] = array_pop($arrIMEI);
			}
		}

		$newIMEI = implode("", $newIMEI);
		$newIMEI = base_convert($newIMEI, 10, 16);

		$syllabes = $this->createSyllabesFrom($newIMEI);

		return implode('', array_slice($syllabes, 0, 2)) . '-' . implode('', array_slice($syllabes, 2, 3)) . '-' . implode('', array_slice($syllabes, 5));
	}

	protected function createSyllabesFrom($part)
	{
		$split_part = str_split($part);
		$indexes = array();
		$syllabes = array();

		for ($i = 0; $i < count($split_part); $i++) { 
			$index = "";
			if (isset($split_part[$i])) {
				$index .= $split_part[$i];
			}

			$i++;
			if (isset($split_part[$i])) {
				$index .= $split_part[$i];
			}

			$indexes[] = $index;
		}

		for ($i = 0; $i < count($indexes); $i++) {
			$syllabes[] = strtoupper(ImeiTrait::$SYLLABLES[base_convert($indexes[$i], 16, 10)]);
		}

		return array_reverse($syllabes);
	}

	/**
	 * Par exemple avec la référence FUGA-SAICHIPRI-NENU
	 * 
	 * 1/ on retire les "-" et 
	 * $reference = FUGASAICHIPRINENU
	 * $reference = NUNEPRICHISAIGAFU 
	 * 
	 * 2/ recherche des index dans SYLLABLES convertis en base 16
	 * $imei = 9315317525072001
	 * 
	 * 3/ reconstruction (en retirant le 9 en premier d'abord)
	 * $imei = 351557010202731
	 */
	protected function createIMEIFromReference($reference)
	{
		$newReference = str_replace("-", "", $reference);
		$imei = array();
		$even = false;
		$parts = $this->createReferenceFrom($newReference);
		$parts = substr($parts, 1);
		$parts = str_split($parts);
		$leftPart = array();
		$rightPart = array();

		while (count($parts) > 0) {
			$even = ! $even;

			if ($even) {
				$leftPart[] = array_shift($parts);
			}
			else {
				array_unshift($rightPart, array_shift($parts));
			}
		}

		return implode("", $leftPart) . implode("", $rightPart);
	}

	protected function createReferenceFrom($word)
	{
		$chars = str_split($word);
		$parts = array();

		$part = "";
		for ($i=0 ; $i<count($chars) ; $i++) {
			$isConsonant = in_array($chars[$i], ImeiTrait::$CONSONANTS);
			$part .= $chars[$i];
			if ( ! isset($chars[$i+1])) {
				$parts[] = $part;
				$part = "";
			}
			elseif ( ! $isConsonant && in_array($chars[$i+1], ImeiTrait::$CONSONANTS)) {
				$parts[] = $part;
				$part = "";
			}
		}

		$parts = array_reverse($parts);

		for ($i=0 ; $i<count($parts) ; $i++) {
			$index = base_convert(array_search(strtolower($parts[$i]), ImeiTrait::$SYLLABLES), 10, 16);
			while (strlen($index)<2) {
				$index = 0 . $index;
			}

			$parts[$i] = $index;
		}

		$part = base_convert(implode("", $parts), 16, 10);

		while (strlen($part)<7) {
			$part = 0 . $part;
		}

		return $part;
	}

	protected function isCompliant($imei)
	{
		$str = str_split($imei);

		# 15 chiffres seulement
		if (count($str)!=15) {
			return false;
		}

		# vérification du RBI
		$rbi = $str[0] . $str[1];

		if ( ! in_array($rbi, ImeiTrait::$RBI)) {
			return false;
		}

		// # vérification du chiffre de contrôle
		// $ctrl = $this->computeLuhnChecksum(substr($imei, 0, 14));
		// if ($ctrl!=$str[count($str)-1]) {
		// 	return false;
		// }

		return true;
	}

	protected function computeLuhnChecksum($imei)
	{
		$arr = str_split($imei);
		$sum = 0;
		$toggle = false;

		for ($i=count($arr)-1 ; $i>=0 ; $i--) { 
			if ($toggle) {
				$buffer = 2*$arr[$i];
				if ($buffer>9) {
					$buffer -= 9;
				}
				$sum += $buffer;
			}
			else {
				$sum += $arr[$i];
			}

			$toggle = !$toggle;
		}

		return (10-($sum%10))%10;	
	}

	protected function generateRandomIMEI()
	{
		# l'IMEI à retourner
		# commençant par le RBI
		$imei = ImeiTrait::$RBI[mt_rand(0, count(ImeiTrait::$RBI)-1)];
		# puis génération aléatoire jusqu'à 14 chiffres au total
		while (strlen($imei)<14) {
			$imei .= mt_rand(0, 9);
		}
		# le chiffre de contrôle
		$imei .= $this->computeLuhnChecksum($imei);

		return $imei;
	}
}