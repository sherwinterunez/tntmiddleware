<?php
require_once('datasd.php');
$file_id='sdfile';
$status="";
//$sdpath="../sectorfile/";
//$sdpath=sdpath();
$sdpath="c:/wamp/www/tnt/sectorfile/";

//$user=$_POST['user'];

$user='middleware';

if(!empty($_SERVER['argv'])&&!empty($_SERVER['argc'])&&$_SERVER['argc']==4) {
} else {
	//pre($_SERVER);
	die("INVALID PARAMETERS");
}

$_FILES = array($file_id=>
	array(
		'name'=>$_SERVER['argv'][2],
		'type'=>'application/octet-stream',
		'tmp_name'=>$_SERVER['argv'][1] . '/' . $_SERVER['argv'][2],
		'error'=>'0',
		'size'=>1048575
	)
);

$sdpath=$_SERVER['argv'][3] . '/';

//pre(array('$_FILES'=>$_FILES)); die;

$filename=$_FILES[$file_id]['name'];
$tmpfile=$_FILES[$file_id]['tmp_name'];

if(!$_FILES[$file_id]['name'])
{
    //echo returnStatus('<font color="red">no file specified</font>');
    die("NO FILE SPECIFIED");
}
else
{
	$status.="Upload: " . trim($_FILES[$file_id]["name"]) . "<br />";
	$status.="Type: " . trim($_FILES[$file_id]["type"]) . "<br />";
    $status.="Size: " . trim(($_FILES[$file_id]["size"] / 1024)) . " Kb<br />";
		
	/*check if file exists*/
	if (file_exists($sdpath . $filename))
	{
		$status.= $filename. " already exists. Please rename file.";
		$val='';
		$val['filename']=$_FILES[$file_id]["name"];
		$val['uploader']=$user;
		$val['uptype']='SD';
		$tmp = @split('-',$val['filename']);
		$val['filedate']=$tmp[1];
		$val['filetime']=substr($tmp[2],0,6);
		$val['filex']='Y';
		$val['remark']= "Duplicate Sector file upload ";
		$idFile=InsDataFile($val);
		unlink($sdpath.$_FILES[$file_id]["name"]);
		//echo returnStatus($status,$filename);
		die("FILE ALREADY EXISTS");
	} else {	 
		/*copy file over to tmp directory */
		//if (move_uploaded_file($_FILES[$file_id]['tmp_name'], $sdpath.$_FILES[$file_id]["name"])){ 

		if (copy($_FILES[$file_id]['tmp_name'], $sdpath.$_FILES[$file_id]["name"])){ 
			$fh=fopen($sdpath.$_FILES[$file_id]["name"],'r');
			$parse=fgets($fh);
			//$status.="<br /><br />".$parse;
			if(substr($parse,0,2)<>'01'){
				//echo "Invalid file upload ".$_FILES[$file_id]['name'];
				$val='';
				$val['filename']=$_FILES[$file_id]["name"];
				$val['uploader']=$user;
				$val['uptype']='SD';
				$tmp = @split('-',$val['filename']);
				$val['filedate']=$tmp[1];
				$val['filetime']=substr($tmp[2],0,6);
				$val['filex']='E';
				$val['remark']= "Invalid Sector file upload ";
				$idFile=InsDataFile($val);
				fclose($fh);
				unlink($sdpath.$_FILES[$file_id]["name"]);
				die("Invalid file upload ".$_FILES[$file_id]['name']);
			} else {
				fclose($fh);
				$status.= "File was successfully uploaded.<br/>";
				//$status.= "Stored in: /sectorfile/". $_FILES[$file_id]["name"]."<br/>";	
				$result=audit_Trail('Sector File Upload','Uploading','Uploading File '.$_FILES[$file_id]["name"],$user);
				
				/* parsing */
				$val='';
					$val['filename']=$_FILES["sdfile"]["name"];
					$val['uploader']=$user;
					$val['uptype']='SD';
					$tmp = @split('-',$val['filename']);
					$val['filedate']=$tmp[1];
					$val['filetime']=substr($tmp[2],0,6);
					$val['filex']='N';
					$val['remark']="Success";
					$idFile=InsDataFile($val);
					if(is_numeric($idFile)){
						$line=1;
						$fh=fopen($sdpath.$_FILES['sdfile']['name'],'r');
							$flight="";
							$con="";
							$cargo="";
							$bag="";
							$csv="";
							$match="";
							$d=0;
							$parsereturn="";
							$match['desc']= '';
							$match['user']= ''; 
							$match['flightno']='';
							$match['flightype']='';
							$match['mawb']='';
							$match['bag']='';
							$match['hbl']='';
							$match['sender']='';
							$match['receiver']='';
							$match['modtype']='';
							$match['itemval']='0';
							$match['itemcry']='';
							$match['itempcs']='0';
							$match['itemwt']='0';
							$match['origin']='';
							$match['des']='';
							$match['port'] = '';
							$match['idsec']='1';
							$match['departport']='';
							while(!feof($fh)){
								$parse=fgets($fh);
								if($parse<>""){
									$d=$d+1;
									$rec="";
									$val['lineno']=$line;
									if(substr($parse,0,2)=="01"){
										$val['conref']=substr($parse,21,12);
									}
									$val['content']=str_replace("'","''",$parse);
									$result=InsSDRaw($val);
									
									switch(substr($parse,0,2))
									{
										case "01": 
											$flight[0][0] = "airlinename";
											$flight[0][1] = substr($parse,2,2);
											$flight[1][0] = "sectorno";
											$flight[1][1] = substr($parse,4,3);
											//$match['flightno']=trim(substr($parse,2,2)).trim(substr($parse,4,3));
											$match['flightno']=trim(substr($parse,2,6));
											$flight[2][0] = "CtryORG";
											$flight[2][1] = substr($parse,8,3);
											$match['departport']=substr($parse,8,3);
											$flight[3][0] = "CtryDST";
											$flight[3][1] = substr($parse,11,3);
											$flight[4][0] = "FlightDate";
											$flight[4][1] = substr($parse,14,6);
											$match['flightdate']=substr($parse,14,6);
											$flight[5][0] = "TravelType";
											$flight[5][1] = substr($parse,20,1);
											$match['flightype']=substr($parse,20,1);
											$flight[6][0] = "MAWB";
											$flight[6][1] = substr($parse,21,12);
											$match['mawb']= substr($parse,21,12);
											$flight[7][0] = "MotherBag";
											$flight[7][1] = substr($parse,33,10);
											$match['bag']=substr($parse,33,10);
											$flight[8][0] = "ORG";
											$flight[8][1] = substr($parse,43,3);
											$flight[9][0] = "DEST";
											$flight[9][1] = substr($parse,46,3);
											$flight[10][0] = "MBAGunit";
											$flight[10][1] = substr($parse,49,2);
											$flight[11][0] = "MBAGGWg";
											$flight[11][1] = substr($parse,51,10);
											$flight[12][0] = "idfile";
											$flight[12][1] = $idFile;
											$flyt = InsParsedData("sectorflight",$flight,$val['uploader'],'idsectorflight');
											$csv[$d]='01|'.Delimetermaker($flight);
											break;
										case "02":    
											$bag[0][0] = substr($parse,0,2);
											$bag[0][1] = substr($parse,2,8); 
											$csv[$d]='02|'.Delimetermaker($bag);
											break;
										case "03": 
											$con[0][0] = "idSectorFlight";
											$con[0][1] = $flyt;
											$con[1][0] = "ConNumber";
											$con[1][1] = substr($parse,2,9); 
											$match['hbl']= substr($parse,2,9);
											$con[2][0] = "SHBL_blank";
											$con[2][1] = substr($parse,11,1);
											$con[3][0] = "ConOrg";
											$con[3][1] = substr($parse,12,3);
											$con[4][0] = "ConDest";
											$con[4][1] = substr($parse,15,3);
											$con[5][0] = "ACCID";
											$con[5][1] = substr($parse,18,6);
											$con[6][0] = "Sender";
											$con[6][1] = substr($parse,24,32);
											$match['sender']= substr($parse,24,32);
											$con[7][0] = "SenderAdd1";
											$con[7][1] = substr($parse,55,31);
											$con[8][0] = "SenderAdd2";
											$con[8][1] = substr($parse,87,31);
											$con[9][0] = "SenderCity";
											$con[9][1] = substr($parse,117,31);
											$con[10][0] = "SenderProvince";
											$con[10][1] = substr($parse,148,31);
											$con[11][0] = "SenderCtryCode";
											$con[11][1] = substr($parse,179,3);
											$con[12][0] = "SenderPostal";
											$con[12][1] = substr($parse,182,9);
											$con[13][0] = "SenderTel";
											$con[13][1] = substr($parse,191,12);
											$con[14][0] = "ReceiverCoy";
											$con[14][1] = substr($parse,203,31);
											$match['receiver']= substr($parse,203,31);
											$con[15][0] = "ReceiverAdd1";
											$con[15][1] = substr($parse,234,31);
											$con[16][0] = "ReceiverAdd2";
											$con[16][1] = substr($parse,265,31);
											$con[17][0] = "ReceiverCity";
											$con[17][1] = substr($parse,296,31);
											$con[18][0] = "ReceiverProvince";
											$con[18][1] = substr($parse,327,31);
											$con[19][0] = "ReceiverCtryCode";
											$con[19][1] = substr($parse,358,3);
											$con[20][0] = "ReceiverPostal";
											$con[20][1] = substr($parse,361,9);
											$con[21][0] = "ReceiverTel";
											$con[21][1] = substr($parse,370,12);
											$con[22][0] = "DocumentIn";
											$con[22][1] = substr($parse,382,3);
											$match['modtype']=substr($parse,382,3);
											$con[23][0] = "ShipmentVal";
											$con[23][1] = substr($parse,385,13);
											$match['itemval']=trim(substr($parse,385,13));
											$con[24][0] = "CurrencyCode";
											$con[24][1] = substr($parse,398,4);
											$match['itemcry']=trim(substr($parse,398,4));
											//$con[24][1] = substr($parse,399,4);
											//$match['itemcry']=substr($parse,399,4);
											$con[25][0] = "TotalPcs";
											$con[25][1] = substr($parse,402,6);
											$match['itempcs']=substr($parse,402,6);
											$con[26][0] = "ShipmentWg";
											$con[26][1] = substr($parse,408,9);
											$match['itemwt']=substr($parse,408,9);
											$con[27][0] = "BlankFld";
											$con[27][1] = substr($parse,418,9);
											$con[28][0] = "RecTelex";
											$con[28][1] = substr($parse,466,12);
											$con[29][0] = "RecFax";
											$con[29][1] = substr($parse,478,11);
											$HBL = InsParsedData("sectorhbl",$con,$val['uploader'],'idshbl');
											$csv[$d]='03|'.Delimetermaker($con);
											$match['origin']=trim(substr($parse,179,3)).trim(substr($parse,12,3));
											$match['des']=trim(substr($parse,358,3)).trim(substr($parse,15,3));
											$match['port'] = trim(substr($parse,15,3));
											$match['idsec']=$HBL;
											break;
										case "04": 
											$cargo[0][0] = "idSHBL";
											$cargo[0][1] = $HBL;
											$cargo[1][0] = "idSectorFlight";
											$cargo[1][1] = $flyt;
											$cargo[2][0] = "ConNumber";
											$cargo[2][1] = substr($parse,2,10);
											$cargo[3][0] = "CNAID";
											$cargo[3][1] = substr($parse,12,2);
											$cargo[4][0] = "Tariff";
											$cargo[4][1] = substr($parse,14,15);
											$cargo[5][0] = "ItemDesc1";
											$cargo[5][1] = substr($parse,29,78);
											//$match['desc']= substr($parse,29,78);
											$match['desc']= trim(substr($parse,29,78));
											$cargo[6][0] = "ItemDesc2";
											$cargo[6][1] = substr($parse,107,78);
											$cargo[7][0] = "ItemDesc3";
											$cargo[7][1] = substr($parse,185,78);
											$cargo[8][0] = "OriginCtry";
											$cargo[8][1] = substr($parse,263,3);
											$cargo[9][0] = "DestCtry";
											$cargo[9][1] = substr($parse,266,3);
											$item = InsParsedData("sectoritem",$cargo,$val['uploader'],'idsitem');
											$csv[$d]="04|".Delimetermaker($cargo);
											//match(HBL,desc, aircraft, flightdate, mawb)
											$match['user']=$user;
											$matchz = matchit($match,$idFile);
										break;
									}
								}
								$line=$line+1;
							}
						fclose($fh);
						$status.= "<br/>Total Record Parse: ".$d."<br/>";
						//$ftp=ftppath();
						$ftp="../ftpsim/";
						$replace = array('.dat','.DAT');
						$filenew=str_replace($replace,"",$_FILES['sdfile']['name']).'_TNTPH_SD'.date("Ymd_Hisu").".csv";
						
						if (!file_exists($ftp.$filenew)) {
							if (!$fh=fopen($ftp.$filenew,'x+')) {
								 $status.= "Cannot open file ($filename)";
								 exit;
							}
						
							// Write $somecontent to our opened file.
							foreach ($csv as $rewrite){
								if (fwrite($fh, $rewrite."\n") === FALSE) {
									$status.= "Cannot write to file ($filename)";
									exit;
								}
							}
							
							fclose($fh);
							
							//$status.= "Process Complete. Click to view the processed";
							//$status.= "Process Complete. Go to Pre-Arrival";

							die("OK");
						} else {
							$status.= "The file $filenew is not writable";
						}
						
					}
			}
		}else{
			$status.='<font color="red">File was not sucessfully uploaded. No parsing occured.</font>';
			
		} 
	}
	//echo returnStatus($status,$filename);
}



function returnStatus($status,$filename){
	return "<html><body>
			<script type='text/javascript'>
				function init(){
					if(top.uploadComplete)
						top.uploadComplete('".$status."','".$filename."');						
				}
				window.onload=init;
			</script></body></html>";
}
//exit;
?>