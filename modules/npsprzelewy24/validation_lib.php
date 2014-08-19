<?php
function p24_weryfikujNoSSL($p24_id_sprzedawcy, $p24_session_id, $p24_order_id, $p24_kwota = "", $url) {
	$P = array();
	$RET = array();
	$header = "POST /transakcjanossl.php HTTP/1.1\r\n";
	$header .= "Host: ".$url."\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$fp = fsockopen($url, 80, $errno, $errstr, 30);
	$P[] = urlencode("p24_id_sprzedawcy")."=".urlencode($p24_id_sprzedawcy);
	$P[] = urlencode("p24_session_id")."=".urlencode($p24_session_id);
	$P[] = urlencode("p24_order_id")."=".urlencode($p24_order_id);
	$P[] = urlencode("p24_kwota")."=".urlencode($p24_kwota);
	$post = join("&", $P);
	$req = "Content-Length: ".strlen($post)."\r\n\r\n";
	$req .= $post;
	if (!$fp) {
		die("CONNECTION ERROR");
	} else {
		fputs($fp, $header.$req);
		$res = false;
		while (!feof($fp)) {
			$line = ereg_replace("[\n\r]", "", fgets($fp, 1024));
			if ($line != "RESULT" and !$res)
				continue;
			if ($res)
				$RET[] = $line;
			else
				$res = true;
		}
	}
	fclose($fp);

	return $RET;
}

function p24_weryfikujSSL($p24_id_sprzedawcy, $p24_session_id, $p24_order_id, $p24_kwota = "", $url) {
	$P = array();
	$RET = array();
	$url = "https://".$url."/transakcja.php";
	$P[] = "p24_id_sprzedawcy=".$p24_id_sprzedawcy;
	$P[] = "p24_session_id=".$p24_session_id;
	$P[] = "p24_order_id=".$p24_order_id;
	$P[] = "p24_kwota=".$p24_kwota;
	$user_agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POST, 1);
	if (count($P))
		curl_setopt($ch, CURLOPT_POSTFIELDS, join("&", $P));
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	$result = curl_exec($ch);
	curl_close($ch);
	$T = explode(chr(13).chr(10), $result);
	$res = false;
	foreach ($T as $line) {
		$line = ereg_replace("[\n\r]", "", $line);
		if ($line != "RESULT" and !$res)
			continue;
		if ($res)
			$RET[] = $line;
		else
			$res = true;
	}
	return $RET;
}

?>