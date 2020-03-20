<?php
$yil=date("Y");
$ay=date("m");
$gyil=2019;
$gay=12;
if ($ay<10) {$ay=str_replace("0", null, $ay);}

define('EMAIL','*****');
define('PASSWORD','****');
define('USER_AGENT', 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2309.372 Safari/537.36');
define('COOKIE_FILE', 'cookie.txt');
define('LOGIN_FORM_URL', 'https://trucksbook.eu/?go=/index');
define('LOGIN_ACTION_URL', 'https://trucksbook.eu/components/notlogged/login.php?go=/index');
$postValues = array(
	'email' => EMAIL,
	'pass' => PASSWORD
);
$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, LOGIN_ACTION_URL);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postValues));
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_COOKIEJAR, COOKIE_FILE);
curl_setopt($curl, CURLOPT_USERAGENT, USER_AGENT);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_REFERER, LOGIN_FORM_URL);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);

//Giriş istediğini çalıştıralım.
curl_exec($curl);

//Hata kontrolü
if(curl_errno($curl)){
	throw new Exception(curl_error($curl));
}

//Şirketteki oyuncuların listesi
$regex='/ <a href="https:\/\/trucksbook\.eu\/profile\/([0-9]+)">(.+?)<\/a> /';
$webData=file_get_contents('https://trucksbook.eu/components/app/company/employee_list.php?id=24994');
//data[1] değişkeninde oyuncu id lerini aldık
preg_match_all($regex, $webData, $data);

$player=[];

for ($i=0; $i <count($data[0]); $i++) { 
	if ($data[1][$i]=="91302") {
		$player[$i]=array('id'=>$data[1][$i],'name'=>$data[2][$i],$gyil=>array(),$yil=>array());
	}
}
//var_dump($player);
foreach ($player as $key=> $value) {
	for ($i=1; $i <=$gay ; $i++) { 
		$url='https://trucksbook.eu/logbook/'.$value["id"].'/'.$gyil.'/'.$i.'/1/';
		curl_setopt($curl, CURLOPT_URL, $url);
		//Aynı çerez dosyasını kullanalım.
		curl_setopt($curl, CURLOPT_COOKIEJAR, COOKIE_FILE);
		curl_setopt($curl, CURLOPT_USERAGENT, USER_AGENT);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		$str=curl_exec($curl); 
		$str = str_replace([" ", "."], null, $str);
		$regex='/(.+)\nkm<\/span>/';
		preg_match_all($regex, $str, $km);
		if (!isset($km[1][0])) {
			$km[1][0]=0;
		}
		$player[$key][$gyil]+=array($i=>$km[1][0]);
	}
	for ($i=1; $i <=$ay ; $i++) { 
		$url='https://trucksbook.eu/logbook/'.$value["id"].'/'.$yil.'/'.$i.'/1/';
		curl_setopt($curl, CURLOPT_URL, $url);
		//Aynı çerez dosyasını kullanalım.
		curl_setopt($curl, CURLOPT_COOKIEJAR, COOKIE_FILE);
		curl_setopt($curl, CURLOPT_USERAGENT, USER_AGENT);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		$str=curl_exec($curl); 
		$str = str_replace([" ", "."], null, $str);
		$regex='/(.+)\nkm<\/span>/';
		preg_match_all($regex, $str, $km);
		if (!isset($km[1][0])) {
			$km[1][0]=0;
		}
		$player[$key][$yil]+=array($i=>$km[1][0]);
	}
}
//print_r($player);
//file_put_contents('db.txt', print_r($player, true));
?>
<!DOCTYPE html>
<html>
<head>
	<title>logistik</title>
	<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
</head>
<body>
	<canvas id="myChart" width="250" height="100"></canvas>
	
</body>
</html>