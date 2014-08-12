<?php

session_start();

$routerIP = '192.168.0.1';
$resetD = 24; // Bandwidth Cycle Start on # of Every Month

function connect() {
	$mysqli = new mysqli('127.0.0.1','IPMON','uNuKV3F7HHfTEu4T','IPMON');
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
	return $mysqli;
};

if (isset($_GET['insert']) && $_SERVER['REMOTE_ADDR'] == $routerIP) {

	$mysqli = connect();

	$data = json_decode($_GET['insert']);
	
	$curDay = date('Y-m-d');
	$curMonth = date('ym', strtotime("- " . ($resetD - 1) . " days"));
	
	for ($i = 0; $i < count($data); $i++) {
	
		$line = $data[$i];
		$mac = $line[0];	$name = $line[1];	$ip = $line[2];		$down = $line[3];		$up = $line[4];
		
		// insert update macid
		$stmt = $mysqli->prepare("
			INSERT INTO mac (mac, name, ip) 
			VALUES (?, ?, ?) 
			ON DUPLICATE KEY UPDATE name=?,ip=?
		");
		$stmt->bind_param('sssss', $mac, $name, $ip, $name, $ip);
		$stmt->execute();
		$stmt->close();
		
		// change mac to macid
		$stmt = $mysqli->prepare("SELECT id FROM mac WHERE mac=?");
		$stmt->bind_param('s', $mac);
		$stmt->execute();
		$stmt->bind_result($id);
		$stmt->fetch();
		$stmt->close();
		
		$data[$i][0] = $id;
		
	};
	
	for ($i = 0; $i < count($data); $i++) {
	
		$line = $data[$i];
		$macid = $line[0];	$name = $line[1];	$ip = $line[2];		$down = $line[3];		$up = $line[4];
		
		// mac: day
		$stmt = $mysqli->prepare("
			INSERT INTO daily (macid, date, up, down)
			VALUES (?, ?, ?, ?)
			ON DUPLICATE KEY UPDATE up=up+?,down=down+?
		");
		$stmt->bind_param('ssiiii', $macid, $curDay, $up, $down, $up, $down);
		$stmt->execute();
		
		// mac: month
		$stmt = $mysqli->prepare("
			INSERT INTO monthly (macid, date, up, down)
			VALUES (?, ?, ?, ?)
			ON DUPLICATE KEY UPDATE up=up+?,down=down+?
		");
		$stmt->bind_param('ssiiii', $macid, $curMonth, $up, $down, $up, $down);
		$stmt->execute();
		
		// mac: total
		$stmt = $mysqli->prepare("
			INSERT INTO monthly (macid, date, up, down)
			VALUES (?, '0000', ?, ?)
			ON DUPLICATE KEY UPDATE up=up+?,down=down+?
		");
		$stmt->bind_param('siiii', $macid, $up, $down, $up, $down);
		$stmt->execute();
		
		// everyone: day
		$stmt = $mysqli->prepare("
			INSERT INTO daily (macid, date, up, down)
			VALUES (0, ?, ?, ?)
			ON DUPLICATE KEY UPDATE up=up+?,down=down+?
		");
		$stmt->bind_param('siiii', $curDay, $up, $down, $up, $down);
		$stmt->execute();
		
		// everyone: month
		$stmt = $mysqli->prepare("
			INSERT INTO monthly (macid, date, up, down)
			VALUES (0, ?, ?, ?)
			ON DUPLICATE KEY UPDATE up=up+?,down=down+?
		");
		$stmt->bind_param('siiii', $curMonth, $up, $down, $up, $down);
		$stmt->execute();
		
		// everyone: total
		$stmt = $mysqli->prepare("
			INSERT INTO monthly (macid, date, up, down)
			VALUES (0, '0000', ?, ?)
			ON DUPLICATE KEY UPDATE up=up+?,down=down+?
		");
		$stmt->bind_param('iiii', $up, $down, $up, $down);
		$stmt->execute();
		
		// $mysqli->close();

	};
	
}
elseif  (isset($_GET['select'])){

	$mysqli = connect();
	$select = $_GET['select'];
	$i = $_GET['i'];
	$f = $_GET['f'];

	if ($select == 'daily' || $select == 'monthly') {
	
		$mac = array();
		$result = $mysqli->query("SELECT mac, ip, name FROM mac ORDER BY id");
		while ($row = $result->fetch_array(MYSQLI_NUM)) {$mac[] = $row;}
		$mac = json_encode($mac);
	
		$stmt = $mysqli->prepare("
			SELECT  `date` ,  `macid` ,  `up` ,  `down` 
			FROM  `$select` 
			WHERE  `date` BETWEEN  ? AND  ?
			ORDER BY  `date` ,  `macid` ASC
			LIMIT 0 , 1000
		");
		$stmt->bind_param('ss', $i, $f);
		$stmt->execute();
		$stmt->bind_result($date, $macid, $up, $down);
		while ($stmt->fetch()) {
			$data[$date][] = array($macid, $up, $down);
		};
		
		echo json_encode(		array( "mac" => $mac, "data" => $data)		);
		
	}
	elseif ($select == 'total') {
	
		$stmt = $mysqli->prepare("
			SELECT  `mac` ,  `ip` ,  `name` ,  `up` ,  `down` 
			FROM  `monthly` 
			LEFT JOIN  `mac` ON  `macid` =  `id` 
			WHERE  `date` =  '0000'
			ORDER BY  `down` DESC 
			LIMIT 0 , 1000
		");
		$stmt->execute();
		$stmt->bind_result($mac, $ip, $name, $up, $down);
		while ($stmt->fetch()) {
			$data[] = array($mac, $ip, $name, $up, $down);
		};
		
		echo json_encode(		$data		);
	
	}
	
	$mysqli->close();

}
else {

	include("index.htm");

};
	
?>