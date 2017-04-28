<?php
error_reporting (E_ALL ^ E_NOTICE);
/**
 * @package 	jquery.Formbuilder
 * @author 		Michael Botsko
 * @copyright 	2009, 2012 Trellis Development, LLC
 *
 * This PHP object is the server-side component of the jquery formbuilder
 * plugin. The Formbuilder allows you to provide users with a way of
 * creating a formand saving that structure to the database.
 *
 * Using this class you can easily prepare the structure for storage,
 * rendering the xml file needed for the builder, or render the html of the form.
 *
 * This package is licensed using the Mozilla Public License 1.1
 *
 * We encourage comments and suggestion to be sent to mbotsko@trellisdev.com.
 * Please feel free to file issues at http://github.com/botskonet/jquery.formbuilder/issues
 * Please feel free to fork the project and provide patches back.
 *
 * Modified by Jason Kirby <jkirby1325@gmail.com>
 */


/**
 * @abstract This class is the server-side component that handles interaction with
 * the jquery formbuilder plugin.
 * @package jquery.Formbuilder
 */
class Formbuilder {

	/**
	 * @var array Holds the form id and array form structure
	 * @access protected
	 */
	protected $_form_array;
	 /**
	  * Constructor, loads either a pre-serialized form structure or an incoming POST form
	  * @param array $containing_form_array
	  * @access public
	  */
	public function __construct($form = false){

		$form = is_array($form) ? $form : array();
		
		// Set the serialized structure if it's provided
		// otherwise, store the source
		if(array_key_exists('form_structure', $form)){
			$form['form_structure'] = json_decode($form['form_structure'], true);
			$this->_form_array = $form;
		}
		else if(array_key_exists('frmb', $form)){
			$_form = array();
			$_form['form_id'] = ($form['form_id'] == "undefined" ? false : $form['form_id']);
			$_form['form_structure'] = $form['frmb']; // since the form is from POST, set it as the raw array
			$this->_form_array = $_form;
		}
		return true;
	}


	/**
	 * Returns the form array with the structure encoded, for saving to a database or other store
	 *
	 * @access public
	 * @return array
	 */
	public function get_encoded_form_array(){
		return array('form_id'=>$this->_form_array['form_id'],'form_structure'=>json_encode($this->_form_array['form_structure']));
	}


	/**
	 * Prints out the generated json file with a content-type of application/json
	 *
	 * @access public
	 */
	public function render_json(){
		header("Content-Type: application/json");
		print json_encode( $this->_form_array );
	}


	/**
	 * Renders the generated html of the form.
	 *
	 * @param string $form_action Action attribute of the form element.
	 * @access public
	 * @uses generate_html
	 */
	public function render_html($form_action = false){
		print $this->generate_html($form_action);
	}
  
    public function get_html($form_action=false, $form_id=false){
		return $this->generate_html($form_action, $form_id);
	}

	/**
	 * Generates the form structure in html.
	 * 
	 * @param string $form_action Action attribute of the form element.
	 * @return string
	 * @access public
	 */
	public function generate_html($form_action = false, $form_id=false){
		
		$html = '';
		$payment = $generate_invoice = false;
		$form_action = $form_action ? $form_action : $_SERVER['PHP_SELF'];
		
		if(is_array($this->_form_array['form_structure'])){
			//JK Mod
			include("scripts/settings.php");
			$html .= '<form id="formform" enctype="multipart/form-data" class="frm-bldr" method="post" action="'.$form_action.'<?php if (!empty($_GET[\'account\'])) echo "?account=".$_GET["account"]; ?>">'."\n";
			$html .= '<div class="frmb">'."\n";
			$html .= '<input type="hidden" name="fid" value="'.$form_id.'"/>'."\n";
			$html .= '<input type="hidden" name="url" value="<?php echo $_GET[\'q\']; ?>"/>'."\n";
			$html .= '<input type="hidden" name="userid" value="<?php echo $_POST[\'userid\']; ?>"/>'."\n";
			$html .= '<table>'."\n";
			//End JK Mod
			foreach($this->_form_array['form_structure'] as $field){
				if ($field['payment_processor'] && $field['payment_processor'] !== 'undefined'){
					if ($field['test_cc'] == "checked")
						$field['payment_processor'].="test";
					if ($field['generate_invoice'] == 'checked')
						$generate_invoice=true;
					$payment=true;
					$payment_processor=$field['payment_processor'];
				}
				$html .= $this->loadField((array)$field)."\n";
			}
			$html .= '</table>';
			if (!$payment)
				$html .= '<div class="btn-submit"><input type="submit" id="submitform2" name="submit" value="Submit" /></div>'."\n";
			else{
				$html .= '<?php if ($_SESSION["membertype"] == "admin"): ?><div class="btn-submit"><input type="submit" id="submitform2" name="submit" value="Submit" /></div><div class="btn-submit"><input type="submit" id="submitform2" name="'.$payment_processor.'" value="Make Payment" /></div><?php else: ?><div class="btn-submit"><input type="submit" id="submitform2" name="'.$payment_processor.'" value="Submit and Continue to Payment" /></div><?php endif; ?>'."\n";
			}
			$html .=  '</div>'."\n" ;
			$html .= '<div><input type="submit" value="Save for completion later"  name="saveforlater" id="saveform"/>'."\n";
			if ($generate_invoice)
				$html .= '<input type="submit" value="Save and generate invoice" name="generateinvoice" id="generate_invoice"/>'."\n";
			$html .=  '</div>'."\n";
			$html .=  '</form>'."\n";			
		}	
		return $html;
	}


	/**
	 * Parses the POST data for the results of the speific form values. Checks
	 * for required fields and returns an array of any errors.
	 *
	 * @access public
	 * @returns array
	 */
	public function process(){

		$error		= array();
		$results 	= array();

		// Put together an array of all expected indices
		if(is_array($this->_form_array['form_structure'])){
			foreach($this->_form_array['form_structure'] as $field){
				
				$field = (array)$field;

				$field['required'] = ($field['required'] == 'checked');

				if($field['cssClass'] == 'input_text' || $field['cssClass'] == 'textarea'){

					$val = $this->getPostValue( $this->elemId($field['values']));

					if($field['required'] && empty($val)){
						$error[] = 'Please complete the ' . $field['values'] . ' field.';
					} else {
						$results[ $this->elemId($field['values']) ] = $val;
					}
				}
				elseif($field['cssClass'] == 'radio' || $field['cssClass'] == 'select'){

					$val = $this->getPostValue( $this->elemId($field['title']));

					if($field['required'] && empty($val)){
						$error[] = 'Please complete the ' . $field['title'] . ' field.';
					} else {
						$results[ $this->elemId($field['title']) ] = $val;
					}
				}
				elseif($field['cssClass'] == 'checkbox'){
					$field['values'] = (array)$field['values'];
					if(is_array($field['values']) && !empty($field['values'])){

						$at_least_one_checked = false;

						foreach($field['values'] as $item){
							$item = (array)$item;
							$elem_id = $this->elemId($item['value'], $field['title']);

							$val = $this->getPostValue( $elem_id );

							if(!empty($val)){
								$at_least_one_checked = true;
							}

							$results[ $this->elemId($item['value']) ] = $this->getPostValue( $elem_id );
						}

						if(!$at_least_one_checked && $field['required']){
							$error[] = 'Please check at least one ' . $field['title'] . ' choice.';
						}
					}
				} else { }
			}
		}
		$success = empty($error);
		return array('success'=>$success,'results'=>$results,'errors'=>$error);		
	}


	//+++++++++++++++++++++++++++++++++++++++++++++++++
	// NON-PUBLIC FUNCTIONS
	//+++++++++++++++++++++++++++++++++++++++++++++++++


	/**
	 * Loads a new field based on its type
	 *
	 * @param array $field
	 * @access protected
	 * @return string
	 */
	protected function loadField($field){

		if(is_array($field) && isset($field['cssClass'])){
			switch($field['cssClass']){
				//JK Mod
				case 'container_text':
					return $this->loadContainerText($field);
					break;
				case 'input_text':
					return $this->loadInputText($field);
					break;
				case 'email_text':
					return $this->loadInputText($field);
					break;
				case 'hidden_field':
					return $this->loadHiddenField($field);
				case 'states':
					return $this->loadStates($field);
					break;
				case 'countries':
					return $this->loadCountries($field);
					break;
				case 'phone':
					return $this->loadPhoneField($field);
					break;
				case 'website':
					return $this->loadWebsiteField($field);
					break;
				//End JK Mod
				case 'input_block':
					return $this->loadParagraph($field);
					break;
				case 'upload_file':
					return $this->loadUploadFile($field);
					break;
				case 'captcha':
					return $this->loadcaptcha($field);
					break;
				//JK Mod
				case 'section':
					return $this->loadSection($field);
					break;
				//End JK Mod
				case 'textarea':
					return $this->loadTextarea($field);
					break;
				case 'checkbox':
					return $this->loadCheckboxGroup($field);
					break;
				case 'radio':
					return $this->loadRadioGroup($field);
					break;
				case 'select':
					return $this->loadSelectBox($field);
					break;
				case 'payment_field':
					return $this->loadPayment($field);
					break;
				case 'discount_code':
					return $this->loadDiscountCode($field);
					break;
			}
		}		
		return false;
	}


	/**
	 * Returns html for an input type="text"
	 * 
	 * @param array $field Field values from database
	 * @access protected
	 * @return string
	 */
	protected function loadInputText($field){
		$values=str_replace('&lt;',"<", $field['values']); //JK Mod
		$values=str_replace('&gt;',">", $values); //JK Mod
		$field['required'] = $field['required'] == 'checked' ? ' required' : false;
		$field['validate_date'] = $field['validate_date'] == 'checked' ? ' validate_date' : false; //JK Mod
				
		$required_field=($field['required'])? "*" : ""; //JK Mod
		$readonly=$field['readonly'] =='checked' ? "readonly" : ""; //JK Mod
		
		$validate_date=($field['validate_date'])? "*" : ""; //JK Mod
		$requiredfieldtext = ($required_field)? " required_field ":""; //JK Mod
		$validate_datetext = ($validate_date)? " validate_date ":""; //JK Mod
		$classtext="class='".$requiredfieldtext." ".$validate_datetext." ".$field['cssClass']."'"; //JK Mod
		$html = '';
		$value=$this->getPostValue($this->elemId($field['values']));
		if (!$value && $field['defaultvalue']){
			$value=$field['defaultvalue'];
			if ($value=='#date')
				$script='<script>var today = new Date(); var dd = today.getDate(); var mm = today.getMonth()+1; var yyyy = today.getFullYear(); if(dd<10){dd="0"+dd} if(mm<10){mm="0"+mm} today = mm+"/"+dd+"/"+yyyy; $("#'.$this->elemId($field['values']).'").val(today);</script>';
		}
		if (!$value || $value=='undefined')
			$value='';
		if (strpos($classtext, 'email_text'))
			$value = "<?php echo \$_GET['email']; ?>";
		
		$html .= sprintf('<tr><td align="left"><div class="%s%s" id="fld-%s">', $this->elemId($field['cssClass']), $field['required'], $this->elemId($field['values']));
		$html .= sprintf('<label for="%s">%s %s</label>' . "</td>", $this->elemId($field['values']), $values, $required_field);
		$html .= sprintf('<td align="left"><input type="text" id="%s" name="%s" value="%s" %s %s/>',
								$this->elemId($field['values']),
								$this->elemId($field['values']),
								$value,
								$readonly,
								$classtext);
		$html .= '</div>' . "</td>";	
		$html.="</tr>";
		if ($script)
			$html.=$script;
		return $html;

	}
	protected function loadHiddenField($field){
		$values=str_replace('&lt;',"<", $field['values']); //JK Mod
		$values=str_replace('&gt;',">", $values); //JK Mod
		$classtext="class='".$field['cssClass']."'"; //JK Mod
		$html = '';
		$value=$this->getPostValue($this->elemId($field['values']));
		if (!$value && $field['defaultvalue']){
			$value=$field['defaultvalue'];
			if ($value=='#date')
				$script='<script>var today = new Date(); var dd = today.getDate(); var mm = today.getMonth()+1; var yyyy = today.getFullYear(); if(dd<10){dd="0"+dd} if(mm<10){mm="0"+mm} today = mm+"/"+dd+"/"+yyyy; $("#'.$this->elemId($field['values']).'").val(today);</script>';
		}
		if (!$value || $value=='undefined')
			$value='';

		$html .= sprintf('<tr>');
		$html .= sprintf('<td colspan="2" align="left"><input type="hidden" id="%s" name="%s" value="%s" %s/>',
								$this->elemId($field['values']),
								$this->elemId($field['values']),
								$value,
								$classtext);
		$html .= '</div>' . "</td>";	
		$html.="</tr>";
		if ($script)
			$html.=$script;
		return $html;
	}
	protected function loadPhoneField($field){
		$values=str_replace('&lt;',"<", $field['values']); //JK Mod
		$values=str_replace('&gt;',">", $values); //JK Mod
		$field['required'] = $field['required'] == 'checked' ? ' required' : false;
		$required_field=($field['required'])? "*" : ""; //JK Mod
		$requiredfieldtext = ($required_field)? " required_field ":""; //JK Mod
		$classtext="class='".$requiredfieldtext." phonefield'"; //JK Mod
		$html = '';
		$value=$this->getPostValue($this->elemId($field['values']));
		
		if (!$value || $value=='undefined')
			$value='';

		$html .= sprintf('<tr><td align="left"><div class="%s%s" id="fld-%s">', $this->elemId($field['cssClass']), $field['required'], $this->elemId($field['values']));
		$html .= sprintf('<label for="%s">%s %s</label>' . "</td>", $this->elemId($field['values']), $values, $required_field);
		$html .= sprintf('<td align="left"><input type="text" id="%s" name="%s" value="%s" %s placeholder="###-###-####"/>',
								$this->elemId($field['values']),
								$this->elemId($field['values']),
								$value,
								$classtext);
		$html .= '</div>' . "</td>";	
		$html.="</tr>";
		return $html;
	}
	protected function loadWebsiteField($field){
		$values=str_replace('&lt;',"<", $field['values']); //JK Mod
		$values=str_replace('&gt;',">", $values); //JK Mod
		$field['required'] = $field['required'] == 'checked' ? ' required' : false;
		$required_field=($field['required'])? "*" : ""; //JK Mod
		$requiredfieldtext = ($required_field)? " required_field ":""; //JK Mod
		$classtext="class='".$requiredfieldtext." websitefield'"; //JK Mod
		$html = '';
		$value=$this->getPostValue($this->elemId($field['values']));
		
		if (!$value || $value=='undefined')
			$value='';

		$html .= sprintf('<tr><td align="left"><div class="%s%s" id="fld-%s">', $this->elemId($field['cssClass']), $field['required'], $this->elemId($field['values']));
		$html .= sprintf('<label for="%s">%s %s</label>' . "</td>", $this->elemId($field['values']), $values, $required_field);
		$html .= sprintf('<td align="left"><input type="text" id="%s" name="%s" value="%s" %s placeholder="http://www.example.com" />',
								$this->elemId($field['values']),
								$this->elemId($field['values']),
								$value,
								$classtext);
		$html .= '</div>' . "</td>";	
		$html.="</tr>";
		return $html;
	}
	protected function loadPayment($field){
		$html = '';
		$html .= '<input type="hidden" id="Transaction_Id" name="Transaction_Id" value="" class="hide"/>'; 
		$html .= '<input type="hidden" id="Auth_Amount" name="Auth_Amount" value="" class="hide"/> 
				  <input type="hidden" id="CC_Number" name="CC_Number" value="" class="hide"/> 
				  <input type="hidden" id="Registration_Sequence" name="Registration_Sequence" value="" class="hide"/> 
				  <input type="hidden" id="Date_Paid" name="Date_Paid" value="" class="hide"/> 
				  <input type="hidden" id="Amount_Paid" name="Amount_Paid" value="0" class="hide"/> 
				  <input type="hidden" id="CC_Type" name="CC_Type" value="" class="hide"/> 
				  <input type="hidden" id="authamount" name="authamount" value="'.$field['authamount'].'" class="hide"/> 
				  <input type="hidden" id="base_amount" name="base_amount" value="'.$field['authamount'].'" class="hide"/> 
				  <input type="hidden" id="billcode" name="billcode" value="'.$field['bill_code'].'" class="hide"/>';
		if ($field['multiplier']){
			$html.='
				<script>
					$(document).ready(function(){
						$("#'.str_replace(" ","_",$field['multiplier']).'").blur(function(){
							$("#authamount").val('.$field['authamount'].'*$(this).val());
							$("#base_amount").val($("#authamount").val());							
						});
					});
				</script>
			';
		}
		return $html;
	}
	protected function loadDiscountCode($field){
		
		$values=str_replace('&lt;',"<", $field['values']); //JK Mod
		$values=str_replace('&gt;',">", $values); //JK Mod
		$html = '';
		$value=$this->getPostValue($this->elemId($field['values']));
		
		if (!$value || $value=='undefined')
			$value='';
		
		$html .= sprintf('<td><label for="%s">%s</label>' . "</td>", $this->elemId($field['values']), $values);
		$html .= sprintf('<td align="left"><input type="text" id="dc-%s" name="dc-%s" value="%s" %s />',
								$this->elemId($field['values']),
								$this->elemId($field['values']),
								$value,
								$classtext);
		$html .= '</div>' . "</td>";
		$html.="</tr>";
		return $html;
	}
	protected function loadStates($field){
		$field['required'] = $field['required'] == 'checked' ? ' required' : false;
		$required_field=($field['required'])? "*" : ""; //JK Mod
		$requiredfieldtext = ($required_field)? " required_field ":""; //JK Mod
		$classtext="class='".$requiredfieldtext." states_list'"; //JK Mod
		$html = '';
		$value=$this->getPostValue($this->elemId($field['values']));
		$html .= sprintf('<tr><td align="left"><div class="%s%s" id="fld-%s">', $this->elemId($field['cssClass']), $field['required'], $this->elemId($field['values']));
		$html .= sprintf('<label for="%s">%s %s</label>' . "</td><td>", $this->elemId($field['values']), $field['values'], $required_field);
		$html .= sprintf('<select id="%s" name="%s" %s>',
								$this->elemId($field['values']),
								$this->elemId($field['values']),
								$classtext
								);
						
			$html .= '</select></div>' . "</td>";	
			$html.="</tr>";
			return $html;
	}
	protected function loadCountries($field){
		$field['required'] = $field['required'] == 'checked' ? ' required' : false;
		$required_field=($field['required'])? "*" : ""; //JK Mod
		$requiredfieldtext = ($required_field)? " required_field ":""; //JK Mod
		$classtext="class='".$requiredfieldtext." countries_list'"; //JK Mod
		$html = '';
		$value=$this->getPostValue($this->elemId($field['values']));
		$html .= sprintf('<tr><td align="left"><div class="%s%s" id="fld-%s">', $this->elemId($field['cssClass']), $field['required'], $this->elemId($field['values']));
		$html .= sprintf('<label for="%s">%s %s</label>' . "</td><td>", $this->elemId($field['values']), $field['values'], $required_field);
		$html .= sprintf('<select id="%s" name="%s" %s>',
								$this->elemId($field['values']),
								$this->elemId($field['values']),
								$classtext
								);
		
		$html .= '</select></div>' . "</td>";	
		$html.="</tr>";
		return $html;
	}
	/**
	 * Returns html for an input type="file"
	 * 
	 * @param array $field Field values from database
	 * @access protected
	 * @return string
	 */
	protected function loadUploadFile($field){
		$values=str_replace('&lt;',"<", $field['values']); //JK Mod
		$values=str_replace('&gt;',">", $values); //JK Mod
		$field['required'] = $field['required'] == 'checked' ? ' required' : false;
		$field['validate_date'] = $field['validate_date'] == 'checked' ? ' validate_date' : false; //JK Mod
		$required_field=($field['required'])? "*" : ""; //JK Mod
		$validate_date=($field['validate_date'])? "*" : ""; //JK Mod
		$requiredfieldtext = ($required_field)? " required_field ":""; //JK Mod
		$validate_datetext = ($validate_date)? " validate_date ":""; //JK Mod
		$classtext="class='".$requiredfieldtext." ".$validate_datetext."'"; //JK Mod
		$html = '';
		$html .= sprintf('<tr><td align="left"><div class="%s%s" id="fld-%s">', $this->elemId($field['cssClass']), $field['required'], $this->elemId($field['values']));
		$html .= sprintf('<label for="%s">%s %s</label>' . "</td>", $this->elemId($field['values']), $values, $required_field);
		$html .= sprintf('<td align="left"><input type="file" id="%s" name="%s" value="%s" %s/>',
								$this->elemId($field['values']),
								$this->elemId($field['values']),
								$this->getPostValue($this->elemId($field['values'])),
								$classtext,
								$this->getPostValue($this->elemId($field['values']))
								);
		$html .= '</div>' . "</td>";	
		$html.="</tr>";
		return $html;

	}
	protected function loadCaptcha($field){
		$values=str_replace('&lt;',"<", $field['values']); //JK Mod
		$values=str_replace('&gt;',">", $values); //JK Mod
		$html = '';
		$html .= sprintf('<tr><td colspan="2" align="center"><div class="%s" id="fld-%s">', $this->elemId($field['cssClass']),  $this->elemId($field['values']));
		$html .= sprintf('<script src="../js/jquerycaptcha.js"></script>
					<?php 
						require_once("../lib/recaptchalib.php");
						$publickey = "6LdYiOgSAAAAAMg2SxnD4D-ycaGm8Mf_npX3csuk";
						
					?><div id="captcha-wrap">
					<div id="termsckb-wrap"><p class="bold">Type in the words below (separated by a space):</p></div>
					<?php
						echo recaptcha_get_html($publickey, null, true);
					?>
					<script>
						$("document").ready(function(){
							$("form").attr("action", "javascript:validateCaptcha()");
						});
					</script>
					<div id="captcha-status"></div>
			</div>');
		$html .= '</div>' . "</td>";	
		$html.="</tr>";
		return $html;

	}
	/**
	 * Returns html for an <p> text </p>
	 * 
	 * @param array $field Field values from database
	 * @access protected
	 * @return string
	 */
	protected function loadParagraph($field){

		$html = '';
		$values=str_replace('&lt;',"<", $field['values']);
		$values=str_replace('&gt;',">", $values);
		$html .= sprintf('<tr><td colspan="2"><p>%s</p></td></tr>', $values);
		$html .= '</div>' . "</td></tr>";

		return $html;

	}
	/**
	 * Returns html for an <div class="clbox4"></div> container
	 * 
	 * @param array $field Field values from database
	 * @access protected
	 * @return string
	 */
	protected function loadContainerText($field){

		$html = '';
		$values=str_replace('&lt;',"<", $field['values']);
		$values=str_replace('&gt;',">", $values);
		$html .= sprintf('<tr><td colspan="2"><div class="clbox4"><p>%s</p></div></td></tr>', $values);
		$html .= '</div>' . "</td></tr>";

		return $html;

	}
	/**
	 * Returns html for a <div class="fb_section"></div> container
	 * 
	 * @param array $field Field values from database
	 * @access protected
	 * @return string
	 */
	protected function loadSection($field){

		$html = '';
		$values=str_replace('&lt;',"<", $field['values']);
		$values=str_replace('&gt;',">", $values);
		$html .= sprintf('<tr><td colspan="2"><div class="fb_section">%s</div></td></tr>', $values);
		$html .= '</div>' . "</td></tr>";

		return $html;

	}


	/**
	 * Returns html for a <textarea>
	 *
	 * @param array $field Field values from database
	 * @access protected
	 * @return string
	 */
	protected function loadTextarea($field){

		$field['required'] = $field['required'] == 'checked' ? ' required' : false;
		$field['word_count'] = $field['word_count'] == 'checked' ? ' word_count' : false; //JK Mod
		$field['character_count'] = $field['character_count'] == 'checked' ? ' character_count' : false; //JK Mod
		$required_field=($field['required'])? "*" : "";
		$word_count=($field['word_count'])? "*" : ""; //JK Mod
		$character_count=($field['character_count'])? "*" : ""; //JK Mod
		$requiredfieldtext = ($required_field)? "class='required_field'":"";
		$values=str_replace('&lt;',"<", $field['values']);
		$values=str_replace('&gt;',">", $values);
		$html = '';
		$html .= sprintf('<tr><td colspan="2"><div class="%s%s" id="fld-%s">', $this->elemId($field['cssClass']), $field['required'], $this->elemId($field['values']));
		$html .= sprintf('<label for="%s">%s %s</label>' . "<br/>", $this->elemId($field['values']), $values, $required_field);
		$html .= sprintf('<textarea id="%s" name="%s" rows="5" cols="61" %s>%s</textarea>',
								$this->elemId($field['values']),
								$this->elemId($field['values']),
								$requiredfieldtext,
								$this->getPostValue($this->elemId($values)));
		$html .= '</div>';
		if ($word_count){
			$html .= '<div>';
			$html .= '<label style="float:left">Word Count: </label>';
			$html .= '<div class="word_counter"></div>';
			$html .= '</div>';
		}
		if ($character_count){
			$html .= '<div>';
			$html .= '<label style="float:left">Character Count: </label>';
			$html .= '<div class="character_counter"></div>';
			$html .= '</div>';
		}
		$html .= "</td></tr>";
		return $html;
	}


	/**
	 * Returns html for an <input type="checkbox"
	 *
	 * @param array $field Field values from database
	 * @access protected
	 * @return string
	 */
	protected function loadCheckboxGroup($field){
		$values=str_replace('&lt;',"<", $field['title']);
		$values=str_replace('&gt;',">", $values);
		$item['default']==false;
		$field['required'] = $field['required'] == 'checked' ? ' required' : false;
		$required_field=($field['required'])? "*" : "";
		$html = '';
		$html .= sprintf('<tr><td colspan="2"><div class="%s%s" id="fld-%s">', $this->elemId($field['cssClass']), $field['required'], $this->elemId($field['title']));

		if(isset($field['title']) && !empty($field['title'])){
			$html .= sprintf('<span class="false_label">%s %s</span>' . "<br/>", $values, $required_field);
		}
		$field['values'] = (array)$field['values'];
		if(isset($field['values']) && is_array($field['values'])){
			$html .= sprintf('<span class="multi-row cleaformx">');
			foreach($field['values'] as $item){
				$value=str_replace('&lt;',"<", $item['value']);
				$value=str_replace('&gt;',">", $value);
				$item = (array)$item;

				// set the default checked value
				$checked = $item['baseline'] == 'checked' ? true : false;

				// if checked, set html
				$checked = $checked ? ' checked="checked"' : '';

				$checkbox 	= '<span class="row cleaformx"><input type="checkbox" %s id="%s" name="%s[]" value="%s" %s /><label for="%s-%s">%s</label></span>' . "<br/>";
				$html .= sprintf($checkbox, $requiredfieldtext, $this->elemId($item['value']), $this->elemId($field['title']),  $this->elemId($item['value']), $checked, $this->elemId($field['title']), $this->elemId($item['value']), $value);
			}
			$html .= sprintf('</span>');
		}

		$html .= '</div>' . "</td></tr>";

		return $html;

	}


	/**
	 * Returns html for an <input type="radio"
	 * @param array $field Field values from database
	 * @access protected
	 * @return string
	 */
	protected function loadRadioGroup($field){
		$values=str_replace('&lt;',"<", $field['title']);
		$values=str_replace('&gt;',">", $values);
		$field['required'] = $field['required'] == 'checked' ? ' required' : false;
		$required_field=($field['required'])? "*" : "";
		$requiredfieldtext = ($required_field)? "class='required_field'":"";
		$html = '';

		$html .= sprintf('<tr><td colspan="2"><div class="%s%s" id="fld-%s">', $this->elemId($field['cssClass']), $field['required'], $this->elemId($field['title']));

		if(isset($field['title']) && !empty($field['title'])){
			$html .= sprintf('<span class="false_label">%s %s</span>' . "<br/>", $values, $required_field);
		}
		$field['values'] = (array)$field['values'];
		if(isset($field['values']) && is_array($field['values'])){
			$html .= sprintf('<span class="multi-row">');
			foreach($field['values'] as $item){
				
				$item = (array)$item;
				$value=str_replace('&lt;',"<", $item['value']);
				$value=str_replace('&gt;',">", $value);
				// set the default checked value
				$checked = $item['baseline'] == 'checked' ? true : false;

				// if checked, set html
				$checked = $checked ? ' checked="checked"' : '';

				$radio 		= '<span class="row cleaformx"><input type="radio" %s id="%s" name="%s" value="%s" %s /><label>%s</label></span>' . "<br/>";
				$html .= sprintf($radio,
										$requiredfieldtext,
										$this->elemId($item['value']),
										$this->elemId($field['title']),
										$this->elemId($item['value']),
										$checked,
										$value
										);
			}
			$html .= sprintf('</span>') ;
		}
		$html .= '</div>' . "</td></tr>";
		return $html;

	}


	/**
	 * Returns html for a <select>
	 * 
	 * @param array $field Field values from database
	 * @access protected
	 * @return string
	 */
	protected function loadSelectBox($field){
	
		$values=str_replace('&lt;',"<", $field['title']);
		$values=str_replace('&gt;',">", $values);
		$field['required'] = $field['required'] == 'checked' ? ' required' : false;
		$required_field=($field['required'])? "*" : "";
		$requiredfieldtext = ($required_field)? "class='required_field'":"";
		$html = '';

		$html .= sprintf('<tr><td colspan="2"><div class="%s%s" id="fld-%s">', $this->elemId($field['cssClass']), $field['required'], $this->elemId($field['title']));

		if(isset($field['title']) && !empty($field['title'])){
			$html .= sprintf('<label for="%s">%s %s</label>' . "<br/>", $this->elemId($field['title']), $values, $required_field);
		}
		$field['values'] = (array)$field['values'];

		if(isset($field['values']) && is_array($field['values'])){
		
			$multiple = $field['multiple'] == "checked" ? ' multiple="multiple"' : '';
			$html .= sprintf('<select %s name="%s" id="%s" %s>', $requiredfieldtext, $this->elemId($field['title']), $this->elemId($field['title']), $multiple);
			if($field['required']){ $html .= '<option value="">Selection Required</label>'; }
			
			foreach($field['values'] as $item){
				
				$item = (array)$item;

				// set the default checked value
				$checked = $item['baseline'] == 'checked' ? true : false;

				// if checked, set html
				$checked = $checked ? ' selected="selected"' : '';
			
				$option 	= '<option value="%s"%s>%s</option>';
				$html .= sprintf($option, $this->elemId($item['value']), $checked, $item['value']);
			}
			$html .= '</select>';
			$html .= '</div>' . "</td></tr>";
		}
		return $html;
	}


	/**
	 * Generates an html-safe element id using it's label
	 * 
	 * @param string $label
	 * @return string
	 * @access protected
	 */
	protected function elemId($label, $prepend = false){
		if(is_string($label)){
			$prepend = is_string($prepend) ? $this->elemId($prepend).'-' : false;
			$fieldid = preg_replace("/&lt;.+?&gt;/is", "", str_replace(" ", "_", trim($label)) );
			$fieldid= preg_replace("/<.+?>/is", "", str_replace(" ", "_", $fieldid) ) ;
			$fieldid=html_entity_decode($fieldid, ENT_QUOTES);
		
			$patterns = array(); //JK Mod
			$patterns[0] = '/[^.a-zA-Z0-9_-]+/';//JK Mod
			$replacements = array(); //JK Mod
			$replacements[0] = ''; //JK Mod     
			$fieldid = preg_replace($patterns, $replacements, trim($fieldid));//JK Mod
			return $fieldid;
		}
		return false;
	}

	/**
	 * Attempts to load the POST value into the field if it's set (errors)
	 *
	 * @param string $key
	 * @return mixed
	 */
	protected function getPostValue($key){
		return array_key_exists($key, $_POST) ? $_POST[$key] : false;
	}
	
}
?>