<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* App User Module
*
* Date: June 9, 2016
*
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

if(!class_exists('APP_app_setting')) {

	class APP_app_setting extends APP_Base_Ajax {

		var $desc = 'setting';

		var $pathid = 'setting';
		var $parent = false;

		/*function __construct($mypathid,$myparent) {
			$this->pathid = $mypathid;
			$this->parent = $myparent;
			$this->init();
		}*/

		function __construct() {
			$this->init();
		}

		function __destruct() {
		}

		function init() {
			$this->add_rules();
		}

		function add_rules() {
			global $appaccess;

			$appaccess->rules($this->desc,'Setting Module');
			$appaccess->rules($this->desc,'Setting Module New');
			$appaccess->rules($this->desc,'Setting Module Edit');
			$appaccess->rules($this->desc,'Setting Module Delete');

			//$appaccess->rules($this->desc,'User Account');
			/*$appaccess->rules($this->desc,'User Account New Role');
			$appaccess->rules($this->desc,'User Account Edit Role');
			$appaccess->rules($this->desc,'User Account Delete Role');
			$appaccess->rules($this->desc,'User Account New User');
			$appaccess->rules($this->desc,'User Account Edit User');
			$appaccess->rules($this->desc,'User Account Delete User');
			$appaccess->rules($this->desc,'User Account Manage All');
			$appaccess->rules($this->desc,'User Account Change Role');
			$appaccess->rules($this->desc,'User Account Change User Login');*/
		}

		function _form_setting($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				//pre(array($routerid,$formid));

				$post = $this->vars['post'];

				$params = array();

				$readonly = true;

				$default_schoolyear = getCurrentSchoolYear();
				$default_tardinessgraceperiodminute = 30;
				$default_absentgraceperiodminute1 = 40;
				$default_absentgraceperiodminute2 = 0;
				$default_absentgraceperiodminute3 = 0;
				$default_titlefontsize = 30;

				$default_bulletin = 'DEMO UNIT... OBIS SOFTWARE TECHNOLOGY... OBIS SOFTWARE TECHNOLOGY... DEMO UNIT...';
				$default_timeinnotification = '%STUDENTFULLNAME% has timed-in at %DATETIME%';
				$default_timeoutnotification = '%STUDENTFULLNAME% has timed-out at %DATETIME%';
				$default_latenotification = '%STUDENTFULLNAME% as of %DATETIME% has not yet arrived in school.';
				$default_absentnotification = '%STUDENTFULLNAME% as of %DATETIME% did not arrived in school.';
				$default_timeinmessage = 'Welcome to School! Nice to see you again!';
				$default_timeoutmessage = 'Goodbye! Keep safe!';
				$default_latemessage = 'Welcome to School! You are encourage to come on time!';

				$settings_schoolyear = getOption('$SETTINGS_SCHOOLYEAR',$default_schoolyear);

				$settings_electronicbulletin = getOption('$SETTINGS_ELECTRONICBULLETIN',$default_bulletin);
				$settings_loginnotificationschooladmin = getOption('$SETTINGS_LOGINNOTIFICATIONSCHOOLADMIN','');
				$settings_loginnotificationschooladminsendsms = getOption('$SETTINGS_LOGINNOTIFICATIONSCHOOLADMINSENDSMS',false);
				$settings_loginnotificationostrelationshipmanager = getOption('$SETTINGS_LOGINNOTIFICATIONOSTRELATIONSHIPMANAGER','');
				$settings_loginnotificationostrelationshipmanagersendsms = getOption('$SETTINGS_LOGINNOTIFICATIONOSTRELATIONSHIPMANAGERSENDSMS',false);

				$settings_rfidinterval = getOption('$SETTINGS_RFIDINTERVAL',false);

				$settings_showadsinterval = getOption('$SETTINGS_SHOWADSINTERVAL',false);
				$settings_showadsintervalenable = getOption('$SETTINGS_SHOWADSINTERVALENABLE',false);

				$settings_servershutdownrfid = getOption('$SETTINGS_SERVERSHUTDOWNRFID',false);
				$settings_servershutdownrfidenable = getOption('$SETTINGS_SERVERSHUTDOWNRFIDENABLE',false);

				$settings_synctoserver = getOption('$SETTINGS_SYNCTOSERVER',false);

				$settings_tardinessgraceperiodminute = getOption('$SETTINGS_TARDINESSGRACEPERIODMINUTE',$default_tardinessgraceperiodminute);

				$settings_absentgraceperiodminute1 = getOption('$SETTINGS_ABSENTGRACEPERIODMINUTE1',$default_absentgraceperiodminute1);
				$settings_absentgraceperiodminute2 = getOption('$SETTINGS_ABSENTGRACEPERIODMINUTE2',$default_absentgraceperiodminute2);
				$settings_absentgraceperiodminute3 = getOption('$SETTINGS_ABSENTGRACEPERIODMINUTE3',$default_absentgraceperiodminute3);

				$settings_sendtimeinnotification  = getOption('$SETTINGS_SENDTIMEINNOTIFICATION',true);
				$settings_sendtimeoutnotification  = getOption('$SETTINGS_SENDTIMEOUTNOTIFICATION',true);
				$settings_sendlatenotification  = getOption('$SETTINGS_SENDLATENOTIFICATION',false);
				$settings_sendabsentnotification  = getOption('$SETTINGS_SENDABSENTNOTIFICATION',false);
				$settings_sendpushnotification  = getOption('$SETTINGS_SENDPUSHNOTIFICATION',false);
				$settings_sendsmsnotification  = getOption('$SETTINGS_SENDSMSNOTIFICATION',true);

				$settings_timeinnotification = getOption('$SETTINGS_TIMEINNOTIFICATION',$default_timeinnotification);
				$settings_timeoutnotification = getOption('$SETTINGS_TIMEOUTNOTIFICATION',$default_timeoutnotification);
				$settings_latenotification = getOption('$SETTINGS_LATENOTIFICATION',$default_latenotification);
				$settings_absentnotification = getOption('$SETTINGS_ABSENTNOTIFICATION',$default_absentnotification);

				$settings_timeinmessage = getOption('$SETTINGS_TIMEINMESSAGE',$default_timeinmessage);
				$settings_timeoutmessage = getOption('$SETTINGS_TIMEOUTMESSAGE',$default_timeoutmessage);
				$settings_latemessage = getOption('$SETTINGS_LATEMESSAGE',$default_latemessage);

				$settings_hidedb = getOption('$SETTINGS_HIDEDB',false);

				$settings_autodetectface = getOption('$SETTINGS_AUTODETECTFACE',false);

				$settings_bridgetoadmin = getOption('$SETTINGS_BRIDGETOADMIN',false);

				$settings_bridgetoadminip = getOption('$SETTINGS_BRIDGETOADMINIP','');

				$settings_licensekey = getOption('$SETTINGS_LICENSEKEY',false);

				$settings_electronicbulletindaily = getOption('$SETTINGS_ELECTRONICBULLETINDAILY',base64_encode(serialize(array())));

				if(!empty($settings_electronicbulletindaily)) {
					$settings_electronicbulletindaily = unserialize(base64_decode($settings_electronicbulletindaily));
				}

				$settings_useuhfrfidreader = getOption('$SETTINGS_USEUHFRFIDREADER',false);

				$settings_uhfrfidreadinterval = getOption('$SETTINGS_UHFRFIDREADINTERVAL',60);

				$settings_useinfraredbeam = getOption('$SETTINGS_USEINFRAREDBEAM',false);

				$settings_titlefontsize = getOption('$SETTINGS_TITLEFONTSIZE',$default_titlefontsize);

				//pre(array('$settings_electronicbulletindaily'=>$settings_electronicbulletindaily));

				/*$settings_electronicbulletindailydate = getOption('$SETTINGS_ELECTRONICBULLETINDAILYDATE',base64_encode(serialize(array())));
				$settings_electronicbulletindailymsg = getOption('$SETTINGS_ELECTRONICBULLETINDAILYMSG',base64_encode(serialize(array())));

				if(!empty($settings_electronicbulletindailydate)) {
					$settings_electronicbulletindailydate = unserialize(base64_decode($settings_electronicbulletindailydate));
				}

				if(!empty($settings_electronicbulletindailymsg)) {
					$settings_electronicbulletindailymsg = unserialize(base64_decode($settings_electronicbulletindailymsg));
				}*/

				if(!empty($post['method'])&&($post['method']=='settingedit')) {
					$readonly = false;
				}

				if(!empty($post['method'])&&$post['method']=='settingsave') {
					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Setting successfully saved!';
					//$retval['post'] = $post;

					//pre(array('$post',$post));

					/*$validsy = false;

					if(!empty($post['settings_schoolyear'])) {
						$sy = explode('-',$post['settings_schoolyear']);

						if(!empty($sy[0])&&!empty($sy[1])&&intval($sy[0])>2000&&intval($sy[1])>2000&&intval($sy[1])>intval($sy[0])&&(intval($sy[1])-intval($sy[0]))==1) {
							$validsy = true;
						}
					}*/

					if(!isValidSchoolYear($post['settings_schoolyear'])) {
						$retval = array();
						$retval['error_code'] = 4581;
						$retval['error_message'] = 'Invalid school year!';

						header_json();
						json_encode_return($retval);
						die;
					}

					if(!empty($post['settings_bridgetoadminip'])&&!isValidIp($post['settings_bridgetoadminip'])) {
						$retval = array();
						$retval['error_code'] = 4587;
						$retval['error_message'] = 'Invalid Bridge Admin IP Address';

						header_json();
						json_encode_return($retval);
						die;
					}

					setSetting('$SETTINGS_SCHOOLYEAR',!empty($post['settings_schoolyear'])?$post['settings_schoolyear']:$default_schoolyear);

					setSetting('$SETTINGS_ELECTRONICBULLETIN',!empty($post['settings_electronicbulletin'])?$post['settings_electronicbulletin']:$default_bulletin);

					setSetting('$SETTINGS_LOGINNOTIFICATIONSCHOOLADMIN',!empty($post['settings_loginnotificationschooladmin'])?$post['settings_loginnotificationschooladmin']:'');

					setSetting('$SETTINGS_LOGINNOTIFICATIONSCHOOLADMINSENDSMS',!empty($post['settings_loginnotificationschooladminsendsms'])?true:false);

					setSetting('$SETTINGS_LOGINNOTIFICATIONOSTRELATIONSHIPMANAGER',!empty($post['settings_loginnotificationostrelationshipmanager'])?$post['settings_loginnotificationostrelationshipmanager']:'');

					setSetting('$SETTINGS_LOGINNOTIFICATIONOSTRELATIONSHIPMANAGERSENDSMS',!empty($post['settings_loginnotificationostrelationshipmanagersendsms'])?true:false);

					setSetting('$SETTINGS_RFIDINTERVAL',!empty($post['settings_rfidinterval'])?intval($post['settings_rfidinterval']):5);

					setSetting('$SETTINGS_SHOWADSINTERVAL',!empty($post['settings_showadsinterval'])?intval($post['settings_showadsinterval']):30);

					setSetting('$SETTINGS_SHOWADSINTERVALENABLE',!empty($post['settings_showadsintervalenable'])?true:false);

					setSetting('$SETTINGS_SERVERSHUTDOWNRFID',!empty($post['settings_servershutdownrfid'])?trim($post['settings_servershutdownrfid']):'');

					setSetting('$SETTINGS_SERVERSHUTDOWNRFIDENABLE',!empty($post['settings_servershutdownrfidenable'])?true:false);

					setSetting('$SETTINGS_SYNCTOSERVER',!empty($post['settings_synctoserver'])?true:false);

					setSetting('$SETTINGS_TARDINESSGRACEPERIODMINUTE',!empty($post['settings_tardinessgraceperiodminute'])?intval($post['settings_tardinessgraceperiodminute']):$default_tardinessgraceperiodminute);

					setSetting('$SETTINGS_ABSENTGRACEPERIODMINUTE1',!empty($post['settings_absentgraceperiodminute1'])?intval($post['settings_absentgraceperiodminute1']):$default_absentgraceperiodminute1);

					setSetting('$SETTINGS_ABSENTGRACEPERIODMINUTE2',!empty($post['settings_absentgraceperiodminute2'])?intval($post['settings_absentgraceperiodminute2']):$default_absentgraceperiodminute2);

					setSetting('$SETTINGS_ABSENTGRACEPERIODMINUTE3',!empty($post['settings_absentgraceperiodminute3'])?intval($post['settings_absentgraceperiodminute3']):$default_absentgraceperiodminute3);

					setSetting('$SETTINGS_TIMEINNOTIFICATION',!empty($post['settings_timeinnotification'])?$post['settings_timeinnotification']:$default_timeinnotification);

					setSetting('$SETTINGS_TIMEOUTNOTIFICATION',!empty($post['settings_timeoutnotification'])?$post['settings_timeoutnotification']:$default_timeoutnotification);

					setSetting('$SETTINGS_LATENOTIFICATION',!empty($post['settings_latenotification'])?$post['settings_latenotification']:$default_latenotification);

					setSetting('$SETTINGS_ABSENTNOTIFICATION',!empty($post['settings_absentnotification'])?$post['settings_absentnotification']:$default_absentnotification);

					setSetting('$SETTINGS_SENDPUSHNOTIFICATION',!empty($post['settings_sendpushnotification'])?true:false);

					setSetting('$SETTINGS_SENDSMSNOTIFICATION',!empty($post['settings_sendsmsnotification'])?true:false);

					setSetting('$SETTINGS_SENDTIMEINNOTIFICATION',!empty($post['settings_sendtimeinnotification'])?true:false);

					setSetting('$SETTINGS_SENDTIMEOUTNOTIFICATION',!empty($post['settings_sendtimeoutnotification'])?true:false);

					setSetting('$SETTINGS_SENDLATENOTIFICATION',!empty($post['settings_sendlatenotification'])?true:false);

					setSetting('$SETTINGS_SENDABSENTNOTIFICATION',!empty($post['settings_sendabsentnotification'])?true:false);

					setSetting('$SETTINGS_TIMEINMESSAGE',!empty($post['settings_timeinmessage'])?$post['settings_timeinmessage']:$default_timeinmessage);

					setSetting('$SETTINGS_TIMEOUTMESSAGE',!empty($post['settings_timeoutmessage'])?$post['settings_timeoutmessage']:$default_timeoutmessage);

					setSetting('$SETTINGS_LATEMESSAGE',!empty($post['settings_latemessage'])?$post['settings_latemessage']:$default_latemessage);

					setSetting('$SETTINGS_HIDEDB',!empty($post['settings_hidedb'])?$post['settings_hidedb']:false);

					setSetting('$SETTINGS_AUTODETECTFACE',!empty($post['settings_autodetectface'])?$post['settings_autodetectface']:false);

					setSetting('$SETTINGS_LICENSEKEY',!empty($post['settings_licensekey'])?$post['settings_licensekey']:'');

					setSetting('$SETTINGS_BRIDGETOADMIN',!empty($post['settings_bridgetoadmin'])?true:false);

					setSetting('$SETTINGS_BRIDGETOADMINIP',!empty($post['settings_bridgetoadminip'])&&isValidIp($post['settings_bridgetoadminip'])?$post['settings_bridgetoadminip']:'');

					setSetting('$SETTINGS_USEUHFRFIDREADER',!empty($post['settings_useuhfrfidreader'])?true:false);

					setSetting('$SETTINGS_UHFRFIDREADINTERVAL',!empty($post['settings_uhfrfidreadinterval'])?$post['settings_uhfrfidreadinterval']:60);

					setSetting('$SETTINGS_USEINFRAREDBEAM',!empty($post['settings_useinfraredbeam'])?true:false);

					setSetting('$SETTINGS_TITLEFONTSIZE',!empty($post['settings_titlefontsize'])?$post['settings_titlefontsize']:$default_titlefontsize);

					$settings_electronicbulletindaily = array();

					if(!empty($post['settings_electronicbulletindailymsg'])&&is_array($post['settings_electronicbulletindailymsg'])) {
						foreach($post['settings_electronicbulletindailymsg'] as $k=>$v) {

							//pre(array('settings_electronicbulletindailymsg'=>$post['settings_electronicbulletindailymsg']));

							if(!empty($post['settings_electronicbulletindailymsg'][$k])&&!empty($post['settings_electronicbulletindailydate'][$k])) {
								$dt = date2timestamp($post['settings_electronicbulletindailydate'][$k]);
								$settings_electronicbulletindaily[$dt] = array(
									'unixdate'=>$dt,
									'date'=>$post['settings_electronicbulletindailydate'][$k],
									'msg'=>$post['settings_electronicbulletindailymsg'][$k]
								);
							}
						}
					}

					if(!empty($settings_electronicbulletindaily)) {

						ksort($settings_electronicbulletindaily);

						$tsettings_electronicbulletindaily = array();

						foreach($settings_electronicbulletindaily as $k=>$v) {
							$tsettings_electronicbulletindaily[] = $v;
						}

						//pre(array('$tsettings_electronicbulletindaily'=>$tsettings_electronicbulletindaily));

						$settings_electronicbulletindaily = base64_encode(serialize($tsettings_electronicbulletindaily));

						//pre(array('$settings_electronicbulletindaily'=>$settings_electronicbulletindaily));
					} else {
						$settings_electronicbulletindaily = base64_encode(serialize(array()));
					}

					/*$settings_electronicbulletindailydate = array();

					$tsettings_electronicbulletindailydate = !empty($post['settings_electronicbulletindailydate'])&&is_array($post['settings_electronicbulletindailydate'])?base64_encode(serialize($post['settings_electronicbulletindailydate'])):base64_encode(serialize(array()));

					setSetting('$SETTINGS_ELECTRONICBULLETINDAILYDATE',$tsettings_electronicbulletindailydate);

					$tsettings_electronicbulletindailymsg = !empty($post['settings_electronicbulletindailymsg'])&&is_array($post['settings_electronicbulletindailymsg'])?base64_encode(serialize($post['settings_electronicbulletindailymsg'])):base64_encode(serialize(array()));

					setSetting('$SETTINGS_ELECTRONICBULLETINDAILYMSG',$tsettings_electronicbulletindailymsg);*/

					setSetting('$SETTINGS_ELECTRONICBULLETINDAILY',$settings_electronicbulletindaily);

					json_encode_return($retval);
					die;
				}

				$params['hello'] = 'Hello, Sherwin!';

				$newcolumnoffset = 50;

				$position = 'right';

				$params['tbElectronicBulletin'] = array();
				$params['tbLoginNotification'] = array();
				$params['tbNotifications'] = array();
				$params['tbGeneral'] = array();
				$params['tbThreshold'] = array();
				$params['tbServer'] = array();
				$params['tbUHFRFID'] = array();
				$params['tbLicense'] = array();

				$params['tbSchoolYear'][] = array(
					'type' => 'input',
					'label' => 'SCHOOL YEAR',
					'labelWidth' => 120,
					'name' => 'settings_schoolyear',
					'readonly' => $readonly,
					'inputMask' => array('mask'=>'2099-2099'),
					//'required' => !$readonly,
					'value' => !empty($settings_schoolyear) ? $settings_schoolyear : '',
				);

				$params['tbElectronicBulletin'][] = array(
					'type' => 'input',
					'label' => 'DEFAULT BULLETIN',
					'inputWidth' => 950,
					//'rows' => 1,
					'labelWidth' => 150,
					'name' => 'settings_electronicbulletin',
					'readonly' => $readonly,
					//'required' => !$readonly,
					'value' => !empty($settings_electronicbulletin) ? $settings_electronicbulletin : '',
				);

				for($i=0;$i<31;$i++) {
					$block = array();

					if($readonly) {
						$block[] = array(
							'type' => 'input',
							'label' => 'DAILY BULLETIN #'.($i+1),
							'inputWidth' => 100,
							'labelWidth' => 150,
							'name' => 'settings_electronicbulletindailydate['.$i.']',
							'readonly' => $readonly,
							'value' => !empty($settings_electronicbulletindaily[$i]['date']) ? $settings_electronicbulletindaily[$i]['date'] : '',
						);
					} else {
						$block[] = array(
							'type' => 'calendar',
							'label' => 'DAILY BULLETIN #'.($i+1),
							'inputWidth' => 100,
							'labelWidth' => 150,
							'name' => 'settings_electronicbulletindailydate['.$i.']',
							'readonly' => $readonly,
							'calendarPosition' => 'right',
							'dateFormat' => '%m-%d-%Y',
							//'inputMask' => array('alias'=>'mm/dd/yyyy','prefix'=>'','autoUnmask'=>true),
							//'inputMask' => array('mask'=>'99-99-9999','prefix'=>'','autoUnmask'=>true),
							'value' => !empty($settings_electronicbulletindaily[$i]['date']) ? $settings_electronicbulletindaily[$i]['date'] : '',
						);
					}

					$block[] = array(
						'type' => 'newcolumn',
						'offset' => 5,
					);

					$block[] = array(
						'type' => 'input',
						//'label' => 'DAILY BULLETIN',
						'inputWidth' => 840,
						//'rows' => 5,
						//'labelWidth' => 250,
						'name' => 'settings_electronicbulletindailymsg['.$i.']',
						'readonly' => $readonly,
						//'required' => !$readonly,
						'value' => !empty($settings_electronicbulletindaily[$i]['msg']) ? $settings_electronicbulletindaily[$i]['msg'] : '',
					);

					$params['tbElectronicBulletin'][] = array(
						'type' => 'block',
						'width' => 1150,
						'blockOffset' => 0,
						'offsetTop' => 5,
						'list' => $block,
					);
				}

				$params['tbLoginNotification'][] = array(
					'type' => 'input',
					'label' => 'TYPE',
					//'inputWidth' => 500,
					//'rows' => 5,
					'labelWidth' => 200,
					'name' => 'settings_loginnotificationtype',
					'readonly' => true,
					//'required' => !$readonly,
					'value' => 'SEND SMS USING MODEM',
				);

				$block = array();

				$block[] = array(
					'type' => 'input',
					'label' => 'SCHOOL ADMINISTRATOR',
					//'inputWidth' => 500,
					//'rows' => 5,
					'labelWidth' => 200,
					'name' => 'settings_loginnotificationschooladmin',
					'readonly' => $readonly,
					//'inputMask' => array('mask'=>'9','placeholder'=>'_','repeat'=>11),
					//'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_loginnotificationschooladmin) ? $settings_loginnotificationschooladmin : '',
				);

				$block[] = array(
					'type' => 'newcolumn',
					'offset' => 5,
				);

				$block[] = array(
					'type' => 'checkbox',
					'label' => 'SEND SMS',
					'labelWidth' => 360,
					'name' => 'settings_loginnotificationschooladminsendsms',
					'readonly' => $readonly,
					'checked' => !empty($settings_loginnotificationschooladminsendsms) ? true : false,
					'position' => 'label-right',
				);

				$params['tbLoginNotification'][] = array(
					'type' => 'block',
					'width' => 1000,
					'blockOffset' => 0,
					'offsetTop' => 5,
					'list' => $block,
				);

				$block = array();

				$block[] = array(
					'type' => 'input',
					'label' => 'OST RELATIONSHIP MANAGER',
					//'inputWidth' => 500,
					//'rows' => 5,
					'labelWidth' => 200,
					'name' => 'settings_loginnotificationostrelationshipmanager',
					'readonly' => $readonly,
					//'inputMask' => array('mask'=>'9','placeholder'=>'_','repeat'=>11),
					//'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_loginnotificationostrelationshipmanager) ? $settings_loginnotificationostrelationshipmanager : '',
				);

				$block[] = array(
					'type' => 'newcolumn',
					'offset' => 5,
				);

				$block[] = array(
					'type' => 'checkbox',
					'label' => 'SEND SMS',
					'labelWidth' => 360,
					'name' => 'settings_loginnotificationostrelationshipmanagersendsms',
					'readonly' => $readonly,
					'checked' => !empty($settings_loginnotificationostrelationshipmanagersendsms) ? true : false,
					'position' => 'label-right',
				);

				$params['tbLoginNotification'][] = array(
					'type' => 'block',
					'width' => 1000,
					'blockOffset' => 0,
					'offsetTop' => 5,
					'list' => $block,
				);

				$params['tbNotifications'][] = array(
					'type' => 'input',
					'label' => 'TIME-IN NOTIFICATION',
					'inputWidth' => 900,
					//'rows' => 2,
					'labelWidth' => 200,
					'name' => 'settings_timeinnotification',
					'readonly' => $readonly,
					//'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_timeinnotification) ? $settings_timeinnotification : '',
				);

				$params['tbNotifications'][] = array(
					'type' => 'input',
					'label' => 'TIME-OUT NOTIFICATION',
					'inputWidth' => 900,
					//'rows' => 2,
					'labelWidth' => 200,
					'name' => 'settings_timeoutnotification',
					'readonly' => $readonly,
					//'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_timeoutnotification) ? $settings_timeoutnotification : '',
				);

				$params['tbNotifications'][] = array(
					'type' => 'input',
					'label' => 'LATE NOTIFICATION',
					'inputWidth' => 900,
					//'rows' => 2,
					'labelWidth' => 200,
					'name' => 'settings_latenotification',
					'readonly' => $readonly,
					//'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_latenotification) ? $settings_latenotification : '',
				);

				$params['tbNotifications'][] = array(
					'type' => 'input',
					'label' => 'ABSENT NOTIFICATION',
					'inputWidth' => 900,
					//'rows' => 2,
					'labelWidth' => 200,
					'name' => 'settings_absentnotification',
					'readonly' => $readonly,
					//'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_absentnotification) ? $settings_absentnotification : '',
				);

				$params['tbNotifications'][] = array(
					'type' => 'checkbox',
					'label' => 'SEND TIME-IN NOTIFICATION',
					'labelWidth' => 360,
					'name' => 'settings_sendtimeinnotification',
					'readonly' => $readonly,
					'checked' => !empty($settings_sendtimeinnotification) ? true : false,
					'position' => 'label-right',
				);

				$params['tbNotifications'][] = array(
					'type' => 'checkbox',
					'label' => 'SEND TIME-OUT NOTIFICATION',
					'labelWidth' => 360,
					'name' => 'settings_sendtimeoutnotification',
					'readonly' => $readonly,
					'checked' => !empty($settings_sendtimeoutnotification) ? true : false,
					'position' => 'label-right',
				);

				$params['tbNotifications'][] = array(
					'type' => 'checkbox',
					'label' => 'SEND LATE NOTIFICATION',
					'labelWidth' => 360,
					'name' => 'settings_sendlatenotification',
					'readonly' => $readonly,
					'checked' => !empty($settings_sendlatenotification) ? true : false,
					'position' => 'label-right',
				);

				$params['tbNotifications'][] = array(
					'type' => 'checkbox',
					'label' => 'SEND ABSENT NOTIFICATION',
					'labelWidth' => 360,
					'name' => 'settings_sendabsentnotification',
					'readonly' => $readonly,
					'checked' => !empty($settings_sendabsentnotification) ? true : false,
					'position' => 'label-right',
				);

				$params['tbNotifications'][] = array(
					'type' => 'checkbox',
					'label' => 'SEND PUSH NOTIFICATION',
					'labelWidth' => 360,
					'name' => 'settings_sendpushnotification',
					'readonly' => $readonly,
					'checked' => !empty($settings_sendpushnotification) ? true : false,
					'position' => 'label-right',
				);

				$params['tbNotifications'][] = array(
					'type' => 'checkbox',
					'label' => 'SEND SMS NOTIFICATION',
					'labelWidth' => 360,
					'name' => 'settings_sendsmsnotification',
					'readonly' => $readonly,
					'checked' => !empty($settings_sendsmsnotification) ? true : false,
					'position' => 'label-right',
				);

				$params['tbGeneral'][] = array(
					'type' => 'input',
					'label' => 'RFID TAP INTERVAL (minutes)',
					//'inputWidth' => 500,
					//'rows' => 5,
					'labelWidth' => 220,
					'name' => 'settings_rfidinterval',
					'readonly' => $readonly,
					'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_rfidinterval) ? $settings_rfidinterval : '5',
				);

				$block = array();

				$block[] = array(
					'type' => 'input',
					'label' => 'SHOW ADS AFTER IDLE (minutes)',
					//'inputWidth' => 500,
					//'rows' => 5,
					'labelWidth' => 220,
					'name' => 'settings_showadsinterval',
					'readonly' => $readonly,
					'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_showadsinterval) ? $settings_showadsinterval : '30',
				);

				$block[] = array(
					'type' => 'newcolumn',
					'offset' => 5,
				);

				$block[] = array(
					'type' => 'checkbox',
					'label' => 'ENABLE',
					'labelWidth' => 360,
					'name' => 'settings_showadsintervalenable',
					'readonly' => $readonly,
					'checked' => !empty($settings_showadsintervalenable) ? true : false,
					'position' => 'label-right',
				);

				$params['tbGeneral'][] = array(
					'type' => 'block',
					'width' => 1000,
					'blockOffset' => 0,
					'offsetTop' => 5,
					'list' => $block,
				);

				$block = array();

				$block[] = array(
					'type' => 'input',
					'label' => 'SERVER SHUTDOWN RFID',
					//'inputWidth' => 500,
					//'rows' => 5,
					'labelWidth' => 220,
					'name' => 'settings_servershutdownrfid',
					'readonly' => $readonly,
					'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_servershutdownrfid) ? $settings_servershutdownrfid : '',
				);

				$block[] = array(
					'type' => 'newcolumn',
					'offset' => 5,
				);

				$block[] = array(
					'type' => 'checkbox',
					'label' => 'ENABLE',
					'labelWidth' => 360,
					'name' => 'settings_servershutdownrfidenable',
					'readonly' => $readonly,
					'checked' => !empty($settings_servershutdownrfidenable) ? true : false,
					'position' => 'label-right',
				);

				$params['tbGeneral'][] = array(
					'type' => 'block',
					'width' => 1000,
					'blockOffset' => 0,
					'offsetTop' => 5,
					'list' => $block,
				);

				$params['tbGeneral'][] = array(
					'type' => 'input',
					'label' => 'TIME-IN MESSAGE',
					'inputWidth' => 900,
					//'rows' => 2,
					'labelWidth' => 220,
					'name' => 'settings_timeinmessage',
					'readonly' => $readonly,
					//'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_timeinmessage) ? $settings_timeinmessage : '',
				);

				$params['tbGeneral'][] = array(
					'type' => 'input',
					'label' => 'TIME-OUT MESSAGE',
					'inputWidth' => 900,
					//'rows' => 2,
					'labelWidth' => 220,
					'name' => 'settings_timeoutmessage',
					'readonly' => $readonly,
					//'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_timeoutmessage) ? $settings_timeoutmessage : '',
				);

				$params['tbGeneral'][] = array(
					'type' => 'input',
					'label' => 'LATE MESSAGE',
					'inputWidth' => 900,
					//'rows' => 2,
					'labelWidth' => 220,
					'name' => 'settings_latemessage',
					'readonly' => $readonly,
					//'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_latemessage) ? $settings_latemessage : '',
				);

				$params['tbGeneral'][] = array(
					'type' => 'checkbox',
					'label' => 'HIDE DB',
					'labelWidth' => 360,
					'name' => 'settings_hidedb',
					'readonly' => $readonly,
					'checked' => !empty($settings_hidedb) ? true : false,
					'position' => 'label-right',
				);

				$params['tbGeneral'][] = array(
					'type' => 'checkbox',
					'label' => 'AUTO DETECT FACE',
					'labelWidth' => 360,
					'name' => 'settings_autodetectface',
					'readonly' => $readonly,
					'checked' => !empty($settings_autodetectface) ? true : false,
					'position' => 'label-right',
				);

				$block = array();

				$block[] = array(
					'type' => 'checkbox',
					'label' => 'BRIDGE TO ADMIN',
					'labelWidth' => 150,
					'name' => 'settings_bridgetoadmin',
					'readonly' => $readonly,
					'checked' => !empty($settings_bridgetoadmin) ? true : false,
					'position' => 'label-right',
				);

				$block[] = array(
					'type' => 'newcolumn',
					'offset' => 5,
				);

				$block[] = array(
					'type' => 'input',
					'label' => 'IP',
					'labelWidth' => 40,
					'name' => 'settings_bridgetoadminip',
					'readonly' => $readonly,
					'value' => !empty($settings_bridgetoadminip) ? $settings_bridgetoadminip : '',
				);

				$params['tbGeneral'][] = array(
					'type' => 'block',
					'width' => 1000,
					'blockOffset' => 0,
					'offsetTop' => 5,
					'list' => $block,
				);

				$params['tbGeneral'][] = array(
					'type' => 'input',
					'label' => 'TITLE FONT SIZE',
					'inputWidth' => 100,
					//'rows' => 2,
					'labelWidth' => 220,
					'name' => 'settings_titlefontsize',
					'readonly' => $readonly,
					'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_titlefontsize) ? $settings_titlefontsize : '30',
				);

				$params['tbThreshold'][] = array(
					'type' => 'input',
					'label' => 'TARDINESS GRACE PERIOD (MINUTE)',
					'labelWidth' => 250,
					'inputWidth' => 100,
					'name' => 'settings_tardinessgraceperiodminute',
					'readonly' => $readonly,
					'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_tardinessgraceperiodminute) ? $settings_tardinessgraceperiodminute : '',
				);

				$block = array();

				$block[] = array(
					'type' => 'input',
					'label' => 'ABSENT GRACE PERIOD (MINUTE)',
					'labelWidth' => 250,
					'inputWidth' => 100,
					'name' => 'settings_absentgraceperiodminute1',
					'readonly' => $readonly,
					'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_absentgraceperiodminute1) ? $settings_absentgraceperiodminute1 : '',
				);

				$block[] = array(
					'type' => 'newcolumn',
					'offset' => 5,
				);

				$block[] = array(
					'type' => 'input',
					//'label' => 'ABSENT GRACE PERIOD (MINUTE)',
					//'labelWidth' => 250,
					'inputWidth' => 100,
					'name' => 'settings_absentgraceperiodminute2',
					'readonly' => $readonly,
					'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_absentgraceperiodminute2) ? $settings_absentgraceperiodminute2 : '',
				);

				$block[] = array(
					'type' => 'newcolumn',
					'offset' => 5,
				);

				$block[] = array(
					'type' => 'input',
					//'label' => 'ABSENT GRACE PERIOD (MINUTE)',
					//'labelWidth' => 250,
					'inputWidth' => 100,
					'name' => 'settings_absentgraceperiodminute3',
					'readonly' => $readonly,
					'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_absentgraceperiodminute3) ? $settings_absentgraceperiodminute3 : '',
				);

				$params['tbThreshold'][] = array(
					'type' => 'block',
					'width' => 1150,
					'blockOffset' => 0,
					'offsetTop' => 5,
					'list' => $block,
				);

				$params['tbServer'][] = array(
					'type' => 'checkbox',
					'label' => 'SYNC ALL CONTACTS TO NOTIFICATION SERVER',
					'labelWidth' => 360,
					'name' => 'settings_synctoserver',
					'readonly' => $readonly,
					'checked' => !empty($settings_synctoserver) ? true : false,
					'position' => 'label-right',
				);

				$params['tbUHFRFID'][] = array(
					'type' => 'checkbox',
					'label' => 'UHF RFID READER',
					'labelWidth' => 360,
					'name' => 'settings_useuhfrfidreader',
					'readonly' => $readonly,
					'checked' => !empty($settings_useuhfrfidreader) ? true : false,
					'position' => 'label-right',
				);

				$params['tbUHFRFID'][] = array(
					'type' => 'checkbox',
					'label' => 'INFRARED BEAM',
					'labelWidth' => 360,
					'name' => 'settings_useinfraredbeam',
					'readonly' => $readonly,
					'checked' => !empty($settings_useinfraredbeam) ? true : false,
					'position' => 'label-right',
				);

				$params['tbUHFRFID'][] = array(
					'type' => 'input',
					'label' => 'UHF RFID READ INTERVAL (seconds)',
					//'inputWidth' => 500,
					//'rows' => 5,
					'labelWidth' => 250,
					'name' => 'settings_uhfrfidreadinterval',
					'readonly' => $readonly,
					'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_uhfrfidreadinterval) ? $settings_uhfrfidreadinterval : '60',
				);

				$params['tbLicense'][] = array(
					'type' => 'input',
					'label' => 'LICENSE KEY',
					'inputWidth' => 800,
					'rows' => 9,
					'labelWidth' => 100,
					'name' => 'settings_licensekey',
					'readonly' => $readonly,
					//'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_licensekey) ? $settings_licensekey : '',
				);

				if(!empty(($license=readLicense()))) {
					//pre(array('$license'=>$license));
					$settings_licenseinfo = ''; //prebuf($license);

					if(!empty($license['sc'])) {
						$settings_licenseinfo .= 'LICENSED TO '.$license['sc']."\n";
						$settings_licenseinfo .= 'DATE: '.$license['dt']."\n";
						$settings_licenseinfo .= 'EXPIRATION: '.$license['de']."\n";
						$settings_licenseinfo .= 'TOTAL DAYS: '.$license['dd']."\n";
						$settings_licenseinfo .= 'TOTAL STUDENTS: '.$license['ns']."\n";
					}

					/*if(!checkLicense()) {
						$settings_licenseinfo .= 'LICENSED EXPIRED. LICENSED EXPIRED. LICENSED EXPIRED.'."\n";
						$settings_licenseinfo .= 'PLEASE CONTACT SUPPORT.'."\n";
					}*/
				} else {
					$settings_licenseinfo = 'UNLICENSED VERSION. UNAUTHORIZED USE IS PROHIBITED.';
				}

				$params['tbLicense'][] = array(
					'type' => 'input',
					'label' => 'LICENSE INFO',
					'inputWidth' => 800,
					'rows' => 9,
					'labelWidth' => 100,
					'name' => 'settings_licenseinfo',
					'readonly' => $readonly,
					//'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_licenseinfo) ? $settings_licenseinfo : '',
				);

				$templatefile = $this->templatefile($routerid,$formid);

				//pre(array($routerid,$formid,$params,$templatefile));

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}
			}

			return false;

		} // _form_group

		function router() {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			$retflag=false;

			header_json();

			if(!empty($this->post['routerid'])&&!empty($this->post['action'])) {

				if( $this->post['action']=='toolbar' && !empty($this->post['toolbarid']) ) {

					if(!empty($toolbar = $this->_toolbar($this->post['routerid'], $this->post['toolbarid']))) {
						$jsonval = json_encode($toolbar,JSON_OBJECT_AS_ARRAY);
						if($retflag===false) {
							die($jsonval);
						} else
						if($retflag==1) {
							return $toolbar;
						} else
						if($retflag==2) {
							return $jsonval;
						}
					}
				} else
				if( $this->post['action']=='form' && !empty($this->post['buttonid']) ) {

					if(!empty($form = $this->_form($this->post['routerid'], $this->post['buttonid']))) {

						$jsontoolbar = $this->_toolbar($this->post['routerid'], $this->post['buttonid']);

						$formid = $this->post['buttonid'];

						if(!empty($this->post['tabid'])) {
							$formid = $this->post['tabid'];
						}

						$formval = sha1($this->post['routerid'].$form.$formid);

						$sform = str_replace('%formval%',$formval,$form);

						$sform = '<div class="srt_cell_cont_tabbar">'.$sform.'</div>';

						$retval = array('html'=>$sform,'formval'=>$formval);

						$_SESSION['FORMS'][$formval] = array('since'=>time(),'formid'=>(!empty($this->post['tabid']) ? $this->post['tabid'] : $this->post['buttonid']),'routerid'=>$this->post['routerid']);

						//$prebuf = prebuf($_SESSION);

						//$retval['html'] .= '<br /><br />' . $prebuf;;

						if(!empty($jsontoolbar)) {
							$retval['toolbar'] = $jsontoolbar;
						}

						$jsonval = json_encode($retval,JSON_OBJECT_AS_ARRAY);

						if($retflag===false) {
							die($jsonval);
						} else
						if($retflag==1) {
							return $form;
						} else
						if($retflag==2) {
							return $jsonval;
						}
					}

				} else
				if( $this->post['action']=='form' && !empty($this->post['formid']) ) {

					$formval = sha1($this->post['routerid'].$this->post['formid'].time());

					$this->vars['post']['formval'] = $this->post['formval'] = $formval;

					$form = $this->_form($this->post['routerid'], $this->post['formid']);

					//pre($this->post); die;

					$jsontoolbar = $this->_toolbar($this->post['routerid'], $this->post['formid']);

					$jsonlayout = $this->_layout($this->post['routerid'], $this->post['formid']);

					$jsonxml = $this->_xml($this->post['routerid'], $this->post['formid']);

					if(empty($form)&&empty($jsontoolbar)&&empty($jsonlayout)) return false;

					$formid = $this->post['formid'];

					if(!empty($this->post['tabid'])) {
						$formid = $this->post['tabid'];
					}

					if(!empty($form)) {
						//$formval = sha1($this->post['routerid'].$form.$formid);

						$sform = str_replace('%formval%',$formval,$form);

						$sform = '<div class="srt_cell_cont_tabbar">'.$sform.'</div>';

						$retval = array('html'=>$sform,'formval'=>$formval);

						$_SESSION['FORMS'][$formval] = array('since'=>time(),'formid'=>(!empty($this->post['tabid']) ? $this->post['tabid'] : $this->post['formid']),'routerid'=>$this->post['routerid']);
					} else {
						$retval = array();
					}

					if(!empty($jsontoolbar)) {
						$retval['toolbar'] = $jsontoolbar;
					}

					if(!empty($jsonxml)) {
						$retval['xml'] = $jsonxml;
					}

					if(!empty($jsonlayout)) {

						$formval = sha1($this->post['routerid'].json_encode($jsonlayout).$formid);

						$_SESSION['FORMS'][$formval] = array('since'=>time(),'formid'=>(!empty($this->post['tabid']) ? $this->post['tabid'] : $this->post['formid']),'routerid'=>$this->post['routerid']);

						$retval['formval'] = $formval;
						$retval['layout'] = $jsonlayout;
					}

					$jsonval = json_encode($retval,JSON_OBJECT_AS_ARRAY);

					if($retflag===false) {
						die($jsonval);
					} else
					if($retflag==1) {
						return $form;
					} else
					if($retflag==2) {
						return $jsonval;
					}
				} else
				if( $this->post['action']=='formonly' && !empty($this->post['formid']) ) {

					//pre(array('post'=>$this->post));

					$toolbar = false;

					if(!empty($this->post['wid'])) {
						if(!empty($toolbar = $this->_toolbar($this->post['routerid'], $this->post['module']))) {
						}
					}

					$form = $this->_form($this->post['routerid'], $this->post['formid']);

					$jsonxml = $this->_xml($this->post['routerid'], $this->post['formid']);

					if(!empty($this->post['formval'])) {
						$form = str_replace('%formval%',$this->post['formval'],$form);
					}

					$retval = array('html'=>$form);

					if(!empty($toolbar)) {
						$retval['toolbar'] = $toolbar;
					}

					if(!empty($jsonxml)) {
						$retval['xml'] = $jsonxml;
					}

					//pre(array('$retval'=>$retval));

					$jsonval = json_encode($retval,JSON_OBJECT_AS_ARRAY);

					if($retflag===false) {
						die($jsonval);
					} else
					if($retflag==1) {
						return $form;
					} else
					if($retflag==2) {
						return $jsonval;
					}
				} else
				if( $this->post['action']=='grid' && !empty($this->post['formid']) && !empty($this->post['table']) ) {

					$retval = array();

					//pre(array($this->post));

					if($this->post['table']=='modemcommands') {
						if(!($result = $appdb->query("select * from tbl_modemcommands order by modemcommands_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}
						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['modemcommands_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['modemcommands_id'],'data'=>array(0,$v['modemcommands_id'],$v['modemcommands_name'],$v['modemcommands_desc']));
							}

							$retval = array('rows'=>$rows);
						}

					}

					$jsonval = json_encode($retval,JSON_OBJECT_AS_ARRAY);

					if($retflag===false) {
						die($jsonval);
					} else
					if($retflag==1) {
						return $form;
					} else
					if($retflag==2) {
						return $jsonval;
					}

				}
			}

			return false;
		} // router($vars=false,$retflag=false)

	}

	$appappsetting = new APP_app_setting;
}

# eof modules/app.user
