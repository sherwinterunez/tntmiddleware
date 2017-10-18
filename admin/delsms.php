<?php

$dsn ="odbc:BRIDGE";

//print_r(array('$_SERVER'=>$_SERVER,'$_POST'=>$_POST));

if(!empty($_POST['delete'])) {
  $delete = true;
}

if(!empty($_POST['mid'])) {
  $mid = explode(',',$_POST['mid']);

  $lid = array();

  foreach($mid as $k=>$v) {
    if(is_numeric($v)) {
      $lid[] = intval($v);
    }
  }
} else {
  die;
}

if(!empty($lid)) {
} else {
  die;
}

$sid = implode(',',$lid);

$sql = 'DELETE FROM dbo.smsmessaging WHERE MessageID IN ('.$sid.');';

//print_r(array('$lid'=>$lid,'$sid'=>$sid,'$sql'=>$sql));

if(!empty($delete)) {
  try {
    $dbh = new PDO($dsn);

    //$stmt = $dbh->prepare('DELETE FROM dbo.smsmessaging WHERE MessageID IN ('..');');

    $stmt = $dbh->prepare($sql);

    $stmt->execute();

    echo json_encode(array('SUCCESS'=>true));
  }
  catch(PDOException $e) {
    $ret = array();
    $ret['error'] = $e->getMessage();

    echo json_encode($ret);
  }
} else {
  echo json_encode(array('DELETE'=>'DISABLED'));
}

///
