<?php

$dsn ="odbc:BRIDGE";

try {
  $dbh = new PDO($dsn);

  //$stmt = $dbh->prepare('SELECT TOP 10 * FROM (SELECT * FROM dbo.smsmessaging);');

  $stmt = $dbh->prepare('SELECT TOP 10 * FROM dbo.smsmessaging ORDER BY MessageID ASC;');

  $stmt->execute();

  $ret = array();

  do {

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	//echo json_encode(array('hello1'=>'sherwin'));

	foreach($result as $rst) {

		foreach($rst as $k=>$v) {
			$rst[$k] = utf8_encode($v);
		}

      $ret[] = $rst;
    }

	//echo json_encode(array('hello2'=>'sherwin'));

	} while ($stmt->nextRowset());

	//print_r(array('data'=>$ret));

  echo json_encode(array('data'=>$ret));
}
catch(PDOException $e) {
  $ret = array();
  $ret['error'] = $e->getMessage();

  echo json_encode($ret);
}


///
