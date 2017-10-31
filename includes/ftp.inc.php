<?php

/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
*
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

if(!class_exists('MyFTP')) {

  class MyFTP {
      public $conn;

      public function __construct($url){
          $this->conn = ftp_connect($url);
          if(!$this->conn) {
            die('cannot connect to ftp.');
          }
      }

      public function __call($func,$a){
          if(strstr($func,'ftp_') !== false && function_exists($func)){
              array_unshift($a,$this->conn);
              return call_user_func_array($func,$a);
          }else{
              // replace with your own error handler.
              die("$func is not a valid FTP function");
          }
      }

			public function listDetailed($children,$mode=0) {
					if(is_array($children)) {
							$items = array();

							if($mode==1) {
								foreach ($children as $child) {
										$chunks = preg_split("/\s+/", $child);
										list($item['rights'], $item['number'], $item['user'], $item['group'], $item['size'], $item['month'], $item['day'], $item['time']) = $chunks;
										$item['type'] = $chunks[0]{0} === 'd' ? 'directory' : 'file';
										array_splice($chunks, 0, 8);
										$file = implode(" ", $chunks);
										$item['file'] = $file;
										$items[] = $item;
								}
							} else {
								foreach ($children as $child) {
										$chunks = preg_split("/\s+/", $child);
										list($item['rights'], $item['number'], $item['user'], $item['group'], $item['size'], $item['month'], $item['day'], $item['time']) = $chunks;
										$item['type'] = $chunks[0]{0} === 'd' ? 'directory' : 'file';
										array_splice($chunks, 0, 8);
										$items[implode(" ", $chunks)] = $item;
								}
							}

							if(!empty($items)) {
								return $items;
							}
					}

					return false;
			}

			public function ftplist($str=false,$mode=0) {

				if(!empty($this->conn)) {
				} else {
					return false;
				}

				if(!empty($ip)) {
					$localIP = $ip;
				} else {
					$localIP = getMyLocalIP();
				}

				$p1 = rand(80,190);
				$p2 = rand(1,255);

				$port = ($p1*256)+$p2;

				$raw = 'PORT '.str_replace('.',',',$localIP).','.$p1.','.$p2;

				print_r(array('$raw'=>$raw));

				$ret = $this->ftp_raw($raw);

				print_r(array('$ret'=>$ret));

				if(preg_match('/200\s+Port\s+request\s+OK\./si',$ret[0])) {
				} else {
					return false;
				}

				$address = '0.0.0.0';

				if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
				    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
						return false;
				}

				if (socket_bind($sock, $address, $port) === false) {
				    echo "socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
						return false;
				}

				if (socket_listen($sock, 5) === false) {
				    echo "socket_listen() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
						return false;
				}

				$param = '';

				if(!empty($str)) {
					$param = ' '.$str;
				}

				$fret = $this->ftp_raw('LIST'.$param);

				print_r(array('$fret'=>$fret));

				if(preg_match('/550\s+LIST\s+cmd\s+failed/si',$fret[0])) {
					return $fret;
				}

				// 125 List started OK

				$retstr = '';

				if(preg_match('/125\s+List\s+started\s+OK/si',$fret[0])) {
				} else {
					return $fret;
				}

				do {
				    if (($msgsock = socket_accept($sock)) === false) {
				        echo "socket_accept() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
				        break;
				    }
				    /* Send instructions. */
				    //$msg = "\nWelcome to the PHP Test Server. \n" .
				    //    "To quit, type 'quit'. To shut down the server type 'shutdown'.\n";
				    //socket_write($msgsock, $msg, strlen($msg));

				    do {
				        if (false === ($buf = @socket_read($msgsock, 2048, PHP_NORMAL_READ))) {
				            //echo "socket_read() failed: reason: " . socket_strerror(socket_last_error($msgsock)) . "\n";
				            break 2;
				        }
				        if (!$buf = trim($buf)) {
				            continue;
				        }
				        if ($buf == 'quit') {
				            break;
				        }
				        if ($buf == 'shutdown') {
				            socket_close($msgsock);
				            break 2;
				        }
				        //$talkback = "PHP: You said '$buf'.\n";
				        //socket_write($msgsock, $talkback, strlen($talkback));
				        //echo "$buf\n";
								$retstr .= "$buf\n";
								//echo "$buf\n";
				    } while (true);
				    socket_close($msgsock);
				} while (true);

				socket_close($sock);

				if(!empty($retstr)) {
					$retstr = trim($retstr);
					$retval = explode("\n",$retstr);

					if($mode==0) {
						return $retstr;
					} else
					if($mode==1) {
						return $retval;
					} else
					if($mode==2) {
						return $this->listDetailed($retval);
					} else
					if($mode==3) {
						return $this->listDetailed($retval,1);
					} else
					if($mode==4) {
						return array('raw'=>$retstr,'array'=>$retval);
					}

				}

				return false;
			}

  }

}

/* INCLUDES_END */
