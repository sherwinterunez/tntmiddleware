<?php
/*
* 
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Template include file
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

if(!class_exists('APP_Form')) {

	class APP_Form {

		var $cr = "\n";
		
		var $fields = array();

		function __construct() {
		}
		
		function __destruct() {
		}
		
		function form($id=false,$action=false,$template=false,$toolbar=false,$method='post',$params=false,$vars=false,$style="margin:10px;") {
			global $apptemplate;
			
			if(empty($id)||empty($action)||empty($template)) {
				die('ERROR: APP_Form::form: Invalid form parameter');
			}
			
			if(empty($_SESSION['id'])) {
				die('ERROR: APP_Form::form: Invalid session id');
			}
			
			if(empty($_SESSION['form_'.$id])) {
				$formid = sha1($_SESSION['id'].microtime());
				$_SESSION['form_'.$id] = $formid;
			} else {
				$formid = $_SESSION['form_'.$id];
			}
			
			echo '<div id="formdiv_'.$id.'" style="'.$style.'" class="windowbg">';
			//echo '<form enctype="multipart/form-data" id="formid_'.$id.'" name="formid_'.$id.'" method="'.$method.'" action="'.$action.'" class="windowbg">';
			echo '<form id="formid_'.$id.'" name="formid_'.$id.'" method="'.$method.'" action="'.$action.'" class="windowbg">';
			
			if(!empty($toolbar)) {
				echo '<div id="formtoolbar_'.$id.'"></div>';
			}
			
			if(is_array($params)&&!empty($params)) {
				foreach($params as $k=>$v) {
					echo '<input type="hidden" name="'.$k.'" id="formvar_'.$k.'" value="'.$v.'" />';
				}
			}
			
			echo '<input type="hidden" name="formid" id="formid" value="'.$formid.'" />';
			echo '<input type="hidden" name="formname" id="formname" value="'.$id.'" />';
			
			$apptemplate->load_template($template,$vars);
			
			echo '</form>';
			echo '</div>';
			
			return true;
		}
		
		function form_valid($formid=false,$debug=false) {
		
			//pre(array('id'=>$formid));
		
			if($debug) {
				pre(array('id'=>$formid,'debug'=>debug_backtrace()));
			}
		
			if(!empty($formid)) {
			} else {
				die('ERROR: APP_Form::form_valid: Invalid form id ['.$formid.']');
			}
			
			if(empty($_POST['formid'])||empty($_POST['formname'])) {
				return false;
			}
			
			if(empty($_SESSION['form_'.$_POST['formname']])) {
				return false;
			}
			
			if($_SESSION['form_'.$_POST['formname']]==$_POST['formid']) {
				return true;
			}
			
			return false;
		}

		function element($element='div',$data=array()) {
		
			//if(!empty($data['name'])&&$data['name']=='socdate') {
			//	pre($data); die;
			//}

			if(empty($data['class'])) {
				$aclass = array();
				if(!empty($data['readonly'])) {
					$aclass[] = 'readonly';
				}
				if(!empty($data['disabled'])) {
					$aclass[] = 'disabled';
				}
				if(!empty($aclass)) {
					$data['class'] = $aclass;
				}
			} else
				
			if(!empty($data['class'])&&is_array($data['class'])) {
				if(!empty($data['readonly'])&&!in_array('readonly',$data['class'])) {
					$data['class'][] = 'readonly';
				}
				if(!empty($data['disabled'])&&!in_array('disabled',$data['class'])) {
					$data['class'][] = 'disabled';
				}
			} else {
				$aclass = array($data['class']);
				if(!empty($data['readonly'])&&!in_array('readonly',$aclass)) {
					$aclass[] = 'readonly';
				}
				if(!empty($data['disabled'])&&!in_array('disabled',$aclass)) {
					$aclass[] = 'disabled';
				}
				if(!empty($aclass)) {
					$data['class'] = $aclass;
				}
			}

			$output = '<' . $element;
			
			if(isset($data['value'])) {
				//if(is_array($data['value'])) {
				//	json_return_error(5,array('pre'=>$data));
				//}
				$data['value'] = htmlentities($data['value']);
			}
			
			if($element!='input'&&$element!='option') {
				if(isset($data['value'])) {
					$value = $data['value'];
					unset($data['value']);
				}
			}
			
			if(!empty($data['inputname'])) {
				unset($data['name']);
			}
			
			foreach($data as $k=>$v) {
				if($k!='innerhtml'&&$k!='option'&&$k!='required') {
					if(is_array($v)) {
						$o = implode(' ',$v);
						$output .= ' '.$k.'="'.$o.'"';
					} else {
						if($k=='checked'&&empty($v)) {
						} else
						if($k=='disabled'&&empty($v)) {
						} else
						if($k=='inputname'&&empty($v)) {
							continue;
						} else
						if($k=='inputname'&&!empty($v)) {
							$output .= ' name="'.$v.'"';
						} else
						if($k=='name') {
							$output .= ' '.$k.'="form['.$v.']"';
						} else
						if($k=='onload') {
							$output .= ' '.$k."='".clearcrlf($v)."'";
						} else						
						if($k=='nowrap'&&!empty($v)) {
							$output .= " $k ";
						} else {
							//if($v===false) {
							//} else
							if(is_numeric($v)) {
								$output .= ' '.$k.'="'.trim($v).'"';
							} else
							if($v=='0') {
								$output .= ' '.$k.'="'.trim($v).'"';
							} else
							if(!empty($v)) $output .= ' '.$k.'="'.$v.'"';
						}
					}
				}
			}

			if($element!='input') {
				$output .= '>';
			} else {
				$output .= ' />';
			}
			
			if($element!='option') {
				$output .= $this->cr;
			}

			if(!empty($data['innerhtml'])) {
				$output .= $data['innerhtml'];
			} else
			if(isset($data['innerhtml'])&&trim($data['innerhtml'])==='0') {
				$output .= $data['innerhtml'];
			}
			
			if(!empty($data['option'])&&is_array($data['option'])) {
				foreach($data['option'] as $k=>$v) {
					if(isset($value)&&$v==$value) {
						$output .= $this->element('option',array('value'=>$v,'innerhtml'=>$v,'selected'=>'selected'));
					} else {
						$output .= $this->element('option',array('value'=>$v,'innerhtml'=>$v));
					}
				}
			}

			if(!empty($data['optionindex'])&&is_array($data['optionindex'])) {
				foreach($data['optionindex'] as $k=>$v) {
					if(isset($value)&&($v==$value||$k==$value)) {
						$output .= $this->element('option',array('value'=>$k,'innerhtml'=>$v,'selected'=>'selected'));
					} else {
						$output .= $this->element('option',array('value'=>$k,'innerhtml'=>$v));
					}
				}
			}
			
			if($element!='input'&&$element!='option') {
			
				if(isset($value)&&$element!='select') {
					$output .= $value;
				}
			
				$output .= '</'.$element.'>'.$this->cr;
			}
			
			if($element=='option') {
				$output .= '</'.$element.'>'.$this->cr;
			}
			
			return $output;
		}
		
		function fieldset($data=array()) {
				
			$output = '<fieldset';

			if(!empty($data['fieldset_id'])) {
				$output .= ' id="'.$data['fieldset_id'].'"';
			}

			if(!empty($data['fieldset_class'])&&is_array($data['fieldset_class'])) {
				$o = implode(' ',$data['fieldset_class']);
				$output .= ' class="'.$o.'"';
			} else {
				$output .= ' class="form-fieldset"';
			}

			if(!empty($data['fieldset_style'])) {
				$output .= ' style="'.$data['fieldset_style'].'"';
			}
	
			$output .= '>' . $this->cr;
			
			if(!empty($data['title'])) {
				$output .= '<legend';

				if(!empty($data['legend_id'])) {
					$output .= ' id="'.$data['legend_id'].'"';
				}

				if(!empty($data['legend_class'])&&is_array($data['legend_class'])) {
					$o = implode(' ',$data['legend_class']);
					$output .= ' class="'.$o.'"';
				}

				if(!empty($data['legend_style'])) {
					$output .= ' style="'.$data['legend_style'].'"';
				}

				$output .='>'.$data['title'].'</legend>' . $this->cr;
			}
			
			if(!empty($data['innerhtml'])) {
				$output .= $data['innerhtml'];
			}
			
			$output .= '</fieldset> ' . $this->cr;
			
			return $output;
		}
		
		function div($data=array()) {
			return $this->element('div',$data);
		}

		function label($data=array()) {
			return $this->element('label',$data);
		}

		function span($data=array()) {
			return $this->element('span',$data);
		}
		
		function br($data=array()) {
			return $this->element('br',$data);
		}

		function table($data=array()) {
			$temp = '';
			
			$header = !empty($data['header'])&&is_array($data['header'])?$data['header']:array();
			$rows = !empty($data['rows'])&&is_array($data['rows'])?$data['rows']:array();
			
			unset($data['header']);
			unset($data['rows']);
			
			/*
			foreach($header as $v) {
				$temp .= $this->th($v);
			}
			*/
			
			foreach($header as $v) {

				$columns = !empty($v['columns'])&&is_array($v['columns'])?$v['columns']:array();
				
				$ctemp = '';

				foreach($columns as $n) {
					$ctemp .= $this->th($n);
				}

				unset($v['columns']);
				
				$v['innerhtml'] = $ctemp;
				
				$temp .= $this->tr($v);

			}
			
			foreach($rows as $v) {
			
				if(isset($v['html'])&&!empty($v['html'])) {
					$temp .= $v['html'];
					continue;
				} else
				if(isset($v['html'])&&empty($v['html'])) {
					continue;
				}
			
				$columns = !empty($v['columns'])&&is_array($v['columns'])?$v['columns']:array();
				
				$ctemp = '';
				
				foreach($columns as $n) {
					$ctemp .= $this->td($n);
				}
				
				unset($v['columns']);
				
				$v['innerhtml'] = $ctemp;
				
				$temp .= $this->tr($v);
			
			}
			
			$data['innerhtml'] = $temp;
		
			return $this->element('table',$data);
		}
		
		function trtd($data=array()) {
			$temp = '';
			
			$rows = !empty($data['rows'])&&is_array($data['rows'])?$data['rows']:array();

			foreach($rows as $v) {
			
				$columns = !empty($v['columns'])&&is_array($v['columns'])?$v['columns']:array();
				
				$ctemp = '';
				
				foreach($columns as $n) {
					$ctemp .= $this->td($n);
				}
				
				unset($v['columns']);
				
				$v['innerhtml'] = $ctemp;
				
				$temp .= $this->tr($v);
			
			}
			
			return $temp;
		}

		function tr($data=array()) {
			return $this->element('tr',$data);
		}

		function td($data=array()) {
			return $this->element('td',$data);
		}

		function th($data=array()) {
			return $this->element('th',$data);
		}

		function hidden($data=array()) {
			$output = '';
			
			$data['type'] = 'hidden';
			
			if(!isset($data['name'])||empty($data['name'])) {
				$data['name'] = 'textfield';
			}
			
			if(!isset($data['id'])||empty($data['id'])) {
				$data['id'] = $data['name'];
			}
			
			$output = $this->element('input',$data);
			
			if(empty($this->fields[$data['name']])) {
				$this->fields[$data['name']] = $data;
			} else {
				die('ERROR: APP_Form::text: Duplicate form element name ('.$data['name'].')');
			}
			
			return $output;
		}

		function password($data=array()) {
			$output = '';
			
			$data['type'] = 'password';
			
			if(!isset($data['name'])||empty($data['name'])) {
				$data['name'] = 'textfield';
			}
			
			if(!isset($data['id'])||empty($data['id'])) {
				$data['id'] = $data['name'];
			}

			if(!isset($data['class'])||empty($data['class'])) {
				$data['class'] = array('inputtext');
			}
			
			if(!empty($data['class'])&&is_array($data['class'])) {
				if(!in_array('inputtext',$data['class'])) {
					$data['class'][] = 'inputtext';
				}
			} else
			
			if(!empty($data['class'])) {
				$data['class'] = array($data['class']);

				if(!in_array('inputtext',$data['class'])) {
					$data['class'][] = 'inputtext';
				}
			}
			
			$output = $this->element('input',$data);
			
			if(empty($this->fields[$data['name']])) {
				$this->fields[$data['name']] = $data;
			} else {
				die('ERROR: APP_Form::text: Duplicate form element name ('.$data['name'].')');
			}
			
			return $output;
		}
		
		function text($data=array()) {
			$output = '';
			
			$data['type'] = 'text';
			
			if(!isset($data['name'])||empty($data['name'])) {
				$data['name'] = 'textfield';
			}
			
			if(!isset($data['id'])||empty($data['id'])) {
				$data['id'] = $data['name'];
			}
			
			if(!isset($data['class'])||empty($data['class'])) {
				$data['class'] = array('inputtext');
			}
			
			if(!empty($data['class'])&&is_array($data['class'])) {
				if(!in_array('inputtext',$data['class'])) {
					$data['class'][] = 'inputtext';
				}
			} else
			
			if(!empty($data['class'])) {
				$data['class'] = array($data['class']);

				if(!in_array('inputtext',$data['class'])) {
					$data['class'][] = 'inputtext';
				}
			}
			
			if(!empty($data['required'])) {
				if(!in_array('required',$data['class'])) {
					$data['class'][] = 'required';
				}
			}

			$before_element = isset($data['before_element'])&&!empty($data['before_element']) ? $data['before_element'] : '';
			
			$after_element = isset($data['after_element'])&&!empty($data['after_element']) ? $data['after_element'] : '';
			
			unset($data['before_element']);
			unset($data['after_element']);
			
			$output = $before_element . $this->element('input',$data) . $after_element;
		
			//$output = $this->element('input',$data);
			
			if(empty($this->fields[$data['name']])) {
				$this->fields[$data['name']] = $data;
			} else {
				die('ERROR: APP_Form::text: Duplicate form element name ('.$data['name'].')');
			}
			
			return $output;
		}

		function checkbox($data=array()) {
			$data['type'] = 'checkbox';

			if(!isset($data['name'])||empty($data['name'])) {
				$data['name'] = 'checkbox';
			}
			
			if(!isset($data['id'])||empty($data['id'])) {
				$data['id'] = $data['name'];
			}

			if(!isset($data['class'])||empty($data['class'])) {
				$data['class'] = 'inputcheckbox';
			}

			if(empty($this->fields[$data['name']])) {
				$this->fields[$data['name']] = $data;
			} else {
				die('ERROR: APP_Form::checkbox: Duplicate form element name ('.$data['name'].')');
			}
			
			$before_element = isset($data['before_element'])&&!empty($data['before_element']) ? $data['before_element'] : '';
			
			$after_element = isset($data['after_element'])&&!empty($data['after_element']) ? $data['after_element'] : '';
			
			unset($data['before_element']);
			unset($data['after_element']);
			
			$ret = $before_element . $this->element('input',$data) . $after_element;
			
			return $ret;
		}
		
		function select($data=array()) {
			$data['type'] = 'select';
			
			if(!isset($data['name'])||empty($data['name'])) {
				$data['name'] = 'select';
			}
			
			if(!isset($data['id'])||empty($data['id'])) {
				$data['id'] = $data['name'];
			}

			if(!isset($data['class'])||empty($data['class'])) {
				$data['class'] = 'inputselect';
			}

			$before_element = isset($data['before_element'])&&!empty($data['before_element']) ? $data['before_element'] : '';
			
			$after_element = isset($data['after_element'])&&!empty($data['after_element']) ? $data['after_element'] : '';
			
			unset($data['before_element']);
			unset($data['after_element']);

			if(empty($this->fields[$data['name']])) {
				$this->fields[$data['name']] = $data;
			} else {
				die('ERROR: APP_Form::select: Duplicate form element name ('.$data['name'].')');
			}
			
			return $before_element . $this->element('select',$data) . $after_element;
		}
		
		function textarea($data=array()) {
			$data['type'] = 'textarea';

			if(!isset($data['name'])||empty($data['name'])) {
				$data['name'] = 'textarea';
			}
			
			if(!isset($data['id'])||empty($data['id'])) {
				$data['id'] = $data['name'];
			}

			if(!isset($data['class'])||empty($data['class'])) {
				$data['class'] = 'inputtextarea';
			}
			
			if(empty($this->fields[$data['name']])) {
				$this->fields[$data['name']] = $data;
			} else {
				die('ERROR: APP_Form::textarea: Duplicate form element name ('.$data['name'].')');
			}
			
			return $this->element('textarea',$data);
		}

		function file($data=array()) {
			$data['type'] = 'file';

			if(!isset($data['name'])||empty($data['name'])) {
				$data['name'] = 'file';
			}
			
			if(!isset($data['id'])||empty($data['id'])) {
				$data['id'] = $data['name'];
			}

			if(!isset($data['class'])||empty($data['class'])) {
				$data['class'] = 'inputfile';
			}
			
			if(empty($this->fields[$data['name']])) {
				$this->fields[$data['name']] = $data;
			} else {
				die('ERROR: APP_Form::file: Duplicate form element name ('.$data['name'].')');
			}
			
			return $this->element('input',$data);
		}
		
		function form_element($data) {
		
			if(empty($data['title'])) {
				$data['title'] = 'Element Title Here';
			}

			if(empty($data['innerhtml'])) {
				$data['innerhtml'] = 'Element Content Here';
			}
			
			if(!empty($data['type'])) {
				if(empty($data['data'])) {
					$data['data'] = array();
				}
				if(!empty($data['required'])) {
					$data['data']['required'] = $data['required'];
					
					/*
					if($data['required']==true) {
						if(!empty($data['data']['class'])&&is_array($data['data']['class'])) {
							if(!in_array('required',$data['data']['class'])) {
								$data['data']['class'][] = 'required';
							}
						} else
						
						if(!empty($data['data']['class'])) {
							$data['data']['class'] = array('required',$data['data']['class']);
						}
					}
					*/
				}
				if(empty($data['data']['title'])) {
					$data['data']['title'] = $data['title'];
				}
								
				switch($data['type']) {
					case 'checkbox':
						$data['innerhtml'] = $this->checkbox($data['data']);
						break;
					case 'select':
						$data['innerhtml'] = $this->select($data['data']);
						break;
					case 'text':
						$data['innerhtml'] = $this->text($data['data']);
						break;
					case 'textarea':
						$data['innerhtml'] = $this->textarea($data['data']);
						break;
					case 'file':
						$data['innerhtml'] = $this->file($data['data']);
						break;
					case 'password':
						$data['innerhtml'] = $this->password($data['data']);
						break;
				}
			}
			
			if(!empty($data['required'])) {
				$data['title'] .= $this->span(array('class'=>'required', 'innerhtml'=>'*'));
			}

			$before_element = isset($data['before_element'])&&!empty($data['before_element']) ? $data['before_element'] : '';
			
			$after_element = isset($data['after_element'])&&!empty($data['after_element']) ? $data['after_element'] : '';
		
			return $this->label(array(
				'style' => isset($data['label_style'])&&!empty($data['label_style']) ? $data['label_style'] : false,
				'class' => isset($data['label_class'])&&!empty($data['label_class']) ? $data['label_class'] : false,
				'innerhtml' => 
					$this->span(array(
						'class'=>array('form-title'),
						'innerhtml'=>$data['title'],
						'style' => isset($data['title_style'])&&!empty($data['title_style']) ? $data['title_style'] : false,
						//'class' => !empty($data['title_class']) ? $data['title_class'] : false
					)) . 
					
					$before_element .
					
					$this->span(array(
						'class' => array('form-element'),
						'innerhtml' => $data['innerhtml'],
						'style' => isset($data['element_style'])&&!empty($data['element_style']) ? $data['element_style'] : false,
					)) . 
					
					$after_element
			));
		}
				
	} // class
	
	$appform = new APP_Form;

}

/* INCLUDES_END */
