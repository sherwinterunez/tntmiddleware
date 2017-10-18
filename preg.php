<?php

/*$mno = '+639287710253';

$mno = '214';

$mno = '09493621618';

$regx = '(\d+)(\d{3})(\d{7})$';

if(preg_match('#'.$regx.'#',$mno,$matches)) {
	print_r(array('$mno'=>$mno,'$matches'=>$matches));
}*/

//////////////////////////////////////////

/*$str = '09-May 19:44: You are about to Pasaload P10 to 09493621255. Reply YES to confirm or NO to cancel to 808 w/in 15mins. P1/Pasa Txn.Ref:290474774655';

//$str = '09-May 19:44: You are about to Pasaload P10 to +639493621255. Reply YES to confirm or NO to cancel to 808 w/in 15mins. P1/Pasa Txn.Ref:290474774655';

//$regx = '(You\s+are\s+about\s+to\s+Pasaload)(\s+)(P)(\d+)(\s+to\s+)(\+\d+|\d+)(.+)(Ref\:)(\d+)';

//$regx = '(Pasaload)(\s+)(P)(\d+)(\s+to\s+)(\+\d+|\d+)(.+)(Ref\:)(\d+)';

//$regx = '(Pasaload)(.+?)(\d+)(.+?)(\+\d+|\d+)(.+?)(Ref\:)(\d+)';

//$regx = '(Pasaload)(.+?)(\d+)(.+?)(\+\d+\d{3}\d{7}|\d+\d{3}\d{7})(.+?)(Ref\:)(\d+)';

//$regx = '(Pasaload)(.+?)(\d+)(.+?)(\+\d+|\d+)(.+?)(Ref\:)(\d+)';

$regx = '(\+\d+\d{3}\d{7}|\d+\d{3}\d{7})(.+?)(Ref\:)(\d+)';

if(preg_match('#'.$regx.'#si',$str,$matches)) {
	print_r(array('$str'=>$str,'$regx'=>$regx,'$matches'=>$matches));
}*/

//////////////////////////////////////////

/*$str = 'eshop      rl      talk100      09287710253';

do {
	$str = str_replace('  ', ' ', trim($str));
} while(preg_match('#\s\s#si', $str));

$keys = explode(' ', $str);

print_r(array('$str'=>$str,'$keys'=>$keys));

*/

/*$str = array();

$str[] = '27May 1135: 09397599095 has loaded LOAD 5 (P4.77) to 09493621618. New Load Wallet Balance:P495.23. Ref:071058379805';
$str[] = '27-May 17:47:639397599095 has loaded BIG BYTES 30 to 09493621618. New Load Wallet Balance:P466.56. Ref:800008270828';
$str[] = '27-May 18:09: 639397599095 has loaded SMARTLoad (P11.47) to 09493621618. New Load Wallet Balance: P455.09. Ref:800008272452';

foreach($str as $v) {
	if(preg_match('/(\d+\d{3}\d{7}).+?loaded(.+?)to(.+?)(\d+\d{3}\d{7}).+?balance.+?(\d+\.\d+).+?ref.+?(\d+)/si',$v,$matches)) {
		print_r(array('$matches'=>$matches));
	}
}

// .+?(?<loadtransaction_simnumber>\d+\d{3}\d{7}).+?loaded(?<loadtransaction_product>.+?)to.+?(?<loadtransaction_recipientnumber>\d+\d{3}\d{7}).+?balance.+?(?<loadtransaction_balance>\d+\.\d+).+?ref.+?(?<loadtransaction_ref>\d+)


$preg = '.+?(?<loadtransaction_simnumber>\d+\d{3}\d{7}).+?balance.+?(?<loadtransaction_balance>\d+\.\d+).+?ref.+?(?<loadtransaction_ref>\d+)';
*/

$str = array();

$str[] = '21Jun 2313:15(14.7) successfully loaded to 09423192452. bal: 50.80. Ref#0621231357721965';

$str[] = '21Jun 2135:Loaded 10(9.9) to 09165347754. bal: 75.40. Ref#260745524';

$str[] = '21Jun 2101:15(14.7) successfully loaded to 09493621618. bal: 85.30. Ref#005854568113';

$str[] = '14Jul 1254:AT10(9.9) successfully loaded to 09219280227. bal: 10.40. Ref#16071412543415276';

$str[] = 'New!! MEGA250(Smart), AT12(150sms allnet+viber),AT30(Alltxt30), SURF50(Smartbro), SURF85, SURF200, SURF250, SURF500, SURF995.  Your OVERLoad Bal:0.50';


foreach($str as $v) {
	//if(preg_match('/.+?\:(?<productcode>.+?)\((?<cost>.+?)\).+?(?<mobileno>\d+\d{3}\d{7}).+?bal\:\s+(?<balance>.+)\.\s+Ref\#(?<reference>\d+)/si',$v,$match)) {
	if(preg_match('/.+?Your\s+OVERLoad\s+Bal\:(?<balance>\d+\.\d+|\d+)/si',$v,$match)) {
		print_r(array('$match'=>$match));
	}
}








