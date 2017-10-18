<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Database Class
*
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

/* INCLUDES_START */

if(!class_exists('APP_Db')) {

	class APP_Db {

		var $dbhost = false;
		var $dbname = false;
		var $dbuser = false;
		var $dbpass = false;
		var $dbport = false;

		var $ready  = false;

		var $pglink = false;

		var $lasterror = false;

		var $lastquery = false;

		var $time_start = false;
		var $total_time = false;

		var $queries = false;

		var $err = false;

		var $show_error = false;

		var $count = 0;

		var $begin = false;

		function __construct($dbhost=false,$dbname=false,$dbuser=false,$dbpass=false) {
			global $apperror;

			if(empty($dbhost)||empty($dbname)||empty($dbuser)||empty($dbpass)) {
				$this->error();
			}

			if(preg_match('#(.+?)\:(\d+)#si',$dbhost,$m)) {
				$this->dbhost = $m[1];
				$this->dbport = $m[2];
			} else {
				$this->dbhost = $dbhost;
				$this->dbport = 5432;
			}

			$this->dbname = $dbname;
			$this->dbuser = $dbuser;
			$this->dbpass = $dbpass;

			$this->ready = true;

			$this->queries = array();

			if(isset($apperror)&&is_object($apperror)) {
				$this->err = &$apperror;
			} else {
				die('error class is missing');
			}

			$this->init();
		}

		function __destruct() {
		}

		function pre($data) {
			echo "<pre>";
			print_r($data);
			echo "</pre>";
		}

		function error($err='error database connection.') {
			die($err);
		}

		function ready() {
			return is_resource($this->pglink);
		}

		function init() {
			global $pglink;

			if(!$this->ready) $this->error();

			//echo 'db class loaded';

			$connect = "host=".$this->dbhost." port=".$this->dbport." dbname=".$this->dbname." user=".$this->dbuser." password=".$this->dbpass;

			//echo $connect;

			$pglink = pg_connect($connect);

			if(!is_resource($pglink)) {
				$this->error("\n\ncan't connect to postgres server\n\n");
			}

			$this->pglink = &$pglink;

			return $this->pglink;

		} // init

		function close() {
			@pg_close($this->pglink);
			$this->pglink = false;
		}

		function begin() {
			if(!is_resource($this->pglink)) $this->error("\n\ninvalid postgres connection\n\n");
			pg_query($this->pglink, "begin;");
			$this->begin = true;
		}

		function rollback() {
			if(!is_resource($this->pglink)) $this->error("\n\ninvalid postgres connection\n\n");
			if($this->begin) pg_query($this->pglink, "rollback;");
		}

		function commit() {
			if(!is_resource($this->pglink)) $this->error("\n\ninvalid postgres connection\n\n");
			if($this->begin) pg_query($this->pglink, "commit;");
		}

		function isTableExist($table=false) {

			if(!empty($table)) {
			} else {
				return false;
			}

			//$sql = "SELECT table_schema,table_catalog,table_name,column_name FROM information_schema.columns where table_schema='public' and table_catalog='".$this->dbname."' and table_name='$table'";

			$sql = "SELECT DISTINCT table_name FROM information_schema.columns where table_schema='public' and table_catalog='".$this->dbname."' and table_name='$table'";

			$ret = $this->query($sql);

			if(!empty($ret['rows'][0]['table_name'])) {
				return true;
			}

			return false;
		}

		function isColumnExist($table=false,$column=false) {

			if(!empty($table)&&!empty($column)) {
			} else {
				return false;
			}

			//$sql = "SELECT table_schema,table_catalog,table_name,column_name FROM information_schema.columns where table_schema='public' and table_catalog='".$this->dbname."' and table_name='$table'";

			$sql = "SELECT DISTINCT column_name FROM information_schema.columns where table_schema='public' and table_catalog='".$this->dbname."' and table_name='$table' and column_name='$column'";

			$ret = $this->query($sql);

			if(!empty($ret['rows'][0]['column_name'])) {
				return true;
			}

			return false;
		}

		function query($sql=false,$show_error=false,$returning=false) {
			if(!is_resource($this->pglink)) $this->error("\n\ninvalid postgres connection\n\n");

			$this->timer_start();

			if(preg_match("/^(insert|update|delete)/si",trim($sql))) {
				$write = true;
				//pg_query($this->pglink, "begin;");
			} else {
				$write = false;
			}

			$this->count++;

			//if(!($result=@pg_query($this->pglink, $sql))) {

			$this->lastquery = $sql;

			if(!($result=@pg_query($this->pglink, $sql))) {

				$this->lasterror = pg_last_error($this->pglink);

				//pre(array('$this->lasterror'=>$this->lasterror));

				if($write) {
					//pg_query($this->pglink, "rollback;");
				}

				$this->queries[] = array('query'=>$sql,'total_time'=>$this->timer_stop(),'err'=>true, 'errmsg'=>$this->lasterror);

				if($this->show_error||$show_error) {
					$this->err->show_error("\n\nSQL ERROR: {$this->lasterror}\n\n",true);
				}
			} else {
				$this->lasterror = false;

				$this->queries[] = array('query'=>$sql,'total_time'=>$this->timer_stop());

				if($write) {
					//pg_query($this->pglink, "commit;");
					$ret = array('sql'=>$sql,'resource'=>&$result,'affected'=>pg_affected_rows($result),'pg_affected_rows'=>true);

					if(is_string($returning)) {
						$ret['returning'] = pg_fetch_all($result);
					}

					return $ret;
				} else {
					$nrows = pg_num_rows($result);
					$ret = array('sql'=>$sql,'resource'=>&$result,'affected'=>$nrows,'pg_num_rows'=>true);

					if($nrows>0) {
						//$ret['rows'] = pg_fetch_all($result);

						$trows = pg_fetch_all($result);
						$krows = array();

						foreach($trows as $k=>$v) {
							//$krows[$k] = array_map('utf8_encode',$v);
							$krows[$k] = array_map('check_utf8',$v);
						}

						$ret['rows'] = $krows;
						//$ret['krows'] = $krows;
					}

					return $ret;
				}
			}

			return false;

		} // query

		function insert($tbl_name=false,$data=false,$ret=false) {

			if(empty($tbl_name)||empty($data)||!is_array($data)) return false;

			$sql = "insert into $tbl_name ";

			$values = array();
			$tables = array();

			foreach($data as $k=>$v) {
				if(empty($v)&&!is_numeric($v)) continue;

				$tables[] = $k;

				if(is_numeric($v)) {
					if($v===0) {
						$values[] = "'0'";
					} else {
						$values[] = "'".trim($v)."'";
					}
				} else {
					$values[] = "'".pg_escape_string(trim(check_utf8($v)))."'";
				}
			}

			$sql .= '('.implode(',',$tables).') values ';
			$sql .= '('.implode(',',$values).')';

			if(is_string($ret)) {
				$sql .= ' returning '.$ret;
			}

			return $this->query($sql,false,$ret);

		} // insert

		function update($tbl_name=false,$data=false,$where=false,$debug=false) {

			if(empty($tbl_name)||empty($data)||!is_array($data)) return false;

			$sql = "update $tbl_name set ";

			$fields = array();

			foreach($data as $k=>$v) {
				if(is_numeric($v)) {
					$fields[] = $k."='".trim($v)."'";
				} else
				if(preg_match("/^\#(.+)\#$/", $v, $m)) {
					//pre(array('$m'=>$m));
					$fields[] = $k."=".pg_escape_string(trim(check_utf8($m[1])));
					//$fields[] = $k."=".pg_escape_string(trim($m[1]));
				} else {
					$fields[] = $k."='".pg_escape_string(trim(check_utf8($v)))."'";
					//$fields[] = $k."='".pg_escape_string(trim($v))."'";
				}
			}

			$sql .= implode(',',$fields);

			if($where) {
				$sql .= ' where '.$where;
			}

			if($debug) {
				pre($sql);
			}

			return $this->query($sql);

		} // update

		function timer_start() {
			$mtime            = explode( ' ', microtime() );
			$this->time_start = $mtime[1] + $mtime[0];
			return true;
		}

		function timer_stop() {
			$mtime      = explode( ' ', microtime() );
			$time_end   = $mtime[1] + $mtime[0];
			$this->total_time = $time_end - $this->time_start;
			return $this->total_time;
		}

		function total_time() {
			return $this->total_time;
		}

	} # class

	$appdb = new APP_Db(DB_HOST, DB_NAME, DB_USER, DB_PASS);
}

/* INCLUDES_END */


# eof includes/db/index.php
