<?php
/*
* 
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Session with postgresql class include file
*
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

if(!class_exists('APP_Session')) {

	class APP_Session {
	
		var $pglink = false;
		var $lasterror = false;
		var $queries = false;
		var $sesname = 'APPSESS';
		var $lifetime = 3600; // 3600secs = 60mins = 1hr
	
		function __construct() {
			$this->init();
			
			$this->queries = array();
			
			if(defined('APP_CODE')) {
				$this->sesname = APP_CODE;
			}
		}
		
		function __destruct() {
		}
		
		function init() {			
			session_set_save_handler(array($this,'_open'), array($this,'_close'), array($this,'_read'), array($this,'_write'), array($this,'_destroy'), array($this,'_clean'));
		}

		function query($sql=false) {
			if(!is_resource($this->pglink)) {
				//pre($sql);
				//die($sql);
				$this->error("\n\ninvalid postgres connection\n\n");
			}

			$this->timer_start();
			
			if(preg_match("/^(insert|update|delete)/si",trim($sql))) {
				$write = true;
				pg_query($this->pglink, "begin;");
				//pre('query: '.$sql);
				//pre('write: '.$write);
			} else {
				$write = false;
			}
			
			if(!($result=@pg_query($this->pglink, $sql))) {

				$this->lasterror = pg_last_error($this->pglink);

				$this->queries[] = array('query'=>$sql,'total_time'=>$this->timer_stop(),'err'=>true, 'errmsg'=>$this->lasterror);
			
				if($write) {
					pg_query($this->pglink, "rollback;");
				}
				
			} else {
				$this->lasterror = false;

				$this->queries[] = array('query'=>$sql,'total_time'=>$this->timer_stop());
				
				if($write) {
					pg_query($this->pglink, "commit;");
					//echo 'Hello!';
					return array('resource'=>&$result,'affected'=>pg_affected_rows($result),'pg_affected_rows'=>true);
				} else {
					$nrows = pg_num_rows($result);
					$ret = array('resource'=>&$result,'affected'=>$nrows,'pg_num_rows'=>true);
					
					if($nrows>0) {
						$ret['rows'] = pg_fetch_all($result);
					}
					
					return $ret;
				}
			}
			
			return false;
		}

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
	
		function _open() {}
		
		function _close() {}
		
		function _clean($age) {}
		
		function _read($id) {
			//echo "\n\n<!--\n\n session read: $id \n\n-->\n\n";
			
			if($ret = $this->query("select * from tbl_session where hash='$id'")) {
				if($ret['affected']>0) {
					//pre($ret);
					return base64_decode($ret['rows'][0]['sessiondata']);
				}
			}
			
			return '';
		}

		function _write($id,$data) {
		
			$retval = false;
			
			$data = base64_encode($data);
			
			if($ret = $this->query("select id,hash from tbl_session where hash='$id'")) {
				if($ret['affected']>0) {
					$sql = sprintf("update tbl_session set sessiondata='%s', datestamp='now()' where hash='%s'",pg_escape_string($data), $id);
					if($ret = $this->query($sql)) {
						$retval = $ret['resource'];
					}
				} else {
					$sql = sprintf("insert into tbl_session (hash,sessiondata,ip,datestamp) values ('%s','%s','%s','now()')", pg_escape_string($id), pg_escape_string($data), pg_escape_string($_SERVER['REMOTE_ADDR']));
					if($ret = $this->query($sql)) {
						$retval = $ret['resource'];
					}
				}
			}
			
			//pre($this->queries);
			
			return $retval;
		}
		
		function _destroy($id) {
			$this->query("delete from tbl_session where hash='$id'");
		}
		
		function _new() {
			$sql = "select nextval('tbl_session_seq')";
				
			if($ret = $this->query($sql)) {
				$id = $ret['rows'][0]['nextval'];
				session_id(sha1($id.microtime(true)));
			} else {
				//debug_print_backtrace();
				die('an error has occured while generating session id');
			}
			
			return $this->id();
		}
		
		function start() {
			global $appdb, $pglink;
			
			if(!is_object($appdb)||!$appdb->ready()) {
				die('database connection failure');
			}
			
			$this->pglink = &$pglink;
			
			if(empty($_COOKIE)||empty($_COOKIE[$this->sesname])) {
				$this->_new();
			} else {
				if($ret = $this->query("select id,hash,extract(epoch from datestamp) as unixdate from tbl_session where hash='{$_COOKIE[$this->sesname]}'")) {
					if($ret['affected']>0) {
						$tt = time()-intval($ret['rows'][0]['unixdate']);
						if($tt>$this->lifetime) {
							$this->_new();
						}
					} else {
						$this->_new();
					}
				}
			}
			
			session_name($this->sesname);

			session_start();
			
			$_SESSION['id'] = $this->id();
		}
		
		function destroy() {
			session_destroy();
		}
		
		function id() {
			return session_id();
		}
			
	} // class
	
	$appsession = new APP_Session;
	
}