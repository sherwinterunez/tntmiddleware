<?php
require_once('dataos.php');

//echo "this is osupload";

//pre($_SERVER);

//die;

$file_id='osfile';
$status='';
//$ospath="../osfile/";
$ospath="c:/wamp/www/tnt/osfile/";
//$user=$_POST['user'];

$user='middleware';

if(!empty($_SERVER['argv'])&&!empty($_SERVER['argc'])&&$_SERVER['argc']==4) {
} else {
	die("INVALID PARAMETERS");
}

/*
Array
(
    [0] => Array
        (
            [osfile] => Array
                (
                    [name] => MNL_D02DAT-20140606-000805
                    [type] => application/octet-stream
                    [tmp_name] => C:\wamp\tmp\phpEECE.tmp
                    [error] => 0
                    [size] => 22541
                )

        )

)
*/

$_FILES = array('osfile'=>
	array(
		'name'=>$_SERVER['argv'][2],
		'type'=>'application/octet-stream',
		'tmp_name'=>$_SERVER['argv'][1] . '/' . $_SERVER['argv'][2],
		'error'=>'0',
		'size'=>1048575
	)
);

$ospath = $_SERVER['argv'][3] . '/';

//die;

$filename=$_FILES[$file_id]['name'];
$tmpfile=$_FILES[$file_id]['tmp_name'];
$size=$_FILES[$file_id]['size'];

//pre(array('$_FILES'=>$_FILES));
//pre(array($_FILES[$file_id]['tmp_name'], $ospath.$_FILES[$file_id]["name"]));

//die;

if(!$_FILES[$file_id]['name'])
{
    //echo returnStatus("<font color=\'red\'>no file specified</font>");
    die("NO FILE SPECIFIED");
}
else
{
	if($size>1048576)
	{
		$status.= "error file size > 1 MB";
		//@unlink($_FILES[$file_id]['tmp_name']);

	}
	else
	{



		if (file_exists($ospath.$_FILES[$file_id]["name"])) {
			$status.= "Error: File already exist ".$_FILES[$file_id]['tmp_name'];
			$val='';
			$val['filename']=$_FILES[$file_id]["name"];
			$val['uploader']=$user;
			$val['uptype']='OS';
			$tmp = @split('-',$val['filename']);
			$val['filedate']=$tmp[1];
			$val['filetime']=substr($tmp[2],0,6);
			$val['filex']='Y';
			$val['remark']= "Duplicate OS file upload ";

			$idFile=InsDataFile($val);

			//@unlink($_FILES[$file_id]['tmp_name']);
			die("FILE ALREADY EXISTS");
		}
		else
		{



			//if (move_uploaded_file($_FILES[$file_id]['tmp_name'], $ospath.$_FILES[$file_id]["name"]))

			if (copy($_FILES[$file_id]['tmp_name'], $ospath.$_FILES[$file_id]["name"]))
			{
				$fh=fopen($ospath.$_FILES[$file_id]["name"],'r');
				$parse=fgets($fh);
				if(substr($parse,3,2)<>'WW'){
					//echo "Invalid file upload ".$_FILES[$file_id]['name'];
					$val='';
					$val['filename']=$_FILES[$file_id]["name"];
					$val['uploader']=$user;
					$val['uptype']='OS';
					$tmp = @split('-',$val['filename']);
					$val['filedate']=$tmp[1];
					$val['filetime']=substr($tmp[2],0,6);
					$val['filex']='E';
					$val['remark']= "Invalid OS file upload ";

					$idFile=InsDataFile($val);

					fclose($fh);
					//@unlink($ospath.$_FILES[$file_id]["name"]);
					die("Invalid file upload ".$_FILES[$file_id]['name']);
				}
				else
				{
						fclose($fh);

						$_SERVER['REMOTE_ADDR'] = "middleware";

						$status.="Success";
						$result=audit_Trail('OS File Upload','Uploading','Uploading File '.$_FILES[$file_id]["name"],$user,$_SERVER['REMOTE_ADDR']);
						$val='';
						$val['filename']=$_FILES[$file_id]["name"];
						$val['uploader']=$user;
						$val['uptype']='OS';
						$tmp=@split('-',$val['filename']);
						$val['filedate']=$tmp[1];
						$val['filetime']=substr($tmp[2],0,6);
						$val['filex']='N';
						$val['remark']="Success OS file upload ";

						$idFile=InsDataFile($val);

						//pre(array('$idFile'=>$idFile,'$ospath'=>$ospath.$_FILES[$file_id]['name']));

						if(is_numeric($idFile)){
							$line=1;
							$fh=fopen($ospath.$_FILES[$file_id]['name'],'r');

							//pre(array('$fh'=>$fh));

							//$fh=fopen($_FILES['osfile']['name'],'r');
								$os="";
								$sender="";
								$receiver="";
								$collect="";
								$delivery="";
								$quantum="";
								$carrier="";
								$agent="";
								$tnt="";
								$oscont="";
								$edita="";
								$edit="";
								$d=0;
								$parsereturn="";
								$fval=formal_val();
								$infval=informal_val();
								$fwt=formal_wt();
								$infwt=informal_wt();

								while(!feof($fh)){

									$parse=fgets($fh);

									if($parse<>""){
										$d=$d+1;
										$rec="";
										$val['lineno']=$line;
										$val['conref']=substr($parse,5,15);
										$val['content']=str_replace("'","''",$parse);

										$result=InsOSRaw($val);

												 /*osparse*/
													$os[0][0] ="recordtype";
													$os[0][1] = substr($parse,0,3);
													$os[1][0] = "CompanyID";
													$os[1][1] = substr($parse,3,2);
													$os[2][0] = "ConNumber";
													$os[2][1] = substr($parse,5,15);
													$os[3][0] = "ConSNum";
													$os[3][1] = substr($parse,20,3);
													$os[4][0] = "ArticleSNum";
													$os[4][1] = substr($parse,23,3);
													$os[5][0] = "ArticleConSN";
													$os[5][1] = substr($parse,24,5);
													$os[6][0] = "ArticleDesc";
													$os[6][1] = substr($parse,29,225);
													$os[7][0] = "ArticleValue";
													//$os[7][1] = substr($parse,254,13);
													$os[7][1] = substr(trim(substr($parse,254,13)),0,strlen(trim(substr($parse,254,13)))-2).'.'.substr(trim(substr($parse,254,13)),strlen(trim(substr($parse,254,13)))-2,2);
													$os[8][0] = "Box39Text";
													$os[8][1] = substr($parse,267,3);
													$os[9][0] = "CertificateOriginType";
													$os[9][1] = substr($parse,270,4);
													$os[10][0]= "CertificateOriginNumber";
													$os[10][1]= substr($parse,274,10);
													$os[11][0]= "ConArticleCurrency";
													$os[11][1]= substr($parse,284,3);
													$os[12][0]= "ConArticleInvCurrency";
													$os[12][1]= substr($parse,287,3);
													$os[13][0]= "ConArticleSVC";
													$os[13][1]= substr($parse,290,3);
													$os[14][0]= "ConArticleSVCC";
													$os[14][1]= substr($parse,293,3);
													$os[15][0]= "ExportDocCode";
													$os[15][1]= substr($parse,296,1);
													$os[16][0]= "ConArticleXDocCity";
													$os[16][1]= substr($parse,297,30);
													$os[17][0]= "ConArticleXDocDCen";
													$os[17][1]= substr($parse,327,2);
													$os[18][0]= "ConArticleXDocDYY";
													$os[18][1]= substr($parse,329,2);
													$os[19][0]= "ConArticleXDocDMM";
													$os[19][1]= substr($parse,331,2);
													$os[20][0]= "ConArticleXDocDDD";
													$os[20][1]= substr($parse,333,2);
													$os[21][0]= "ConArticleXDocNo";
													$os[21][1]= substr($parse,335,13);
													$os[22][0]= "ConArticleXDocType";
													$os[22][1]= substr($parse,348,4);
													$os[23][0]= "TmpXDecNo";
													$os[23][1]= substr($parse,352,13);
													$os[24][0]= "Grossweight";
													$os[24][1]= substr($parse,365,8);
													$os[25][0]= "HazardGoodCodes";
													$os[25][1]= substr($parse,373,4);
													$os[26][0]= "TNoItems";
													$os[26][1]= substr($parse,377,3);
													$os[27][0]= "NoExportLicense";
													$os[27][1]= substr($parse,380,10);
													$os[28][0]= "ConArticleMarks";
													$os[28][1]= substr($parse,390,10);
													$os[29][0]= "NetWeight";
													//$os[29][1]= substr($parse,400,8);
													$os[29][1] = substr(trim(substr($parse,400,8)),0,strlen(trim(substr($parse,400,8)))-3).'.'.substr(trim(substr($parse,400,8)),strlen(trim(substr($parse,400,8)))-3,3);
													$os[30][0]= "ConArticleCtryOrigin";
													$os[30][1]= substr($parse,408,3);
													$os[31][0]= "NoCtryCode";
													$os[31][1]= substr($parse,411,3);
													$os[32][0]= "CtryDescArticleOrigin";
													$os[32][1]= substr($parse,414,30);
													$os[33][0]= "PackDesc";
													$os[33][1]= substr($parse,444,20);
													$os[34][0]= "ConArticlePrevCDocCity";
													$os[34][1]= substr($parse,464,30);
													$os[35][0]= "ConArticlePrevCDocDCen";
													$os[35][1]= substr($parse,494,2);
													$os[36][0]= "ConArticlePrevCDocDYY";
													$os[36][1]= substr($parse,496,2);
													$os[37][0]= "ConArticlePrevCDocDMM";
													$os[37][1]= substr($parse,498,2);
													$os[38][0]= "ConArticlePrevCDocDDD";
													$os[38][1]= substr($parse,500,2);
													$os[39][0]= "PrevCustumerDocNo";
													$os[39][1]= substr($parse,502,13);
													$os[40][0]= "PrevCustomerDocType";
													$os[40][1]= substr($parse,515,4);
													$os[41][0]= "ProcedureCode1";
													$os[41][1]= substr($parse,519,4);
													$os[42][0]= "ProcedureCode2";
													$os[42][1]= substr($parse,523,3);
													$os[43][0]= "SValArticleCon";
													$os[43][1]= substr($parse,526,13);
													$os[44][0]= "SIndicator";
													$os[44][1]= substr($parse,539,1);
													$os[45][0]= "SimIndicator";
													$os[45][1]= substr($parse,540,1);
													$os[46][0]= "SpMentionText1";
													$os[46][1]= substr($parse,541,40);
													$os[47][0]= "SPMentionText2";
													$os[47][1]= substr($parse,581,40);
													$os[48][0]= "StatValue";
													$os[48][1]= substr($parse,621,13);
													$os[49][0]= "ConArticleTSDocCity";
													$os[49][1]= substr($parse,634,30);
													$os[50][0]= "ConArticleTSDocDCen";
													$os[50][1]= substr($parse,664,2);
													$os[51][0]= "ConArticleTSDocDYY";
													$os[51][1]= substr($parse,666,2);
													$os[52][0]= "ConArticleTSDocDMM";
													$os[52][1]= substr($parse,668,2);
													$os[53][0]= "ConArticleTSDocDDD";
													$os[53][1]= substr($parse,670,2);
													$os[54][0]= "TypeTDOC";
													$os[54][1]= substr($parse,687,4);
													$os[55][0]= "TmpTDOCNo";
													$os[55][1]= substr($parse,691,15);
													$os[56][0]= "HTS";
													$os[56][1]= substr($parse,706,30);
													$os[57][0]= "TransType";
													$os[57][1]= substr($parse,736,2);
													$os[58][0]= "UNHazardCode";
													$os[58][1]= substr($parse,738,4);
													$os[59][0]= "UnitDesc";
													$os[59][1]= substr($parse,742,12);
													$os[60][0]= "UnitNum";
													$os[60][1]= substr($parse,754,4);

													$osid=InsParsedData("osparse",$os,$user,"idparse");

													$rec=Delimetermaker($os);

													$oscont[0][0]= "Calculated";
													$oscont[0][1]= substr($parse,758,13);
													$oscont[1][0]= "ActTradeStat";
													$oscont[1][1]= substr($parse,771,4);
													$oscont[2][0]= "ConOrigin";
													$oscont[2][1]= substr($parse,775,5);
													$oscont[3][0]= "LocClearanceDepot";
													$oscont[3][1]= substr($parse,780,5);
													$oscont[4][0]= "ConBusLoc";
													$oscont[4][1]= substr($parse,785,5);
													$oscont[5][0]= "ConDes";
													$oscont[5][1]= substr($parse,790,5);
													$oscont[6][0]= "LocDocAutoAlloc";
													$oscont[6][1]= substr($parse,795,5);
													$oscont[7][0]= "LocNxtDepotAutoAlloc";
													$oscont[7][1]= substr($parse,800,5);
													$oscont[8][0]= "ClientRefText";
													$oscont[8][1]= substr($parse,805,24);
													$oscont[9][0]= "CODClientText";
													$oscont[9][1]= substr($parse,829,13);
													$oscont[10][0]= "ConIDAltConID";
													$oscont[10][1]= substr($parse,842,15);
													$oscont[11][0]= "ControlledIndicator";
													$oscont[11][1]= substr($parse,857,1);
													$oscont[12][0]= "CtryIDGoodsOrigin";
													$oscont[12][1]= substr($parse,858,3);
													$oscont[13][0]= "NumCtryCode";
													$oscont[13][1]= substr($parse,861,3);
													$oscont[14][0]= "CtryDescGoodsOrigin";
													$oscont[14][1]= substr($parse,864,30);
													$oscont[15][0]= "CreationDate";
													$oscont[15][1]= substr($parse,894,8);
													$oscont[16][0]= "CreationTime";
													$oscont[16][1]= substr($parse,902,4);
													$oscont[17][0]= "CustomerDeliveryNo";
													$oscont[17][1]= substr($parse,907,3);
													$oscont[18][0]= "CustomerDeliveryCity";
													$oscont[18][1]= substr($parse,909,24);
													$oscont[19][0]= "CustomerTradeNo";
													$oscont[19][1]= substr($parse,933,4);
													$oscont[20][0]= "CurrencyCodeValue";
													$oscont[20][1]= substr($parse,937,3);
													$oscont[21][0]= "ConInsCurrencyCode";
													$oscont[21][1]= substr($parse,940,3);
													$oscont[22][0]= "DeliveryIns";
													$oscont[22][1]= substr($parse,943,1);
													$oscont[23][0]= "SPInstruction";
													$oscont[23][1]= substr($parse,944,60);
													$oscont[24][0]= "ConDepoDCen";
													$oscont[24][1]= substr($parse,1004,2);
													$oscont[25][0]= "ConDepoDYY";
													$oscont[25][1]= substr($parse,1006,2);
													$oscont[26][0]= "ConDepoDMM";
													$oscont[26][1]= substr($parse,1008,2);
													$oscont[27][0]= "ConDepoDDD";
													$oscont[27][1]= substr($parse,1010,2);
													$oscont[28][0]= "ConDivID";
													$oscont[28][1]= substr($parse,1012,3);
													$oscont[29][0]= "ConDocIndicator";
													$oscont[29][1]= substr($parse,1015,1);
													$oscont[30][0]= "ConExhibit";
													$oscont[30][1]= substr($parse,1016,1);
													$oscont[31][0]= "InvNumber";
													$oscont[31][1]= substr($parse,1017,30);
													$oscont[32][0]= "InvDetails";
													$oscont[32][1]= substr($parse,1078,30);
													$oscont[33][0]= "ConGoodsDesc";
													$oscont[33][1]= substr($parse,1107,30);
													$oscont[34][0]= "HazardGoodsCode";
													$oscont[34][1]= substr($parse,1137,4);
													$oscont[35][0]= "ConInsAmt";
													$oscont[35][1]= substr($parse,1141,13);
													$oscont[36][0]= "ServiceLvlCode";
													$oscont[36][1]= substr($parse,1154,2);
													$oscont[37][0]= "ActVolCon";
													$oscont[37][1]= substr($parse,1156,7);
													$oscont[38][0]= "ConGrossWeight";
													$oscont[38][1]= substr($parse,1163,8);
													$oscont[39][0]= "RndWholeCGWt";
													$oscont[39][1]= substr($parse,1171,5);
													$oscont[40][0]= "ItemQty";
													$oscont[40][1]= substr($parse,1176,4);
													$oscont[41][0]= "ConSumVol";
													$oscont[41][1]= substr($parse,1180,6);
													$oscont[42][0]= "ConSumGWt";
													$oscont[42][1]= substr($parse,1187,8);
													$oscont[43][0]= "RndWholeGWt";
													$oscont[43][1]= substr($parse,1195,5);
													$oscont[44][0]= "OptCode1";
													$oscont[44][1]= substr($parse,1199,3);
													$oscont[45][0]= "Optcode2";
													$oscont[45][1]= substr($parse,1202,3);
													$oscont[46][0]= "Optcode3";
													$oscont[46][1]= substr($parse,1205,3);
													$oscont[47][0]= "OptCode4";
													$oscont[47][1]= substr($parse,1208,3);
													$oscont[48][0]= "ConPackCode";
													$oscont[48][1]= substr($parse,1211,2);
													$oscont[49][0]= "ConPackDescESAD";
													$oscont[49][1]= substr($parse,1213,20);
													$oscont[50][0]= "ProdIndicator";
													$oscont[50][1]= substr($parse,1233,4);
													$oscont[51][0]= "PWConIndicator";
													$oscont[51][1]= substr($parse,1237,1);
													$oscont[52][0]= "PWConScanIndicator";
													$oscont[52][1]= substr($parse,1238,1);
													$oscont[53][0]= "SelfCollectionIndicator";
													$oscont[53][1]= substr($parse,1239,1);
													$oscont[54][0]= "TDOCPReq";
													$oscont[54][1]= substr($parse,1240,1);
													$oscont[55][0]= "TDOCSCon";
													$oscont[55][1]= substr($parse,1241,1);
													$oscont[56][0]= "ConPayType";
													$oscont[56][1]= substr($parse,1242,1);
													$oscont[57][0]= "SumPcsCon";
													$oscont[57][1]= substr($parse,1243,5);
													$oscont[58][0]= "UNCodeHCC";
													$oscont[58][1]= substr($parse,1248,4);
													$oscont[59][0]= "ConVal";
													$oscont[59][1]= substr($parse,1252,3);
													$oscont[60][0]= "idosparse";
													$oscont[60][1]= $osid;

													$result=InsParsedData("osparsecont",$oscont,$user,"idparsecont");

													$rec.=Delimetermaker($oscont);

													$sender[0][0]= "SenderAcctId";
													$sender[0][1]= substr($parse,1266,8);
													$sender[1][0]= "SenderAcctGrpID";
													$sender[1][1]= substr($parse,1275,3);
													$sender[2][0]= "SenderLineAdd1";
													$sender[2][1]= substr($parse,1278,30);
													$sender[3][0]= "SenderLineAdd2";
													$sender[3][1]= substr($parse,1308,30);
													$sender[4][0]= "SenderLineAdd3";
													$sender[4][1]= substr($parse,1338,30);
													$sender[5][0]= "SenderCity";
													$sender[5][1]= substr($parse,1368,30);
													$sender[6][0]= "SenderCtryID";
													$sender[6][1]= substr($parse,1398,3);
													$sender[7][0]= "SenderCtryCode";
													$sender[7][1]= substr($parse,1401,3);
													$sender[8][0]= "SenderCtryDesc";
													$sender[8][1]= substr($parse,1404,30);
													$sender[9][0]= "SenderCTCName";
													$sender[9][1]= substr($parse,1434,22);
													$sender[10][0]= "SenderTel1";
													$sender[10][1]= substr($parse,1456,6);
													$sender[11][0]= "SenderTel2";
													$sender[11][1]= substr($parse,1463,8);
													$sender[12][0]= "SenderNADID";
													$sender[12][1]= substr($parse,1472,12);
													$sender[13][0]= "SenderName";
													$sender[13][1]= substr($parse,1484,50);
													$sender[14][0]= "SenderPostCode";
													$sender[14][1]= substr($parse,1534,8);
													$sender[15][0]= "SenderProvName";
													$sender[15][1]= substr($parse,1543,30);
													$sender[16][0]= "SenderVAT";
													$sender[16][1]= substr($parse,1573,20);
													$sender[17][0]= "idosparse";
													$sender[17][1]= $osid;

													$result=InsParsedData("ossender",$sender,$user,"idossender");

													$rec.=Delimetermaker($sender);

													$receive[0][0]= "ReceiverAcctID";
													$receive[0][1]= substr($parse,1593,8);
													$receive[1][0]= "ReceiverAcctGrpID";
													$receive[1][1]= substr($parse,1602,3);
													$receive[2][0]= "ReceiverLineAdd1";
													$receive[2][1]= substr($parse,1606,30);
													$receive[3][0]= "ReceiverLineAdd2";
													$receive[3][1]= substr($parse,1635,30);
													$receive[4][0]= "ReceiverLineAdd3";
													$receive[4][1]= substr($parse,1695,30);
													$receive[5][0]= "ReceiverCity";
													$receive[5][1]= substr($parse,1695,30);
													$receive[6][0]= "ReceiverCtryID";
													$receive[6][1]= substr($parse,1725,3);
													$receive[7][0]= "ReceiverCtryCode";
													$receive[7][1]= substr($parse,1728,3);
													$receive[8][0]= "ReceiverCtryDesc";
													$receive[8][1]= substr($parse,1731,30);
													$receive[9][0]= "ReceiverCTCName";
													$receive[9][1]= substr($parse,1761,22);
													$receive[10][0]= "ReceiverTel1";
													$receive[10][1]= substr($parse,1783,7);
													$receive[11][0]= "ReceiverTel2";
													$receive[11][1]= substr($parse,1790,8);
													$receive[12][0]= "ReceiverNADID";
													$receive[12][1]= substr($parse,1799,12);
													$receive[13][0]= "ReceiverName";
													$receive[13][1]= substr($parse,1811,50);
													$receive[14][0]= "ReceiverPostCode";
													$receive[14][1]= substr($parse,1861,9);
													$receive[15][0]= "ReceiverProvName";
													$receive[15][1]= substr($parse,1870,30);
													$receive[16][0]= "ReceiverVAT";
													$receive[16][1]= substr($parse,1900,20);
													$receive[17][0]= "idosparse";
													$receive[17][1]= $osid;

													$result=InsParsedData("osreceiver",$receive,$user,"idosreceiver");

													$rec.=Delimetermaker($receive);

													$collect[0][0]= "CollectionAcctID";
													$collect[0][1]= substr($parse,1920,9);
													$collect[1][0]= "CollectionAcctGrpID";
													$collect[1][1]= substr($parse,1929,3);
													$collect[2][0]= "CollectionLineAddr1";
													$collect[2][1]= substr($parse,1932,30);
													$collect[3][0]= "CollectionLineAddr2";
													$collect[3][1]= substr($parse,1962,30);
													$collect[4][0]= "CollectionLineAddr3";
													$collect[4][1]= substr($parse,1992,30);
													$collect[5][0]= "CollectionCity";
													$collect[5][1]= substr($parse,2022,30);
													$collect[6][0]= "CollectionCtryID";
													$collect[6][1]= substr($parse,2052,3);
													$collect[7][0]= "CollectionCtryCode";
													$collect[7][1]= substr($parse,2055,3);
													$collect[8][0]= "CollectionCtryDesc";
													$collect[8][1]= substr($parse,2059,30);
													$collect[9][0]= "CollectionCTCName";
													$collect[9][1]= substr($parse,2088,22);
													$collect[10][0]= "CollectionTel1";
													$collect[10][1]= substr($parse,2110,7);
													$collect[11][0]= "CollectionTel2";
													$collect[11][1]= substr($parse,2117,9);
													$collect[12][0]= "CollectionNADID";
													$collect[12][1]= substr($parse,2126,12);
													$collect[13][0]= "CollectionName";
													$collect[13][1]= substr($parse,2138,50);
													$collect[14][0]= "CollectionPostCode";
													$collect[14][1]= substr($parse,2188,9);
													$collect[15][0]= "CollectionProvName";
													$collect[15][1]= substr($parse,2197,30);
													$collect[16][0]= "CollectionVAT";
													$collect[16][1]= substr($parse,2227,20);

													$rec.=Delimetermaker($collect);

													$collect[17][0]= "idosparse";
													$collect[17][1]= $osid;

													$result=InsParsedData("oscollection", $collect, $user, "idoscollection");

													$delivery[0][0]= "DeliveryAcctID";
													$delivery[0][1]= substr($parse,2247,9);
													$delivery[1][0]= "DeliveryAcctGrpID";
													$delivery[1][1]= substr($parse,2256,3);
													$delivery[2][0]= "DeliveryLineAdd1";
													$delivery[2][1]= substr($parse,2259,30);
													$delivery[3][0]= "DeliveryLineAdd2";
													$delivery[3][1]= substr($parse,2289,30);
													$delivery[4][0]= "DeliveryLineAdd3";
													$delivery[4][1]= substr($parse,2319,30);
													$delivery[5][0]= "DeliveryCity";
													$delivery[5][1]= substr($parse,2349,30);
													$delivery[6][0]= "DeliveryCtryID";
													$delivery[6][1]= substr($parse,2379,3);
													$delivery[7][0]= "DeliveryCtryCode";
													$delivery[7][1]= substr($parse,2382,3);
													$delivery[8][0]= "DeliveryCtryDesc";
													$delivery[8][1]= substr($parse,2385,30);
													$delivery[9][0]= "DeliveryCTCName";
													$delivery[9][1]= substr($parse,2415,22);
													$delivery[10][0]= "DeliveryTel1";
													$delivery[10][1]= substr($parse,2437,7);
													$delivery[11][0]= "DeliveryTel2";
													$delivery[11][1]= substr($parse,2444,9);
													$delivery[12][0]= "DeliveryNADID";
													$delivery[12][1]= substr($parse,2453,12);
													$delivery[13][0]= "DeliveryName";
													$delivery[13][1]= substr($parse,2465,50);
													$delivery[14][0]= "DeliveryPostCode";
													$delivery[14][1]= substr($parse,2515,9);
													$delivery[15][0]= "DeliveryProvName";
													$delivery[15][1]= substr($parse,2524,30);
													$delivery[16][0]= "DeliveryVAT";
													$delivery[16][1]= substr($parse,2554,20);

													$rec.=Delimetermaker($delivery);

													$delivery[17][0]= "idosparse";
													$delivery[17][1]= $osid;

													$result=InsParsedData("osdelivery", $delivery, $user, "idosdelivery");

													$quantum[0][0]= "QuantumMov";
													$quantum[0][1]= substr($parse,2574,4);
													$quantum[1][0]= "ActArrivalDate";
													$quantum[1][1]= substr($parse,2578,8);
													$quantum[2][0]= "ActArrivalTime";
													$quantum[2][1]= substr($parse,2586,4);
													$quantum[3][0]= "ActDepartDate";
													$quantum[3][1]= substr($parse,2590,8);
													$quantum[4][0]= "ActDepartTime";
													$quantum[4][1]= substr($parse,2598,4);
													$quantum[5][0]= "CompanyID";
													$quantum[5][1]= substr($parse,2602,2);
													$quantum[6][0]= "DepartCtry";
													$quantum[6][1]= substr($parse,2604,3);
													$quantum[7][0]= "CtryCode";
													$quantum[7][1]= substr($parse,2607,3);
													$quantum[8][0]= "CtryDesc";
													$quantum[8][1]= substr($parse,2610,30);
													$quantum[9][0]= "DepartCtryID";
													$quantum[9][1]= substr($parse,2640,3);
													$quantum[10][0]= "DepartCtryCode";
													$quantum[10][1]= substr($parse,2643,3);
													$quantum[11][0]= "DepartCtryDesc";
													$quantum[11][1]= substr($parse,2646,30);

													$rec.=Delimetermaker($quantum);

													$quantum[12][0]= "idosparse";
													$quantum[12][1]= $osid;

													$result=InsParsedData("osquantum", $quantum, $user, "idosq");

													$carrier[0][0]= "CarrierCode";
													$carrier[0][1]= substr($parse,2676,3);
													$carrier[1][0]= "DestMovement";
													$carrier[1][1]= substr($parse,2679,5);
													$carrier[2][0]= "UserDeptDate";
													$carrier[2][1]= substr($parse,2684,8);
													$carrier[3][0]= "ContainerTypeCode";
													$carrier[3][1]= substr($parse,2692,1);
													$carrier[4][0]= "ContainerNum";
													$carrier[4][1]= substr($parse,2693,20);
													$carrier[5][0]= "VoyageTransportType";
													$carrier[5][1]= substr($parse,2713,2);
													$carrier[6][0]= "BorderTypeCode";
													$carrier[6][1]= substr($parse,2715,2);
													$carrier[7][0]= "DepartLicense";
													$carrier[7][1]= substr($parse,2717,15);
													$carrier[8][0]= "BorderLicense";
													$carrier[8][1]= substr($parse,2732,15);
													$carrier[9][0]= "MAWB";
													$carrier[9][1]= substr($parse,2747,15);
													$carrier[10][0]= "blank5";
													$carrier[10][1]= substr($parse,2762,5);
													//$carrier[11][0]= "FlightNo";
													//$carrier[11][1]= '';
													//$carrier[11][0]= "FlightNo";
													//$carrier[11][1]= substr($parse,2767,8);
													$carrier[12][0]= "FlightOrigin";
													$carrier[12][1]= substr($parse,2775,5);
													$carrier[13][0]= "BorderCode";
													$carrier[13][1]= substr($parse,2780,4);
													$carrier[14][0]= "DepartSeal";
													$carrier[14][1]= substr($parse,2784,8);
													$carrier[15][0]= "BorderSeal";
													$carrier[15][1]= substr($parse,2792,8);
													$carrier[16][0]= "TypeSector";
													$carrier[16][1]= substr($parse,2800,1);
													$carrier[17][0]= "Starts";
													$carrier[17][1]= substr($parse,2801,1);
													$carrier[18][0]= "CarrierMode";
													$carrier[18][1]= substr($parse,2802,2);
													$carrier[19][0]= "CarrierBound";
													$carrier[19][1]= substr($parse,2804,3);
													$carrier[20][0]= "SectorType";
													$carrier[20][1]= substr($parse,2807,3);
													$carrier[21][0]= "LicenseInfo";
													$carrier[21][1]= substr($parse,2810,15);
													$carrier[22][0]= "DefaCurrency";
													$carrier[22][1]= substr($parse,2825,25);
													$carrier[23][0]= "BorderCross";
													$carrier[23][1]= substr($parse,2850,25);

													$rec.=Delimetermaker($carrier);

													$carrier[24][0]= "idosparse";
													$carrier[24][1]= $osid;

													$result=InsParsedData("oscarrier", $carrier, $user, "idoscarrier");

													$agent[0][0]= "ImpCtryAgent";
													$agent[0][1]= substr($parse,2875,3);
													$agent[1][0]= "ImpCtryCode";
													$agent[1][1]= substr($parse,2878,3);
													$agent[2][0]= "ImpCtryDesc";
													$agent[2][1]= substr($parse,2881,30);
													$agent[3][0]= "ImpName";
													$agent[3][1]= substr($parse,2911,30);
													$agent[4][0]= "ImpAdd1";
													$agent[4][1]= substr($parse,2941,30);
													$agent[5][0]= "ImpAdd2";
													$agent[5][1]= substr($parse,2971,30);
													$agent[6][0]= "ImpAdd3";
													$agent[6][1]= substr($parse,3001,30);
													$agent[7][0]= "ImpPostCode";
													$agent[7][1]= substr($parse,3031,9);
													$agent[8][0]= "Impcity";
													$agent[8][1]= substr($parse,3040,30);

													$rec.=Delimetermaker($agent);

													$agent[9][0]= "idosparse";
													$agent[9][1]= $osid;

													$result=InsParsedData("osagent", $agent, $user, "idodagent");

													$tnt[0][0]= "TNTName";
													$tnt[0][1]= substr($parse,3070,30);
													$tnt[1][0]= "TNTPostBoxCode";
													$tnt[1][1]= substr($parse,3100,9);
													$tnt[2][0]= "TNTPostBoxCityName";
													$tnt[2][1]= substr($parse,3109,30);
													$tnt[3][0]= "TNTPostBoxPostCode";
													$tnt[3][1]= substr($parse,3139,9);
													$tnt[4][0]= "TNTVAT";
													$tnt[4][1]= substr($parse,3148,16);
													$tnt[5][0]= "TNTProvName";
													$tnt[5][1]= substr($parse,3164,30);
													$tnt[6][0]= "TNTUserID";
													$tnt[6][1]= substr($parse,3194,6);

													$rec.=Delimetermaker($tnt);

													$tnt[7][0]= "idosparse";
													$tnt[7][1]= $osid;

													$result=InsParsedData("ostntinfo", $tnt, $user, "idostntinfo");

													$edita[0][0] = "consigneeno";
													$edita[0][1] = trim($os[2][1]);
													//$edita[1][0] = "articledesc";
													//$edita[1][1] = $os[6][1];
													$edita[1][0] = "articledesc";
													$edita[1][1] = trim($os[6][1]);
													$edita[2][0] = "articlevalue";
													$edita[2][1] = $os[7][1];
													$edita[3][0] = "articlecurrency";
													$edita[3][1] = $os[11][1];
													$edita[4][0] = "usdrate";
													if($os[11][1]!="USD"){
														$convert = (float)$os[7][1]*currencyconvert($os[11][1],"USD");
													} else {
														$convert = (float)$os[7][1];
													}
													$edita[4][1] = floatval($convert);
													$edit[0][0] = "usdrate";
													$edit[0][1] = $convert;
													$edita[5][0] = "phprate";
													$edita[5][1] = round($convert*currencyconvert("USD","PHP"),2);
													$edit[1][0] = "phprate";
													$edit[1][1] = $edita[5][1];
													$edita[6][0] = "tntstat";
													$edita[6][1] = "PAC";
													$edit[2][0] = "tntph";
													$edit[2][1] = "PAC";
													$edita[7][0] = "status";
													$edita[7][1] = "A";
													$edita[8][0] = "archive";
													$edita[8][1] = "N";
													$edita[9][0] = "idfile";
													$edita[9][1] = $idFile;
													$edita[10][0]= "unitinfo";
													$edita[10][1]= ""; //for editing
													$edita[11][0]= "contype";
													$edita[11][1]= $oscont[50][1];
													$edita[12][0]= "sendername";
													$edita[12][1]= $sender[13][1];
													$edita[13][0]= "receivername";
													$edita[13][1]= $receive[13][1];
													$edita[14][0]= "pcs";
													$edita[14][1]= $os[26][1];
													$edita[15][0]= "totalpcs";
													$edita[15][1]= $oscont[40][1];
													$edita[16][0]= "weight";
													$edita[16][1]= $os[29][1];
													$edita[17][0]= "convertprate";
													$edita[17][1]= currencyconvert("USD","PHP");
													$edit[3][0] = "phpexch";
													$edit[3][1] = $edita[17][1];
													$edita[18][0]= "idosparse";
													$edita[18][1]= $osid;
													$edita[19][0]= "tagvalue";
													/* if($convert>=$determinant){
														$edita[19][1] = "H";
													} else {
														$edita[19][1] = "L";
													} */
													$edita[20][0] = "itemorigin";
													$edita[20][1] = trim($os[30][1]).trim($oscont[2][1]);
													$edita[21][0] = "itemdest";
													$edita[21][1] = 'PH'.trim($oscont[5][1]);
													$edita[22][0] = "arrivalport";
													if($carrier[1][1]=='MNL'){
														$edita[22][1] = 'P03';
													} else {
														$edita[22][1] = 'P03';
													}
													$edita[23][0] = "dup";
													$edita[23][1] = 'N';
													$edita[24][0] = "mstatus";
													$edita[24][1] = 'N';
													//$edita[25][0] = "flightno";
													//$edita[25][1] = '';
													//$edita[25][0] = "flightno";
													//$edita[25][1] = trim($carrier[0][1]).$carrier[11][1];
													$edita[26][0] = "flightmawb";
													$edita[26][1] = trim($carrier[9][1]);
													$edita[27][0] = "flightype";
													$edita[27][1] = trim($carrier[16][1]);
													$edita[28][0] = "modetype";
													$edita[28][1] = trim($carrier[18][1]);
													$edita[29][0] = "departport";
													$edita[29][1] = trim($carrier[12][1]);
													$edita[30][0] = "depot";
													$edita[30][1] = trim($carrier[1][1]);
													//$edita[31][0] = "arrivaldate";
													//$edita[31][1] = substr(trim($carrier[2][1]),0,4).'-'.substr(trim($carrier[2][1]),4,2).'-'.substr(trim($carrier[2][1]),6,2);
													$edita[32][0] = "boctype";
													//if ($edita[2][1]>=$infval ){ - Remarked: IJVelas - Use converted currency in determining Formal, Informal, NCV
														//if($edita[2][1]>=$fval){
													if ($edit[0][1]>=$infval ){
														if($edit[0][1]>=$fval){
															$edita[32][1]='F';
															$edita[19][1] = 'H';
														} else {
															$edita[19][1] = 'L';
															$edita[32][1]='I';
														}
													} else {
														if($edita[16][1]>=$infwt){
															if($edita[16][1]>=$fwt){
																$edita[19][1] = 'L';
																$edita[32][1]='I';
															} else {
																$edita[19][1] = 'N';
																$edita[32][1]='N';
															}
														} else {
															if($edita[16][1]>=$infwt){
																$edita[19][1] = 'L';
																$edita[32][1]='I';
															} else {
																$edita[19][1] = 'N';
																$edita[32][1]='N';
															}
														}
													}

													if(!empty($edita[31][1])&&$edita[31][1]=='--') {
														unset($edita[31]);
													}

													$edita[33][0] = "currencyusdrate";
													//$edita[33][1] = '0.0';

													if($os[11][1]!="USD"){
														$edita[33][1] = currencyconvert($os[11][1],"USD");
													}

													$edita[34][0] = "osdataflag";
													$edita[34][1] = 1;

													$constat = 1;

													$tedita = $edita;

													foreach($edita as $kedita=>$vedita) {

														//pre(array('$vedita'=>$vedita));

														//$zedita = trim($edita[$kedita][1]);

														if($vedita[0]=='articlevalue'&&!isValidNumeric($vedita[1])) {
															unset($tedita[$kedita]);
														} else
														if($vedita[0]=='phprate'&&!isValidNumeric($vedita[1])) {
															unset($tedita[$kedita]);
														} else
														if($vedita[0]=='usdrate'&&!isValidNumeric($vedita[1])) {
															unset($tedita[$kedita]);
														} else
														if($vedita[0]=='weight'&&!isValidNumeric($vedita[1])) {
															unset($tedita[$kedita]);
														} else
														if($vedita[0]=='convertprate'&&!isValidNumeric($vedita[1])) {
															unset($tedita[$kedita]);
														} else
														if($vedita[0]=='currencyusdrate'&&!isValidNumeric($vedita[1])) {
															unset($tedita[$kedita]);
														} /*else
														if(empty($zedita)) {
															unset($tedita[$kedita]);
															//pre(array('empty_'.$kedita=>array($edita[$kedita][0],"'".$edita[$kedita][1]."'")));
														} else {
															//pre(array('notempty_'.$kedita=>array($edita[$kedita][0],"'".$edita[$kedita][1]."'")));
														}*/

														if($vedita[0]=='currencyusdrate'&&@trim($vedita[1])=='') {
															unset($tedita[$kedita]);
														}

														if($vedita[0]=='currencyusdrate'&&!isset($vedita[1])) {
															unset($tedita[$kedita]);
														}

														/*if($vedita[0]=='currencyusdrate') {
															pre(array('$tedita[$kedita]'=>$tedita[$kedita]));
															//pre(array('$tedita'=>$tedita));
															//die;
														}*/

														// trim(articledesc)<>'DOCUMENTS'
														// trim(articledesc)<>'DOCUMENT'
														// trim(contype)<>'15D'
														// trim(contype)<>'D')

														if($constat==1) {
															//if($vedita[0]=='articledesc') {
															if($vedita[0]=='articledesc' && (trim($vedita[1])=='DOCUMENTS'||trim($vedita[1])=='DOCUMENT')) {
																$constat = 255; // Document
																//pre(array('$vedita'=>$vedita));
															} else
															if($vedita[0]=='contype' && (trim($vedita[1])=='15D'||trim($vedita[1])=='D')) {
															//if($vedita[0]=='contype') {
																$constat = 255; // Document
																//pre(array('$vedita'=>$vedita));
															}
														}

													}

													foreach($tedita as $kedita=>$vedita) {
														if(!isset($vedita[1])) {
															unset($tedita[$kedita]);
														} else
														if(trim($vedita[1])=='') {
															unset($tedita[$kedita]);
														}
													}

													$tedita[] = array(0=>'constat',1=>$constat);

													//pre($edita);

													//die;

													//if(($dupdata=is_osdup($os[2][1],$os[6][1]))) {
													//	pre($dupdata);
													//}

													//$result=InsParsedData("osparseinfo", $edita, $user, "idosparseinfo");
													$result=InsParsedData("osparseinfo", $tedita, $user, "idosparseinfo");

													$rec.=Delimetermaker($edit);

													$parsereturn[$d] = substr($rec,0,strlen($rec)-1);

													$dup = oscheckdup($os[2][1],$os[6][1],$result,$osid);

											}
											$line=$line+1;
										}
									fclose($fh);
									$status.= "<br/>Total Record Parse: ".$d."<br/>";
									//$ftp=ftppath();
									$ftp="../ftpsim/";
									$filenew=str_replace(".dat","",basename($_FILES[$file_id]['name'])).'_TNTPH_OS'.date("Ymd_Hisu").".dat";

									if (!file_exists($ftp.$filenew)) {
										if (!$fh=fopen($ftp.$filenew,'x+')) {
											$status.= "Cannot open file ($filename)";

										}
										else
										{
											// Write $somecontent to our opened file.
											foreach ($parsereturn as $rewrite){
												if (fwrite($fh, $rewrite."\n") === FALSE) {
													$status.= "Cannot write to file ($filename)";
													exit;
												}
											}
										}



										fclose($fh);

										//$status.= "<b>Process Complete. Go to Pre-Arrival under Transaction Menu</b>";
										die("OK");
									} else {
										$status.= "The file $filenew is not writable";
									}

						}
				}
			} else {
				//$status.= "Error: ".$_FILES[$file_id]['error']." --- ".$_FILES[$file_id]['tmp_name']." %%% ".$file."($size)";
				die("Error: ".$_FILES[$file_id]['error']." --- ".$_FILES[$file_id]['tmp_name']." %%% ".$file."($size)");
			}

		}
	}

	//echo returnStatus($status);
}

echo $status;

function returnStatus($status){
	return "<html><body><script type='text/javascript'>function init(){if(top.uploadComplete) top.uploadComplete('".$status."'); }window.onload=init;
</script></body></html>";
}
