<?php

$servername = "localhost";
$database = "kbabtel";
$username = "kbabtel";
$password = "21wyisey";
$sql = "select Body from `kob-Systeme-MailQueue` where Body like '%Code utilisateur%' order by Id";
$sqla = "update `kob-Cadref-Adherent` set Password=:pass where Numero=:num";
$sqle = "update `kob-Cadref-Enseignant` set Password=:pass where Code=:ens";

try {
	$pdo = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
	// set the PDO error mode to exception
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

	$upda = $pdo->prepare($sqla);
	$upde = $pdo->prepare($sqle);

	$qry = $pdo->prepare($sql);
	$qry->execute();
	$data = $qry->fetchAll(PDO::FETCH_ASSOC);
	$qry->closeCursor();
	foreach($data as $r) {
		$b = $r['Body'];
		$a = explode("<strong>", $b);
		$n = explode("</strong>", $a[1]);
		$num = $n[0];
		$n = explode("</strong>", $a[2]);
		$pass = $n[0];
		echo "$num $pass\n";
		if(substr($num,0,3) == 'ens') {
				$par = array(':ens'=>substr($num,3), ':pass'=>$pass);
				$upde->execute($par);
				$upde->closeCursor();			
		}
		else {
				$par = array(':num'=>$num, ':pass'=>$pass);
				$upda->execute($par);
				$upda->closeCursor();			
		}
	}
}
catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

