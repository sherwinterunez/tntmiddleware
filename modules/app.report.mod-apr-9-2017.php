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

if(!class_exists('APP_app_report')) {

	class APP_app_report extends APP_Base_Ajax {

		var $desc = 'report';

		var $pathid = 'report';
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

			$appaccess->rules($this->desc,'Report Module');
			$appaccess->rules($this->desc,'Report Module New');
			$appaccess->rules($this->desc,'Report Module Edit');
			$appaccess->rules($this->desc,'Report Module Delete');

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

		function _form_report($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				//pre(array($routerid,$formid));

				$post = $this->vars['post'];

				$params = array();

				$readonly = true;

				if(!empty($post['method'])&&($post['method']=='reportedit')) {
					$readonly = false;
				}

				if(!empty($post['method'])&&$post['method']=='reportsave') {
					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Report successfully saved!';
					$retval['post'] = $post;

					//pre(array('$post',$post));

					json_encode_return($retval);
					die;
				}

				$params['hello'] = 'Hello, Sherwin!';

				$newcolumnoffset = 50;

				$position = 'right';

				$params['tbElectronicBulletin'] = array();
				$params['tbLoginNotification'] = array();

				$params['tbElectronicBulletin'][] = array(
					'type' => 'input',
					'label' => 'TARDINESS GRACE PERIOD (MINUTE)',
					'labelWidth' => 250,
					'name' => 'setting_tardinessgraceperiod',
					'readonly' => $readonly,
					//'required' => !$readonly,
					'value' => !empty($params['settinginfo']['setting_tardinessgraceperiod']) ? $params['settinginfo']['group_tardinessgraceperiod'] : '',
				);

				$templatefile = $this->templatefile($routerid,$formid);

				//pre(array($routerid,$formid,$params,$templatefile));

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}
			}

			return false;

		} // _form_group

		function _form_reportmaindailyabsent($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				//pre(array($routerid,$formid));

				$post = $this->vars['post'];

				$params = array();

				$readonly = true;

				if(!empty($post['method'])&&($post['method']=='reportedit')) {
					$readonly = false;
				}

				if(!empty($post['method'])&&$post['method']=='reportsave') {
					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Report successfully saved!';
					$retval['post'] = $post;

					//pre(array('$post',$post));

					json_encode_return($retval);
					die;
				}

				$params['hello'] = 'Hello, Sherwin!';

				$newcolumnoffset = 50;

				$position = 'right';

				$params['tbDetails'] = array();
				//$params['tbLoginNotification'] = array();

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => 'OBIS MONTESSORI',
					'labelWidth' => 250,
					'className' => 'schoolName_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => 'Period: 07 April 2017',
					'labelWidth' => 250,
					'className' => 'period_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => 'DAILY ABSENT REPORT',
					'labelWidth' => 250,
					'className' => 'dailyabsentreport_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => 'NURSERY',
					'labelWidth' => 250,
					'className' => 'yearlevel_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => 'ST. JOHN',
					'labelWidth' => 250,
					'offsetLeft' => 25,
					'className' => 'section_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => '1. RODRIGO DUTERTE',
					'labelWidth' => 250,
					'offsetLeft' => 50,
					'className' => 'studentName_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => 'ST. MICHAEL',
					'labelWidth' => 250,
					'offsetLeft' => 25,
					'className' => 'section_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => '1. SHERWIN TERUNEZ',
					'labelWidth' => 250,
					'offsetLeft' => 50,
					'className' => 'studentName_'.$post['formval'],
				);

				/*$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'TARDINESS GRACE PERIOD (MINUTE)',
					'labelWidth' => 250,
					'name' => 'setting_tardinessgraceperiod',
					'readonly' => $readonly,
					//'required' => !$readonly,
					'value' => !empty($params['settinginfo']['setting_tardinessgraceperiod']) ? $params['settinginfo']['group_tardinessgraceperiod'] : '',
				);*/

				$templatefile = $this->templatefile($routerid,$formid);

				//pre(array($routerid,$formid,$params,$templatefile));

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}
			}

			return false;

		} // _form_reportmaindailyabsent

		function _form_reportmaindailytardy($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				//pre(array($routerid,$formid));

				$post = $this->vars['post'];

				$params = array();

				$readonly = true;

				if(!empty($post['method'])&&($post['method']=='reportedit')) {
					$readonly = false;
				}

				if(!empty($post['method'])&&$post['method']=='reportsave') {
					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Report successfully saved!';
					$retval['post'] = $post;

					//pre(array('$post',$post));

					json_encode_return($retval);
					die;
				}

				$params['hello'] = 'Hello, Sherwin!';

				$newcolumnoffset = 50;

				$position = 'right';

				$params['tbDetails'] = array();
				//$params['tbLoginNotification'] = array();

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => 'OBIS MONTESSORI',
					'labelWidth' => 250,
					'className' => 'schoolName_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => 'Period: 07 April 2017',
					'labelWidth' => 250,
					'className' => 'period_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => 'DAILY TARDY REPORT',
					'labelWidth' => 250,
					'className' => 'dailytardyreport_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => 'NURSERY',
					'labelWidth' => 250,
					'className' => 'yearlevel_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => 'ST. JOHN',
					'labelWidth' => 250,
					'offsetLeft' => 25,
					'className' => 'section_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => '1. RODRIGO DUTERTE',
					'labelWidth' => 250,
					'offsetLeft' => 50,
					'className' => 'studentName_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => 'ST. MICHAEL',
					'labelWidth' => 250,
					'offsetLeft' => 25,
					'className' => 'section_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => '1. SHERWIN TERUNEZ',
					'labelWidth' => 250,
					'offsetLeft' => 50,
					'className' => 'studentName_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => '2. CELESTE TERUNEZ',
					'labelWidth' => 250,
					'offsetLeft' => 50,
					'className' => 'studentName_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => '3. JOSHUA DANIEL TERUNEZ',
					'labelWidth' => 250,
					'offsetLeft' => 50,
					'className' => 'studentName_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => 'ST. PAUL',
					'labelWidth' => 250,
					'offsetLeft' => 25,
					'className' => 'section_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => '1. SHERWIN PADILLA',
					'labelWidth' => 250,
					'offsetLeft' => 50,
					'className' => 'studentName_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => '2. CELESTE PADILLA',
					'labelWidth' => 250,
					'offsetLeft' => 50,
					'className' => 'studentName_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => '3. JOSHUA DANIEL PADILLA',
					'labelWidth' => 250,
					'offsetLeft' => 50,
					'className' => 'studentName_'.$post['formval'],
				);

				/*$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'TARDINESS GRACE PERIOD (MINUTE)',
					'labelWidth' => 250,
					'name' => 'setting_tardinessgraceperiod',
					'readonly' => $readonly,
					//'required' => !$readonly,
					'value' => !empty($params['settinginfo']['setting_tardinessgraceperiod']) ? $params['settinginfo']['group_tardinessgraceperiod'] : '',
				);*/

				$templatefile = $this->templatefile($routerid,$formid);

				//pre(array($routerid,$formid,$params,$templatefile));

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}
			}

			return false;

		} // _form_reportmaindailytardy

		function _form_reportmainindividualattendance($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				//pre(array($routerid,$formid));

				$post = $this->vars['post'];

				$params = array();

				$readonly = true;

				if(!empty($post['method'])&&($post['method']=='reportedit')) {
					$readonly = false;
				}

				if(!empty($post['method'])&&$post['method']=='reportsave') {
					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Report successfully saved!';
					$retval['post'] = $post;

					//pre(array('$post',$post));

					json_encode_return($retval);
					die;
				}

				$params['hello'] = 'Hello, Sherwin!';

				$newcolumnoffset = 50;

				$position = 'right';

				$params['tbDetails'] = array();
				//$params['tbLoginNotification'] = array();

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => 'OBIS MONTESSORI',
					'labelWidth' => 250,
					'className' => 'schoolName_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => 'Period: 07 April 2017',
					'labelWidth' => 250,
					'className' => 'period_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => 'DAILY TARDY REPORT',
					'labelWidth' => 250,
					'className' => 'dailytardyreport_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => 'NURSERY',
					'labelWidth' => 250,
					'className' => 'yearlevel_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => 'ST. JOHN',
					'labelWidth' => 250,
					'offsetLeft' => 25,
					'className' => 'section_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => '1. RODRIGO DUTERTE',
					'labelWidth' => 250,
					'offsetLeft' => 50,
					'className' => 'studentName_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => 'ST. MICHAEL',
					'labelWidth' => 250,
					'offsetLeft' => 25,
					'className' => 'section_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => '1. SHERWIN TERUNEZ',
					'labelWidth' => 250,
					'offsetLeft' => 50,
					'className' => 'studentName_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => '2. CELESTE TERUNEZ',
					'labelWidth' => 250,
					'offsetLeft' => 50,
					'className' => 'studentName_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => '3. JOSHUA DANIEL TERUNEZ',
					'labelWidth' => 250,
					'offsetLeft' => 50,
					'className' => 'studentName_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => 'ST. PAUL',
					'labelWidth' => 250,
					'offsetLeft' => 25,
					'className' => 'section_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => '1. SHERWIN PADILLA',
					'labelWidth' => 250,
					'offsetLeft' => 50,
					'className' => 'studentName_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => '2. CELESTE PADILLA',
					'labelWidth' => 250,
					'offsetLeft' => 50,
					'className' => 'studentName_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'label',
					'label' => '3. JOSHUA DANIEL PADILLA',
					'labelWidth' => 250,
					'offsetLeft' => 50,
					'className' => 'studentName_'.$post['formval'],
				);

				/*$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'TARDINESS GRACE PERIOD (MINUTE)',
					'labelWidth' => 250,
					'name' => 'setting_tardinessgraceperiod',
					'readonly' => $readonly,
					//'required' => !$readonly,
					'value' => !empty($params['settinginfo']['setting_tardinessgraceperiod']) ? $params['settinginfo']['group_tardinessgraceperiod'] : '',
				);*/

				$templatefile = $this->templatefile($routerid,$formid);

				//pre(array($routerid,$formid,$params,$templatefile));

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}
			}

			return false;

		} // _form_reportmainindividualattendance

		function _form_reportmainmonthlyattendance($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				//pre(array($routerid,$formid));

				$post = $this->vars['post'];

				$params = array();

				$readonly = true;

				if(!empty($post['method'])&&($post['method']=='reportedit')) {
					$readonly = false;
				}

				if(!empty($post['method'])&&$post['method']=='reportsave') {
					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Report successfully saved!';
					$retval['post'] = $post;

					//pre(array('$post',$post));

					json_encode_return($retval);
					die;
				} else
				if(!empty($post['method'])&&$post['method']=='generatereport') {

					if(!empty($post['section'])) {
					} else
					if(!empty($post['yearlevel'])) {
					} else {

						$params['tbReports'][] = array(
							'type' => 'label',
							'label' => 'Please specify parameters to generate report.',
							'labelWidth' => 500,
						);

						json_encode_return($params);
						die;
					}

					if(!empty($post['datefrom'])&&!empty($post['dateto'])) {
					} else {

						$params['tbReports'][] = array(
							'type' => 'label',
							'label' => 'Please select date/period to generate report.',
							'labelWidth' => 500,
						);

						json_encode_return($params);
						die;
					}

					$from = date2timestamp($post['datefrom']." 00:00:00",'m/d/Y H:i:s');
					$to = date2timestamp($post['dateto']." 23:59:59",'m/d/Y H:i:s');

					if(!($result = $appdb->query("select A.*,B.studentprofile_firstname,B.studentprofile_lastname,B.studentprofile_middlename,B.studentprofile_yearlevel,B.studentprofile_section,B.studentprofile_id from tbl_studentdtr as A, tbl_studentprofile as B where A.studentdtr_type='IN' and A.studentdtr_studentid=B.studentprofile_id and A.studentdtr_unixtime >= $from and A.studentdtr_unixtime <= $to"))) {
						json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
						die;
					}

					$students = array();

					if(!empty($result['rows'][0]['studentdtr_id'])) {
						foreach($result['rows'] as $k=>$v) {
							$yearlevel = getGroupRefName($v['studentprofile_yearlevel']);
							$section = getGroupRefName($v['studentprofile_section']);
							$dt = pgDateUnix($v['studentdtr_unixtime'],'m-d-Y');

							if(!empty($students[$yearlevel][$section][$v['studentprofile_id']][$dt])) {
							} else {
								//pre(array('$dt'=>$dt));
								$students[$yearlevel][$section][$v['studentprofile_id']][$dt][] = $v;
							}
						}
					}

					pre(array('$students'=>$students));

					$params['tbReports'][] = array(
						'type' => 'label',
						'label' => 'OBIS MONTESSORI',
						'labelWidth' => 250,
						'className' => 'schoolName_'.$post['formval'],
					);

					$params['tbReports'][] = array(
						'type' => 'label',
						'label' => 'Period: 07 April 2017 '.time(),
						'labelWidth' => 250,
						'className' => 'period_'.$post['formval'],
					);

					$params['tbReports'][] = array(
						'type' => 'label',
						'label' => 'MONTHLY ATTENDANCE REPORT',
						'labelWidth' => 300,
						'className' => 'monthlyattendancereport_'.$post['formval'],
					);

					$params['tbReports'][] = array(
						'type' => 'label',
						'label' => 'NURSERY - ST. JOHN',
						'labelWidth' => 250,
						'className' => 'yearlevel_'.$post['formval'],
					);

					/*$params['tbReports'][] = array(
						'type' => 'label',
						'label' => '&nbsp;',
						'labelWidth' => 250,
						'className' => 'present_'.$post['formval'],
					);*/

					/*$params['tbReports'][] = array(
						'type' => 'label',
						'label' => 'ST. JOHN',
						'labelWidth' => 250,
						'offsetLeft' => 25,
						'className' => 'section_'.$post['formval'],
					);*/

					$block = array();

					$block[] = array(
						'type' => 'label',
						'label' => '1<br />MO',
						'labelWidth' => 32,
						'offsetLeft' => 200,
						'className' => 'ddmm_'.$post['formval'],
					);

					$block[] = array(
						'type' => 'newcolumn',
						'offset' => 0,
					);

					$block[] = array(
						'type' => 'label',
						'label' => '2<br />TU',
						'labelWidth' => 32,
						'offsetLeft' => 0,
						'className' => 'ddmm_'.$post['formval'],
					);

					$block[] = array(
						'type' => 'newcolumn',
						'offset' => 0,
					);

					$block[] = array(
						'type' => 'label',
						'label' => '3<br />WE',
						'labelWidth' => 32,
						'offsetLeft' => 0,
						'className' => 'ddmm_'.$post['formval'],
					);

					$block[] = array(
						'type' => 'newcolumn',
						'offset' => 0,
					);

					$block[] = array(
						'type' => 'label',
						'label' => '4<br />TH',
						'labelWidth' => 32,
						'offsetLeft' => 0,
						'className' => 'ddmm_'.$post['formval'],
					);

					$block[] = array(
						'type' => 'newcolumn',
						'offset' => 0,
					);

					$block[] = array(
						'type' => 'label',
						'label' => '5<br />FR',
						'labelWidth' => 32,
						'offsetLeft' => 0,
						'className' => 'ddmm_'.$post['formval'],
					);

					$block[] = array(
						'type' => 'newcolumn',
						'offset' => 0,
					);

					$block[] = array(
						'type' => 'label',
						'label' => '6<br />SA',
						'labelWidth' => 32,
						'offsetLeft' => 0,
						'className' => 'ddmm_'.$post['formval'],
					);

					$block[] = array(
						'type' => 'newcolumn',
						'offset' => 0,
					);

					$block[] = array(
						'type' => 'label',
						'label' => '7<br />SU',
						'labelWidth' => 32,
						'offsetLeft' => 0,
						'className' => 'ddmm_'.$post['formval'],
					);

					$params['tbReports'][] = array(
						'type' => 'block',
						'width' => 1500,
						'blockOffset' => 0,
						'offsetTop' => 0,
						'list' => $block,
						'className' => 'block_'.$post['formval'],
					);

	/////////////////////////////////

					$block = array();

					$block[] = array(
						'type' => 'label',
						'label' => '1. RODRIGO DUTERTE',
						'labelWidth' => 200,
						'offsetLeft' => 0,
						'className' => 'studentName_'.$post['formval'],
					);

					$block[] = array(
						'type' => 'newcolumn',
						'offset' => 0,
					);

					$block[] = array(
						'type' => 'label',
						'label' => '<span class="present_'.$post['formval'].'"></span>',
						'labelWidth' => 32,
						//'className' => 'present_'.$post['formval'],
					);

					$block[] = array(
						'type' => 'newcolumn',
						'offset' => 0,
					);

					$block[] = array(
						'type' => 'label',
						'label' => '<span class="absent_'.$post['formval'].'"></span>',
						'labelWidth' => 32,
						//'className' => 'present_'.$post['formval'],
					);

					$block[] = array(
						'type' => 'newcolumn',
						'offset' => 0,
					);

					$block[] = array(
						'type' => 'label',
						'label' => '<span class="present_'.$post['formval'].'"></span>',
						'labelWidth' => 32,
						//'className' => 'present_'.$post['formval'],
					);

					$block[] = array(
						'type' => 'newcolumn',
						'offset' => 0,
					);

					$block[] = array(
						'type' => 'label',
						'label' => '<span class="absent_'.$post['formval'].'"></span>',
						'labelWidth' => 32,
						//'className' => 'present_'.$post['formval'],
					);

					$params['tbReports'][] = array(
						'type' => 'block',
						'width' => 1500,
						'blockOffset' => 0,
						'offsetTop' => 0,
						'list' => $block,
						'className' => 'block_'.$post['formval'],
					);

	/////////////////////////////////

					$block = array();

					$block[] = array(
						'type' => 'label',
						'label' => '2. GLORIA ARROYO',
						'labelWidth' => 200,
						'offsetLeft' => 0,
						'className' => 'studentName_'.$post['formval'],
					);

					$block[] = array(
						'type' => 'newcolumn',
						'offset' => 0,
					);

					$block[] = array(
						'type' => 'label',
						'label' => '<span class="present_'.$post['formval'].'"></span>',
						'labelWidth' => 32,
						//'className' => 'present_'.$post['formval'],
					);

					$block[] = array(
						'type' => 'newcolumn',
						'offset' => 0,
					);

					$block[] = array(
						'type' => 'label',
						'label' => '<span class="present_'.$post['formval'].'"></span>',
						'labelWidth' => 32,
						//'className' => 'present_'.$post['formval'],
					);

					$block[] = array(
						'type' => 'newcolumn',
						'offset' => 0,
					);

					$block[] = array(
						'type' => 'label',
						'label' => '<span class="present_'.$post['formval'].'"></span>',
						'labelWidth' => 32,
						//'className' => 'present_'.$post['formval'],
					);

					$block[] = array(
						'type' => 'newcolumn',
						'offset' => 0,
					);

					$block[] = array(
						'type' => 'label',
						'label' => '<span class="absent_'.$post['formval'].'"></span>',
						'labelWidth' => 32,
						//'className' => 'present_'.$post['formval'],
					);

					$params['tbReports'][] = array(
						'type' => 'block',
						'width' => 1500,
						'blockOffset' => 0,
						'offsetTop' => 0,
						'list' => $block,
						'className' => 'block_'.$post['formval'],
					);

	/////////////////////////////////

					$params['tbReports'][] = array(
						'type' => 'label',
						'label' => 'ST. MICHAEL',
						'labelWidth' => 250,
						'offsetLeft' => 25,
						'className' => 'section_'.$post['formval'],
					);

					$params['tbReports'][] = array(
						'type' => 'label',
						'label' => '1. SHERWIN TERUNEZ',
						'labelWidth' => 250,
						'offsetLeft' => 50,
						'className' => 'studentName_'.$post['formval'],
					);

					$params['tbReports'][] = array(
						'type' => 'label',
						'label' => '2. CELESTE TERUNEZ',
						'labelWidth' => 250,
						'offsetLeft' => 50,
						'className' => 'studentName_'.$post['formval'],
					);

					$params['tbReports'][] = array(
						'type' => 'label',
						'label' => '3. JOSHUA DANIEL TERUNEZ',
						'labelWidth' => 250,
						'offsetLeft' => 50,
						'className' => 'studentName_'.$post['formval'],
					);

					$params['tbReports'][] = array(
						'type' => 'label',
						'label' => 'ST. PAUL',
						'labelWidth' => 250,
						'offsetLeft' => 25,
						'className' => 'section_'.$post['formval'],
					);

					$params['tbReports'][] = array(
						'type' => 'label',
						'label' => '1. SHERWIN PADILLA',
						'labelWidth' => 250,
						'offsetLeft' => 50,
						'className' => 'studentName_'.$post['formval'],
					);

					$params['tbReports'][] = array(
						'type' => 'label',
						'label' => '2. CELESTE PADILLA',
						'labelWidth' => 250,
						'offsetLeft' => 50,
						'className' => 'studentName_'.$post['formval'],
					);

					$params['tbReports'][] = array(
						'type' => 'label',
						'label' => '3. JOSHUA DANIEL PADILLA',
						'labelWidth' => 250,
						'offsetLeft' => 50,
						'className' => 'studentName_'.$post['formval'],
					);

					json_encode_return($params);
					die;
				}

				$params['hello'] = 'Hello, Sherwin!';

				$newcolumnoffset = 50;

				$position = 'right';

				$params['tbDetails'] = array();
				$params['tbReports'] = array();
				//$params['tbLoginNotification'] = array();

				$block = array();

				$block[] = array(
					'type' => 'container',
					'name' => 'newmessage_yearlevel',
					'inputWidth' => 400,
					'inputHeight' => 300,
					'className' => 'newmessage_yearlevel_'.$post['formval'],
				);

				/*$block[] = array(
					'type' => 'container',
					'name' => 'newmessage_contacts',
					'inputWidth' => 400,
					'inputHeight' => 347,
					'className' => 'newmessage_contacts_'.$post['formval'],
				);*/

				$params['tbDetails'][] = array(
					'type' => 'block',
					'width' => 400,
					'blockOffset' => 0,
					'offsetTop' => 5,
					'list' => $block,
					'className' => 'newmessage_blockcontacts_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'newcolumn',
					'offset' => 15,
				);

				$block = array();

				$block[] = array(
					'type' => 'container',
					'name' => 'newmessage_section',
					'inputWidth' => 400,
					'inputHeight' => 300,
					'className' => 'newmessage_section_'.$post['formval'],
				);

				$params['tbDetails'][] = array(
					'type' => 'block',
					'width' => 400,
					'blockOffset' => 0,
					'offsetTop' => 5,
					'list' => $block,
					'className' => 'newmessage_blockyearlevel_'.$post['formval'],
				);

				/*$params['tbDetails'][] = array(
					'type' => 'input',
					'label' => 'TARDINESS GRACE PERIOD (MINUTE)',
					'labelWidth' => 250,
					'name' => 'setting_tardinessgraceperiod',
					'readonly' => $readonly,
					//'required' => !$readonly,
					'value' => !empty($params['settinginfo']['setting_tardinessgraceperiod']) ? $params['settinginfo']['group_tardinessgraceperiod'] : '',
				);*/

				$templatefile = $this->templatefile($routerid,$formid);

				//pre(array($routerid,$formid,$params,$templatefile));

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}
			}

			return false;

		} // _form_reportmainmonthlyattendance

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

					if(!empty($this->post['wid'])) {
						$retval['wid'] = $this->post['wid'];
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

					} else
					if($this->post['table']=='newmessageyearlevel') {

						if(!($result = $appdb->query("select * from tbl_groupref where groupref_type=2 order by groupref_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}
						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['groupref_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['groupref_id'],'data'=>array(0,$v['groupref_id'],$v['groupref_name']));
							}

							$retval = array('rows'=>$rows);
						}

					} else
					if($this->post['table']=='newmessagesection') {

						if(!($result = $appdb->query("select * from tbl_groupref where groupref_type=1 order by groupref_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}
						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['groupref_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['groupref_id'],'data'=>array(0,$v['groupref_id'],$v['groupref_name']));
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

	$appappreport = new APP_app_report;
}

# eof modules/app.user
