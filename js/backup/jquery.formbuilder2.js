/**
 * jQuery Form Builder Plugin
 * Copyright (c) 2009 Mike Botsko, Botsko.net LLC (http://www.botsko.net)
 * http://www.botsko.net/blog/2009/04/jquery-form-builder-plugin/
 * Originally designed for AspenMSM, a CMS product from Trellis Development
 * Licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * Copyright notice and license must remain intact for legal use
 *
 * Modified by Jason Kirby <jkirby1325@gmail.com>
 */
(function ($) {
	$.fn.formbuilder = function (options) {
		// Extend the configuration options with user-provided
		var defaults = {
			save_url: false,
			load_url: false,
			control_box_target: false,
			serialize_prefix: 'frmb',
			messages: {
				save				: "Save",
				add_new_field		: "Add New Field...",
				text				: "Text Field",
				email				: "Email Field",
				confirmation_email  : "Confirmation Email?",
				title				: "Title",
				paragraph			: "Text Area",
				phone				: "Phone",
				website				: "Website",
				checkboxes			: "Checkboxes",
				states				: "States",
				countries			: "Countries",
				radio				: "Radio",
				select				: "Select List",
				text_field			: "Text Field",
				payment_field		: "Payment",
				discount_code		: "Discount Code",
				discount_amount		: "Discount Amount",
				discount_multiplier	: "Multiplier",
				upload_file			: "Upload File",
				captcha				: "Captcha",
				label				: "Label",
				paragraph_field		: "Text Area",
				select_options		: "Select Options",
				payment_processor	: "Payment Processor",
				authamount			: "Authorization Amount",
				multiplier			: "Amount Multiplier Field",
				bill_code			: "Billing Code",
				generate_invoice	: "Generate Invoice Option",
				add					: "Add",
				checkbox_group		: "Checkbox Group",
				remove_message		: "Are you sure you want to remove this element?",
				remove				: "Remove",
				radio_group			: "Radio Group",
				selections_message	: "Allow Multiple Selections",
				hide				: "Hide",
				required			: "Required",
				use_for_confirmation : "Use For Confirmation",
				readonly			: "Read Only",
				defaultvalue		: "Default Value",
				validate_date		: "Date",
				show				: "Show", 
				text_block			: "Static Text",
				hidden_field		: "Hidden Field",
				container_text		: "Text inside a container",
				value				: "Value",
				validation			: 'Validation',
				word_count			: 'Word Count',
				character_count		: 'Character Count',
				section				: "Section",
				test_cc				: "Test Mode"
			}
		};
		$('#form-builder input[type=text]:not(.defaultvalue, .checkfield, .multiplier, .discount_multiplier, .discount_code), #form-builder textarea, #form-builder select').live('blur',function(){
			var t=$(this);
			var val=t.val();
			$('#form-builder input[type=text]:not(.defaultvalue, .checkfield, .multiplier, .discount_multiplier, .discount_code), #form-builder textarea, #form-builder select').each(function(){
				if ($(this)[0] != t[0]){
					if ($(this).val().toLowerCase().replace(/[^\w\s]/gi, '').replace(/(<([^>]+)>)/ig, '')===val.toLowerCase().replace(/[^\w\s]/gi, '').replace(/(<([^>]+)>)/ig, '') && $(this).val() !== "" && t.val() !== ""){
							alert("There is already an element with this label. It is strongly suggested that you do not use duplicate labels. Changing label to contain hidden information.");
							t.val("<h>_</h>"+val);
					}
				}
			});
		});
		var opts = $.extend(defaults, options);
		var frmb_id = 'frmb-' + $('ul[id^=frmb-]').length++;
		return this.each(function () {
			$(this).append('<div id="stage"><ul id="' + frmb_id + '" class="frmb"></ul></div>');
			var ul_obj = $('#stage');
			var field = '', field_type = '', last_id = 1, help, form_db_id;
			// Add a unique class to the current element
			$(ul_obj).addClass(frmb_id);
			// load existing form data
			if (opts.load_url) {
			    $.ajaxSetup({ cache: false });
				$.getJSON(opts.load_url, function(json) {
					form_db_id = json.form_id;
					fromJson(json.form_structure);
					$.ajaxSetup({ cache: true });

				});
			}
			// Create form control select box and add into the editor
			var controlBox = function (target) {
					var select = '';
					var box_content = '';
					var save_button = '';
					var done_button = '';
					var box_id = frmb_id + '-control-box';
					var save_id = frmb_id + '-save-button';
					var done_id = frmb_id + '-done-button';
					// Add the available options
					select += '<div class="control_option" id="container_text"><img src="images/filled_container.png"/><span> ' + opts.messages.container_text + '</span></div>';
					select += '<div class="control_option" id="input_block"><img src="images/static_text.png"/><span>' + opts.messages.text_block + '</span></div>';
					select += '<div class="control_option" id="section"><img src="images/section.png"/><span>' + opts.messages.section + '</span></div>';
					select += '<div class="control_option" id="input_text"><img src="images/text_field.png"/><span>' + opts.messages.text + '</span><img align="right" src="images/open.png" class="clickable"></div>';
					select += '<div class="control_option items" id="phone"><span>'+opts.messages.phone+'</span></div>';
					select += '<div class="control_option items" id="website"><span>'+opts.messages.website+'</span></div>';
					select += '<div class="control_option" id="email_text"><img src="images/email.png"/><span>' + opts.messages.email + '</span></div>';
					select += '<div class="control_option" id="textarea"><img src="images/textarea.png"/><span>' + opts.messages.paragraph + '</span></div>';
					select += '<div class="control_option" id="checkbox"><img src="images/checkbox.png"/><span>' + opts.messages.checkboxes + '</span></div>';
					select += '<div class="control_option" id="radio"><img src="images/radio.png"/><span>' + opts.messages.radio + '</span></div>';
					select += '<div class="control_option" id="select"><img src="images/select.png"/><span>' + opts.messages.select + '</span><img align="right" src="images/open.png" class="clickable"></div>';
					select += '<div class="control_option items" id="states"><span>'+opts.messages.states+'</span></div>';
					select += '<div class="control_option items" id="countries"><span>'+opts.messages.countries+'</span></div>';
					select += '<div class="control_option" id="upload_file"><img src="images/file_upload.png"/><span>' + opts.messages.upload_file + '</span></div>';
					select += '<div class="control_option" id="captcha"><img src="images/lock-icon.png"/><span>' + opts.messages.captcha + '</span></div>';
					select += '<div class="control_option" id="hidden_field"><img src="images/hide-icon.png"/><span>' + opts.messages.hidden_field + '</span></div>';
					select += '<div class="control_option" id="payment_field"><img src="images/payment-icon.png"/><span>' + opts.messages.payment_field + '</span></div>';
					select += '<div class="control_option" id="discount_code"><img src="images/discount-icon.png"/><span>' + opts.messages.discount_code + '</span></div>';
					// Build the control box and search button content
					box_content = '<div  class="frmb-control"> <div id="form_box_tools"><img src="images/pencil.png"/><span>Form Box Tools</span></div>'
					box_content += '<div class="form_box_controls" id="' + box_id + '">'+select + '</div></div>';
					
					save_button = '<input type="submit" id="' + save_id + '" class="frmb-submit" value="' + opts.messages.save + '"/>';	
					done_button = '<input type="submit" id="' + done_id + '" class="frmb-submit" value="Done"/>';
					
					// Insert the control box into page
					if (!target) {
						$(ul_obj).before(box_content);
					} else {
						$(target).append(box_content);
					}
					// Insert the search button
				
					$('#tab-3').after(done_button);
					$('#tab-3').after(save_button);

					// Set the form save action
					$('#' + save_id).click(function () {						
						save();
						return false;
					});
					$('#' + done_id).click(function () {
						save(true);
						return false;
					});
					$(window).keypress(function(event) {
						if (!(event.which == 115 && event.ctrlKey) && !(event.which == 19)) return true;
						save(true);
						event.preventDefault();
						return false;
					});
					
					// Add a callback to the select element
					$('.control_option:not(.clickable)').click(function (e) {
						if($(e.target).is('.clickable')){
							var t=$(this);
							while (t.next().hasClass('items')){
								t=t.next();
								t.toggle()
							}
							e.preventDefault();
							return;
						}
							appendNewField($(this).attr('id'));
						$(this).val(0).blur();
						// This solves the scrollTo dependency
					//	$('html, body').animate({
					//		scrollTop: $('#frm-' + (last_id - 1) + '-item').offset().top
					//	}, 500);
						return false;
					});
					
				}(opts.control_box_target);
			// Json parser to build the form builder
			var fromJson = function (json) {
					var values = '';
					var options = false;
					// Parse json
					$(json).each(function () {
						// checkbox type
						if (this.cssClass === 'checkbox') {
							options = [this.title];
							values = [];
							$.each(this.values, function () {
								values.push([this.value, this.baseline]);
							});
						}
						// radio type
						else if (this.cssClass === 'radio') {
							options = [this.title];
							values = [];
							$.each(this.values, function () {
								values.push([this.value, this.baseline]);
							});
						}
						// select type
						else if (this.cssClass === 'select') {
							options = [this.title, this.multiple];
							values = [];
							$.each(this.values, function () {
								values.push([this.value, this.baseline]);
							});
						}
						else {
							values = [this.values];
						}
						appendNewField(this.cssClass, values, options, this.required, this.use_for_confirmation, this.defaultvalue, this.readonly, this.validate_date, this.word_count, this.character_count, this.test_cc, this.payment_processor, this.authamount, this.multiplier, this.bill_code, this.generate_invoice, this.discount_code, this.discount_amount, this.discount_multiplier);
					});
				};
			// Wrapper for adding a new field
			var appendNewField = function (type, values, options, required, use_for_confirmation, defaultvalue, readonly, validate_date, word_count, character_count, test_cc, payment_processor, authamount, multiplier, bill_code, generate_invoice, discount_code, discount_amount, discount_multiplier) {
					field = '';
					field_type = type;
					if (typeof (values) === 'undefined') {
						values = '';
					}
					switch (type) {
					case 'container_text':
						appendContainerText(values, required);
						break;
					case 'input_text':
						appendTextInput(values, required, validate_date, defaultvalue, readonly);
						break;
					case 'payment_field':
						appendPaymentField(values, test_cc, payment_processor,authamount, multiplier, bill_code, generate_invoice);
						break;
					case 'discount_code':
						appendDiscountField(values, discount_code, discount_amount, discount_multiplier);
						break;
					case 'phone':
						appendPhoneInput(values, required);
						break;
					case 'website':
						appendWebsiteInput(values, required);
						break;
					case 'email_text':
						appendEmail(values, required, use_for_confirmation);
						break;
					case 'section':
						appendSection(values, required);
						break;
					case 'input_block':
						appendTextBlock(values, "");
						break;
					case 'textarea':
						appendTextarea(values, required, word_count, character_count);
						break;
					case 'checkbox':
						appendCheckboxGroup(values, options, required);
						break;
					case 'radio':
						appendRadioGroup(values, options, required);
						break;
					case 'select':
						appendSelectList(values, options, required);
						break;
					case 'states':
						appendStatesList(values, required);
						break;
					case 'countries':
						appendCountriesList(values, required);
						break;
					case 'upload_file':
						appendFileUpload(values, required);
						break;
					case 'captcha':
						appendCaptcha();
						break;
					case 'hidden_field':
						appendHiddenField(values, defaultvalue);
						break;
					}					
				};
			// single line input type="text"
			var appendTextInput = function (values, required, validate_date, defaultvalue, readonly) {
					validate_date = validate_date === 'checked' ? true : false;
					readonly = readonly === 'checked' ? true : false;
					field += '<div class="frm-fld"><label for="validate_date-' + last_id + '">' + opts.messages.validate_date + '</label>';
					field += '<input class="validate_date" type="checkbox" value="1" name="validate_date-' + last_id + '" id="validate_date-' + last_id + '"' + (validate_date ? ' checked="checked"' : '') + ' /></div>'
					field += '<div class="frm-fld"><label for="readonly-' + last_id + '">' + opts.messages.readonly + '</label>';
					field += '<input class="readonly" type="checkbox" value="1" name="readonly-' + last_id + '" id="readonly-' + last_id + '"' + (readonly ? ' checked="checked"' : '') + ' /></div>'
					field += '<div><label>' + opts.messages.label + '</label>';
					field += '<input class="fld-title" id="title-' + last_id + '" type="text" value="' + values + '" /></div>';
					field += '<div style="margin-top:25px;"><label>'+opts.messages.defaultvalue+'</label>';
					field += '<input placeholder="optional" class="defaultvalue" type="text" name="default_value-' + last_id + '" id="default_value-' + last_id + '" ';
					if (defaultvalue)
						field+='value="'+defaultvalue+'"';
					field += '/></div>';
					help = '';
					appendFieldLi(opts.messages.text, field, required, help);
				};
			var appendPaymentField = function(values, test_cc, payment_processor, authamount, multiplier, bill_code, generate_invoice){
				test_cc = test_cc === 'checked' ? true : false;
				generate_invoice=generate_invoice === 'checked'? true: false;
				if (!authamount)
					authamount=0;
				if (!bill_code)
					bill_code='';
				if (!multiplier)
					multiplier='';
				field += '<div class="frm-fld"><label for="test_cc-'+last_id+'">' +  opts.messages.test_cc+'</label><input class="test_cc" type="checkbox" value="0" name="test_cc-' + last_id + '" id="test_cc-' + last_id + '"' + (test_cc ? ' checked="checked"' : '') + ' /></div>';
				field += '<div class="frm-fld"><label for="generate_invoice-'+last_id+'">' +  opts.messages.generate_invoice+'</label><input class="generate_invoice" type="checkbox" value="0" name="generate_invoice-' + last_id + '" id="generate_invoice-' + last_id + '"' + (generate_invoice ? ' checked="checked"' : '') + ' /></div>';
				field += '<div class="frm-fld"><label for="payment_processor-'+last_id+'">' + opts.messages.payment_processor + '</label> <select class="payment_processor" id="payment_processor-'+last_id+'" name="payment_processor-'+last_id+'"><option '+(payment_processor=='authorizepayment'? 'selected="selected"' : '')+' value="authorizepayment">Authorize.NET</option></select></div>';
				field += '<div class="frm-fld"><label for="authamount-'+last_id+'">' + opts.messages.authamount + '</label><span style="float:left; font-size:24px; margin-top:7px;">\$</span><input class="authamount" size="5" value="'+authamount+'" style="width:80px !important; float:none;" type="text" name="authamount-'+last_id+'" id="authamount-'+last_id+'" /></div>';
				field += '<div class="frm-fld"><label for="multiplier-'+last_id+'">' + opts.messages.multiplier + '</label><span style="float:left; font-size:24px; margin-top:7px;"></span><input class="multiplier"  value="'+multiplier+'" style="float:none;" type="text" name="multiplier-'+last_id+'" id="multiplier-'+last_id+'" /></div>';
				field += '<div class="frm-fld"><label for="bill_code-'+last_id+'">' + opts.messages.bill_code + '</label><input class="bill_code" value="'+bill_code+'" size="5" type="text" name="bill_code-'+last_id+'" id="bill_code-'+last_id+'" /></div>';
				appendHiddenLi(opts.messages.payment_field, field);
			}
			var appendDiscountField = function(values, discount_code, discount_amount,  discount_multiplier){
				if (!discount_code)
					discount_code='';
				if (!discount_amount)
					discount_amount='0';
				if (!discount_multiplier)
					discount_multiplier='';
				field += '<div><label>' + opts.messages.label + '</label>';
				field += '<input class="fld-title" id="title-' + last_id + '" type="text" value="' + values + '" /></div>';
				field += '<div class="frm-fld"><label for="discount_code-'+last_id+'">' + opts.messages.discount_code + '</label><span style="float:left; font-size:24px; margin-top:7px;"></span><input class="discount_code" value="'+discount_code+'" style="float:none;" type="text" name="discount_code-'+last_id+'" id="discount_code-'+last_id+'" /></div>';
				field += '<div class="frm-fld"><label for="discount_amount-'+last_id+'">' + opts.messages.discount_amount + '</label><span style="float:left; font-size:24px; margin-top:7px;"></span><input class="discount_amount" size="5" value="'+discount_amount+'" style="width:80px !important; float:none;" type="text" name="discount_amount-'+last_id+'" id="discount_amount-'+last_id+'" /></div>';
				field += '<div class="frm-fld"><label for="discount_multiplier-'+last_id+'">' + opts.messages.discount_multiplier + '</label><span style="float:left; font-size:24px; margin-top:7px;"></span><input class="discount_multiplier"  value="'+discount_multiplier+'" style="float:none;" type="text" name="discount_multiplier-'+last_id+'" id="discount_multiplier-'+last_id+'" /></div>';
				appendHiddenLi(opts.messages.discount_code+' <div>You can assign one or many different discount codes here. To assign muliple discount code, use a comma separated list in the discount code field. You can also use a comma separated list in the discount amount and multiplier fields to associate them with each discount code. The MULTIPLIER indicates the field you want to associate the discount code to. If this is left blank, the discount is applied to the total amount. </div>', field);
			}
			var appendHiddenField = function (values, defaultvalue) {
					field += '<div><label>' + opts.messages.label + '</label>';
					field += '<input class="fld-title" id="hidden-' + last_id + '" type="text" value="' + values + '" /></div>';
					field += '<div style="margin-top:25px;"><label>'+opts.messages.defaultvalue+'</label>';
					field += '<input placeholder="optional" class="defaultvalue" type="text" name="default_value-' + last_id + '" id="default_value-' + last_id + '" ';
					if (defaultvalue)
						field+='value="'+defaultvalue+'"';
					field += '/></div>';
					help = '';
					appendHiddenLi(opts.messages.hidden_field, field);
				};
			var appendEmail = function(values, required, use_for_confirmation){
				use_for_confirmation = use_for_confirmation === 'checked' ? true : false;
				field += '<div class="frm-fld"><label style="width:20%" for="use_for_confirmation-' + last_id + '">' + opts.messages.use_for_confirmation + '</label>';
				field += '<input class="use_for_confirmation" type="checkbox"  name="use_for_confirmation-' + last_id + '" id="use_for_confirmation-' + last_id + '"' + (use_for_confirmation ? ' checked="checked"' : '') + ' /></div>'
				field += '<div><label >' + opts.messages.label + '</label>';
				field += '<input class="fld-title" id="email-' + last_id + '" type="text" value="' + values + '" /></div>';
				appendEmailFieldLi(opts.messages.email, field, required, 'A message will be sent to this email if user enters one in.', use_for_confirmation);
			}
			var appendPhoneInput = function(values, required){
				field += '<div><label >' + opts.messages.label + '</label>';
				field += '<input class="fld-title" id="phone-' + last_id + '" type="text" value="' + values + '" /></div>';
				appendFieldLi(opts.messages.phone, field, required);
			}
			var appendWebsiteInput = function(values, required){
				field += '<div><label >' + opts.messages.label + '</label>';
				field += '<input class="fld-title" id="website-' + last_id + '" type="text" value="' + values + '" /></div>';
				appendFieldLi(opts.messages.website, field, required);
			}
			//File upload 
			var appendFileUpload = function (values, required) {
					field += '<label>' + opts.messages.label + '</label>';
					field += '<input class="fld-title" id="file-' + last_id + '" type="text" value="' + values + '" />';
					help = '';
					appendFieldLi(opts.messages.upload_file, field, required);
				};
			//captcha 
			var appendCaptcha = function () {
					appendCaptchaLi(opts.messages.captcha);
					field += '<label>' + opts.messages.label + '</label>';
				};
			
				// Plain text
			var appendContainerText = function (values, required) {
					field += '<label>' + opts.messages.label + '</label>';
					field += '<textarea cols="80" id="block-'+last_id+'">'+values+'</textarea>';
					help = '';
					appendContainerTextLi(opts.messages.container_text, field);
				};
			// Plain text
			var appendTextBlock = function (values, required) {
					field += '<label>' + opts.messages.label + '</label>';
					field += '<textarea cols="80" id="block-'+last_id+'">'+values+'</textarea>';
					help = '';
					appendTextBlockLi(opts.messages.text_block, field);
				};
			// Section
			var appendSection = function (values, required) {
					field += '<label>' + opts.messages.label + '</label>';
					field += '<textarea cols="80" id="block-'+last_id+'">'+values+'</textarea>';
					help = '';
					appendSectionLi(opts.messages.section, field);
				};
			// multi-line textarea
			var appendTextarea = function (values, required, word_count, character_count) {
					word_count = word_count === 'checked' ? true : false;
					character_count = character_count === 'checked' ? true : false;
					field += '<div class="frm-fld"><label for="word_count-' + last_id + '">' + opts.messages.word_count + '</label>';
					field += '<input class="word_count" type="checkbox" value="1" name="word_count-' + last_id + '" id="word_count-' + last_id + '"' + (word_count ? ' checked="checked"' : '') + ' /></div>';
					field += '<div class="frm-fld"><label for="character_count-' + last_id + '">' + opts.messages.character_count + '</label>';
					field += '<input class="character_count" type="checkbox" value="1" name="character_count-' + last_id + '" id="character_count-' + last_id + '"' + (character_count ? ' checked="checked"' : '') + ' /></div>';
					field += '<label>' + opts.messages.label + '</label>';
					field += '<input type="text" value="' + values + '" />';
					help = '';
					appendFieldLi(opts.messages.paragraph_field, field, required, help);
				};
			// adds a checkbox element
			var appendCheckboxGroup = function (values, options, required) {
					var title = '';
					if (typeof (options) === 'object') {
						title = options[0];
					}
					field += '<div class="chk_group">';
					field += '<div class="frm-fld"><label>' + opts.messages.label + '</label>';
					field += '<input type="text" name="title" value="' + title + '" /></div>';
					field += '<div class="false-label">' + opts.messages.select_options + '</div>';
					field += '<div class="fields">';
					if (typeof (values) === 'object') {
						for (i = 0; i < values.length; i++) {
							field += checkboxFieldHtml(values[i]);
						}
					}
					else {
						field += checkboxFieldHtml('');
					}
					field += '<div class="add-area"><a href="#" class="add add_ck">' + opts.messages.add + '</a></div>';
					field += '</div>';
					field += '</div>';
					help = '';
					appendFieldLi(opts.messages.checkbox_group, field, required, help);
				};
			// Checkbox field html, since there may be multiple
			var checkboxFieldHtml = function (values) {
					var checked = false;
					var value = '';
					if (typeof (values) === 'object') {
						value = values[0];
						checked = ( values[1] === 'false' || values[1] === 'undefined' ) ? false : true;
						
					}
					field = '';
					field += '<div>';
					field += '<input type="checkbox"' + (checked ? ' checked="checked"' : '') + ' />';
					field += '<input type="text" value="' + value + '" class="checkfield"/>';
					field += '<a href="#" class="remove" title="' + opts.messages.remove_message + '">' + opts.messages.remove + '</a>';
					field += '</div>';
					return field;
				};
			// adds a radio element
			var appendRadioGroup = function (values, options, required) {
					var title = '';
					if (typeof (options) === 'object') {
						title = options[0];
					}
					field += '<div class="rd_group">';
					field += '<div class="frm-fld"><label>' + opts.messages.label + '</label>';
					field += '<input type="text" name="title" value="' + title + '" /></div>';
					field += '<div class="false-label">' + opts.messages.select_options + '</div>';
					field += '<div class="fields">';
					if (typeof (values) === 'object') {
						for (i = 0; i < values.length; i++) {
							field += radioFieldHtml(values[i], 'frm-' + last_id + '-fld');
						}
					}
					else {
						field += radioFieldHtml('', 'frm-' + last_id + '-fld');
					}
					field += '<div class="add-area"><a href="#" class="add add_rd">' + opts.messages.add + '</a></div>';
					field += '</div>';
					field += '</div>';
					help = '';
					appendFieldLi(opts.messages.radio_group, field, required, help);
				};
			// Radio field html, since there may be multiple
			var radioFieldHtml = function (values, name) {
					var checked = false;
					var value = '';
					if (typeof (values) === 'object') {
						value = values[0];
						checked = ( values[1] === 'false' || values[1] === 'undefined' ) ? false : true;
					}
					field = '';
					field += '<div>';
					field += '<input type="radio"' + (checked ? ' checked="checked"' : '') + ' name="radio_' + name + '" />';
					field += '<input type="text" value="' + value + '"  class="checkfield"/>';
					field += '<a href="#" class="remove" title="' + opts.messages.remove_message + '">' + opts.messages.remove + '</a>';
					field += '</div>';
					return field;
				};
			// adds a select/option element
			var appendSelectList = function (values, options, required) {
				var multiple = false;
					var title = '';
					if (typeof (options) === 'object') {
						title = options[0];
						multiple = options[1] === 'checked' ? true : false;
					}
					field += '<div class="opt_group">';
					field += '<div class="frm-fld"><label>' + opts.messages.label + '</label>';
					field += '<input type="text" name="title" value="' + title + '" class="checkfield" /></div>';
					field += '';
					field += '<div class="false-label">' + opts.messages.select_options + '</div>';
					field += '<div class="fields">';
					if (typeof (values) === 'object') {
						for (i = 0; i < values.length; i++) {
							field += selectFieldHtml(values[i], multiple);
						}
					}
					else {
						field += selectFieldHtml('', multiple);
					}
					field += '<div class="add-area"><a href="#" class="add add_opt">' + opts.messages.add + '</a></div>';
					field += '</div>';
					field += '</div>';
					help = '';
					appendFieldLi(opts.messages.select, field, required, help);
				};
				
			var appendStatesList = function (values, required) {
				var multiple = false;
					var title = '';
					if (typeof (options) === 'object') {
						title = options[0];
						multiple = options[1] === 'checked' ? true : false;
					}
					field += '<div class="opt_group">';
					field += '<div class="frm-fld"><label>' + opts.messages.label + '</label>';
					field += '<input type="text"  id="states-'+last_id+'" name="title" value="' + values + '" /></div>';
					field += '</div>';
					field += '</div>';
					help = '';
					appendFieldLi(opts.messages.states, field, required);
				};
			var appendCountriesList = function (values, required) {
				var multiple = false;
					var title = '';
					if (typeof (options) === 'object') {
						title = options[0];
						multiple = options[1] === 'checked' ? true : false;
					}
					field += '<div class="opt_group">';
					field += '<div class="frm-fld"><label>' + opts.messages.label + '</label>';
					field += '<input type="text"  id="countries-'+last_id+'" name="title" value="' + values + '" /></div>';
					field += '</div>';
					field += '</div>';
					help = '';
					appendFieldLi(opts.messages.countries, field, required);
				};
			// Select field html, since there may be multiple
			var selectFieldHtml = function (values, multiple) {
					if (multiple) {
						return checkboxFieldHtml(values);
					}
					else {
						return radioFieldHtml(values);
					}
				};
				var appendFileUpload = function (values, required) {
					field += '<label>' + opts.messages.label + '</label>';
					field += '<input type="text" value="' + values + '" />';
					help = '';
					appendFieldLi(opts.messages.upload_file, field, required, help);
				};
				var appendCaptchaLi = function(){
					var li = '';
					li += '<li id="frm-' + last_id + '-item" class="' + field_type + '">';
					li += '<div class="legend">';
					li += '<a id="down-' + last_id + '" class="movedown" href="javascript:;"><img src="images/movedown.png" /></a>';
					li += '<a id="up-' + last_id + '" class="moveup" href="javascript:;"><img src="images/moveup.png" /></a>';
					li += '<a id="del_' + last_id + '" class="del-button delete-confirm" href="#" title="' + opts.messages.remove_message + '"><span>' + opts.messages.remove + '</span></a>';
					li += '<div id="frm-' + last_id + '-fld" class="frm-holder">';
					li += '<div class="frm-elements">';
					li += '<div class="captcha_style">CAPTCHA</div>';
					li += field;
					li += '</div>';
					li += '</div>';
					li += '</li>';
					$(ul_obj).append(li);
					$('#frm-' + last_id + '-item').hide();
					$('#frm-' + last_id + '-item').animate({
						opacity: 'show',
						height: 'show'
					}, 'slow');
					last_id++;
				}
				var appendContainerTextLi = function(title, field_html){
					var li = '';
					li += '<li id="frm-' + last_id + '-item" class="' + field_type + '">';
					li += '<div class="legend">';
					li += '<a id="down-' + last_id + '" class="movedown" href="javascript:;"><img src="images/movedown.png" /></a>';
					li += '<a id="up-' + last_id + '" class="moveup" href="javascript:;"><img src="images/moveup.png" /></a>';
					li += '<a id="del_' + last_id + '" class="del-button delete-confirm" href="javascript:;" title="' + opts.messages.remove_message + '"><span>' + opts.messages.remove + '</span></a>';
					li += '<strong id="txt-title-' + last_id + '">' + title + '</strong></div>';
					li += '<div id="frm-' + last_id + '-fld" class="frm-holder">';
					li += '<div class="frm-elements">';
					li += field;
					li += '</div>';
					li += '</div>';
					li += '</li>';
					$(ul_obj).append(li);
					$('#frm-' + last_id + '-item').hide();
					$('#frm-' + last_id + '-item').animate({
						opacity: 'show',
						height: 'show'
					}, 'slow');
					
					tinyMCE.execCommand('mceAddControl', false, 'block-'+last_id);
					last_id++;
			};
				
			var appendTextBlockLi = function(title, field_html){
					var li = '';
					li += '<li id="frm-' + last_id + '-item" class="' + field_type + '">';
					li += '<div class="legend">';
					li += '<a id="down-' + last_id + '" class="movedown" href="#"><img src="images/movedown.png" /></a>';
					li += '<a id="up-' + last_id + '" class="moveup" href="javascript:;"><img src="images/moveup.png" /></a>';
					li += '<a id="del_' + last_id + '" class="del-button delete-confirm" href="#" title="' + opts.messages.remove_message + '"><span>' + opts.messages.remove + '</span></a>';
					li += '<strong id="txt-title-' + last_id + '">' + title + '</strong></div>';
					li += '<div id="frm-' + last_id + '-fld" class="frm-holder">';
					li += '<div class="frm-elements">';
					li += field;
					li += '</div>';
					li += '</div>';
					li += '</li>';
					$(ul_obj).append(li);
					$('#frm-' + last_id + '-item').hide();
					$('#frm-' + last_id + '-item').animate({
						opacity: 'show',
						height: 'show'
					}, 'slow');
					
					tinyMCE.execCommand('mceAddControl', false, 'block-'+last_id);
					last_id++;
			};
			var appendHiddenLi = function(title, field_html){
				var li='';
				li += '<li id="frm-' + last_id + '-item" class="' + field_type + '">';
				li += '<div class="legend">';
				li += '<a id="down-' + last_id + '" class="movedown" href="javascript:;"><img src="images/movedown.png" /></a>';
				li += '<a id="up-' + last_id + '" class="moveup" href="javascript:;"><img src="images/moveup.png" /></a>';
				li += '<a id="del_' + last_id + '" class="del-button delete-confirm" href="#" title="' + opts.messages.remove_message + '"><span>' + opts.messages.remove + '</span></a>';
				li += '<strong id="txt-title-' + last_id + '">' + title + '</strong></div>';
				li += '<div id="frm-' + last_id + '-fld" class="frm-holder">';
				li += '<div class="frm-elements">';
				li += field;
				li += '</div>';
				li += '</div>';
				li += '</li>';
				$(ul_obj).append(li);
				$('#frm-' + last_id + '-item').hide();
				$('#frm-' + last_id + '-item').animate({
					opacity: 'show',
					height: 'show'
				}, 'slow');
				last_id++;
			}
			var appendSectionLi = function(title, field_html){
					var li = '';
					li += '<li id="frm-' + last_id + '-item" class="' + field_type + '">';
					li += '<div class="legend">';
					li += '<a id="down-' + last_id + '" class="movedown" href="#"><img src="images/movedown.png" /></a>';
					li += '<a id="up-' + last_id + '" class="moveup" href="javascript:;"><img src="images/moveup.png" /></a>';
					li += '<a id="del_' + last_id + '" class="del-button delete-confirm" href="#" title="' + opts.messages.remove_message + '"><span>' + opts.messages.remove + '</span></a>';
					li += '<strong id="txt-title-' + last_id + '">' + title + '</strong></div>';
					li += '<div id="frm-' + last_id + '-fld" class="frm-holder">';
					li += '<div class="frm-elements">';
					li += field;
					li += '</div>';
					li += '</div>';
					li += '</li>';
					$(ul_obj).append(li);
					$('#frm-' + last_id + '-item').hide();
					$('#frm-' + last_id + '-item').animate({
						opacity: 'show',
						height: 'show'
					}, 'slow');
					
					tinyMCE.execCommand('mceAddControl', false, 'block-'+last_id);
					last_id++;
			};
			// Appends the new field markup to the editor
			var appendFieldLi = function (title, field_html, required, help, use_for_confirmation) {
					if (required) {
						required = required === 'checked' ? true : false;
					}
					if (use_for_confirmation) {
						use_for_confirmation = use_for_confirmation === 'checked' ? true : false;
					}
				
					var li = '';
					li += '<li id="frm-' + last_id + '-item" class="' + field_type + '">';
					li += '<div class="legend">';
					li += '<a id="down-' + last_id + '" class="movedown" href="javascript:;"><img src="images/movedown.png" /></a>';
					li += '<a id="up-' + last_id + '" class="moveup" href="javascript:;"><img src="images/moveup.png" /></a>';
					li += '<a id="del_' + last_id + '" class="del-button delete-confirm" href="#" title="' + opts.messages.remove_message + '"><span>' + opts.messages.remove + '</span></a>';
					li += '<strong id="txt-title-' + last_id + '">' + title + '</strong></div>';
					li += '<div id="frm-' + last_id + '-fld" class="frm-holder">';
					li += '<div class="frm-elements">';
					li += '<div class="frm-fld"><label for="required-' + last_id + '">' + opts.messages.required + '</label>';
					li += '<input class="required" type="checkbox" value="1" name="required-' + last_id + '" id="required-' + last_id + '"' + (required ? ' checked="checked"' : '') + ' /></div>';
					li += field;
					li += '</div>';
					li += '</div>';
					li += '</li>';
					$(ul_obj).append(li);
					$('#frm-' + last_id + '-item').hide();
					$('#frm-' + last_id + '-item').animate({
						opacity: 'show',
						height: 'show'
					}, 'slow');
					last_id++;
				};
			var appendEmailFieldLi = function (title, field_html, required, help, use_for_confirmation) {
					if (required) {
						required = required === 'checked' ? true : false;
					}
					
					var li = '';
					li += '<li id="frm-' + last_id + '-item" class="' + field_type + '">';
					li += '<div class="legend">';
					li += '<a id="down-' + last_id + '" class="movedown" href="javascript:;"><img src="images/movedown.png" /></a>';
					li += '<a id="up-' + last_id + '" class="moveup" href="javascript:;"><img src="images/moveup.png" /></a>';
					li += '<a id="del_' + last_id + '" class="del-button delete-confirm" href="#" title="' + opts.messages.remove_message + '"><span>' + opts.messages.remove + '</span></a>';
					li += '<strong id="txt-title-' + last_id + '">' + title + '</strong></div>';
					li += '<div id="frm-' + last_id + '-fld" class="frm-holder">';
					li += '<div class="frm-elements">';
					li += '<div class="frm-fld"><label for="required-' + last_id + '">' + opts.messages.required + '</label>';
					li += '<input class="required" type="checkbox" value="1" name="required-' + last_id + '" id="required-' + last_id + '"' + (required ? ' checked="checked"' : '') + ' /></div>';
					li += field;
					li += '</div>';
					li += '</div>';
					li += '</li>';
					$(ul_obj).append(li);
					$('#frm-' + last_id + '-item').hide();
					$('#frm-' + last_id + '-item').animate({
						opacity: 'show',
						height: 'show'
					}, 'slow');
					last_id++;
				};
			// handle field delete links
			$('.remove').live('click', function () {
				$(this).parent('div').animate({
					opacity: 'hide',
					height: 'hide',
					marginBottom: '0px'
				}, 'fast', function () {
					$(this).remove();
				});
				return false;
			});
			// handle field display/hide
			$('.toggle-form').live('click', function () {
				var target = $(this).attr("id");
				if ($(this).html() === opts.messages.hide) {
					$(this).removeClass('open').addClass('closed').html(opts.messages.show);
					$('#' + target + '-fld').animate({
						opacity: 'hide',
						height: 'hide'
					}, 'slow');
					return false;
				}
				if ($(this).html() === opts.messages.show) {
					$(this).removeClass('closed').addClass('open').html(opts.messages.hide);
					$('#' + target + '-fld').animate({
						opacity: 'show',
						height: 'show'
					}, 'slow');
					return false;
				}
				return false;
			});
			// handle delete confirmation
			$('.delete-confirm').live('click', function () {
				var delete_id = $(this).attr("id").replace(/del_/, '');
				if (confirm($(this).attr('title'))) {
					$('#frm-' + delete_id + '-item').animate({
						opacity: 'hide',
						height: 'hide',
						marginBottom: '0px'
					}, 'slow', function () {
						$(this).remove();
					});
				}
				return false;
			});
			$('.movedown').live('click', function(){
				var down_id = $(this).attr("id").replace(/down-/, '');	
				tinyMCE.execCommand('mceRemoveControl', false, 'block-'+down_id);
				$('#frm-' + down_id + '-item').insertAfter($('#frm-' + down_id + '-item').next('li'));
				tinyMCE.execCommand('mceAddControl', false, 'block-'+down_id);
			});
			$('.moveup').live('click', function(){
				var up_id = $(this).attr("id").replace(/up-/, '');	
				tinyMCE.execCommand('mceRemoveControl', false, 'block-'+up_id);
				$('#frm-' + up_id + '-item').insertBefore($('#frm-' + up_id + '-item').prev('li'));
				tinyMCE.execCommand('mceAddControl', false, 'block-'+up_id);
			});
			// Attach a callback to add new checkboxes
			$('.add_ck').live('click', function () {
				$(this).parent().before(checkboxFieldHtml());
				return false;
			});
			// Attach a callback to add new options
			$('.add_opt').live('click', function () {
				$(this).parent().before(selectFieldHtml('', false));
				return false;
			});
			// Attach a callback to add new radio fields
			$('.add_rd').live('click', function () {
				$(this).parent().before(radioFieldHtml(false, $(this).parents('.frm-holder').attr('id')));
				return false;
			});
			
			// saves the serialized data to the server 
			var save = function (done) {
					//get group permissions
					var group='{';
					var user='{';
					var num_times_filled_out=0;
					if ($('#num_times_filled_out').length != 0)
						num_times_filled_out=$('#num_times_filled_out').val()
					var no_restrictions=0; //No restrictions on who can view reports
					if ($('#no_restrictions').prop("checked"))
						no_restrictions=1;
						
					$('.group').each(function(){
						var g=$(this).val();
						group+='"'+g+'":{"edit":';
						if ($('#groupedit_'+g).prop("checked"))
							group+='"true"';
						else
							group+='"false"';
						group+=',"report":';
						if ($('#groupreport_'+g).prop("checked"))
							group+='"true"';
						else
							group+='"false"';
						group+='},';						
					});
					group=group.substring(0, group.length - 1);
					group+='}';
					//get user permissions
					$('.user').each(function(){
						var g=$(this).val();
						user+='"'+g+'":{"edit":';
						if ($('#useredit_'+g).prop("checked"))
							user+='"true"';
						else
							user+='"false"';
						user+=',"report":';
						if ($('#userreport_'+g).prop("checked"))
							user+='"true"';
						else
							user+='"false"';
						user+='},';
					});
				
					user=user.substring(0, user.length - 1);
					user+='}';
					
					done = typeof done !== 'undefined' ? done : false;
					if ($('#savename').val()==''){
						alert("Please give this form a title.");
						return false;
					}
					if ($('#form_date').val()==''){
						alert("Please give this form a date.");
						return false;
					}	
					$('#notify_textarea').val($('#notify_textarea').val().replace(/^\s+|\s+$/g, ''));
					var emails=$('#notify_textarea').val();
					var accepted_email=$('#accepted_email').val();
					var declined_email=$('#declined_email').val();
					emails=emails.split("\n");
					var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

					for (var i=0; i<emails.length; i++){
						if (!re.test(emails[i])){
							alert("One of your notification emails is invalid.");
							return false;
						}
					}
					
					if ($('#notification_email').val() && !re.test($('#notification_email').val())){
						alert("Please enter a valid notification email.");
						return false;
					}
					if ($('#url')===undefined)
						var url='';
					else
						var url=$('#url').val();
					if (opts.save_url) {
						$.ajax({
							type: "POST",
							url: opts.save_url,
							data: $(ul_obj).serializeFormList({
								prepend: opts.serialize_prefix
							}) 	+ "&form_id=" + form_db_id
								+"&savename="+$('#savename').val().replace(/<\/?[^>]+>/gi, '')
								+"&userId="+$('#userId').val()
								+"&date="+$('#form_date').val()
								+"&visible="+$('input:radio[name=visible]:checked').val()
								+"&enabled="+$('input:radio[name=enabled]:checked').val()
								+"&notifyees="+escape($('#notify_textarea').val())
								+"&notification_email="+$('#notification_email').val()
								+"&accepted_email="+escape($('#accepted_email').val())
								+"&declined_email="+escape($('#declined_email').val())
								+"&sitename="+$('#sitename').val()
								+"&url="+url
								+"&form_invisible_message="+$('#form_invisible_message').val()
								+"&form_no_reg_message="+encodeURIComponent(tinyMCE.get('form_no_reg_message').getContent())
								+"&thank_you_page_message="+encodeURIComponent(tinyMCE.get('thank_you_page_message').getContent())
								+"&no_restrictions="+no_restrictions
								+"&groupperms="+group
								+"&userperms="+user
								+"&num_times_filled_out="+num_times_filled_out
								+"&thankyou_url="+$('#thankyou_url').val()
								+"&email_confirmation_to_customer="+encodeURIComponent(tinyMCE.get('email_confirmation_to_customer').getContent())
								+"&email_confirmation_to_administrator="+encodeURIComponent(tinyMCE.get('email_confirmation_to_administrator').getContent())
								+"&email_confirmation_to_administrator_subject="+$('#email_confirmation_to_administrator_subject').val()
								+"&email_confirmation_to_customer_subject="+$('#email_confirmation_to_customer_subject').val()
								+"&invoice="+encodeURIComponent(tinyMCE.get('invoice').getContent()),
								success: function (data) { $('.form-status').html("Form has been saved"); if (data){ if (done) window.location='admin.php?created=true'+'&random='+Math.random();  else window.location='admin.php?id='+data+'&created=true'+'&random='+Math.random(); }}
						});
					}
				};
		});
	};
})(jQuery);
/**
 * jQuery Form Builder List Serialization Plugin
 * Copyright (c) 2009 Mike Botsko, Botsko.net LLC (http://www.botsko.net)
 * Originally designed for AspenMSM, a CMS product from Trellis Development
 * Licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * Copyright notice and license must remain intact for legal use
 * Modified from the serialize list plugin
 * http://www.botsko.net/blog/2009/01/jquery_serialize_list_plugin/
 */
(function ($) {
	$.fn.serializeFormList = function (options) {
		var payment=false;
		// Extend the configuration options with user-provided
		var defaults = {
			prepend: 'ul',
			is_child: false,
			attributes: ['class']
		};
		var opts = $.extend(defaults, options);
		if (!opts.is_child) {
			opts.prepend = '&' + opts.prepend;
		}
		var serialStr = '';
		// Begin the core plugin
		this.each(function () {
			var ul_obj = this;
			var li_count = 0;
			var c = 1;
			$(this).children().each(function () {
				for (att = 0; att < opts.attributes.length; att++) {
					var key = (opts.attributes[att] === 'class' ? 'cssClass' : opts.attributes[att]);
					paymentStr='';
					serialStr += opts.prepend + '[' + li_count + '][' + key + ']=' + encodeURIComponent($(this).attr(opts.attributes[att]));
					// append the form field values
					if (opts.attributes[att] === 'class') {
						serialStr += opts.prepend + '[' + li_count + '][required]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input.required').attr('checked'));
						serialStr += opts.prepend + '[' + li_count + '][readonly]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input.readonly').attr('checked'));
						serialStr += opts.prepend + '[' + li_count + '][validate_date]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input.validate_date').attr('checked'));
						serialStr += opts.prepend + '[' + li_count + '][word_count]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input.word_count').attr('checked'));
						serialStr += opts.prepend + '[' + li_count + '][defaultvalue]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input.defaultvalue').val());
						serialStr += opts.prepend + '[' + li_count + '][use_for_confirmation]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input.use_for_confirmation').attr('checked'));
						
						serialStr += opts.prepend + '[' + li_count + '][character_count]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input.character_count').attr('checked'));
						switch ($(this).attr(opts.attributes[att])) {
						case 'container_text':
							serialStr += opts.prepend + '[' + li_count + '][values]=' + encodeURIComponent(tinyMCE.get($('textarea', this).attr('id')).getContent());
							break;
						case 'input_text':
							serialStr += opts.prepend + '[' + li_count + '][values]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input[type=text]').val());
							break;
						case 'hidden_field':
							serialStr += opts.prepend + '[' + li_count + '][values]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input[type=text]').val());
							break;	
						case 'email_text':
							serialStr += opts.prepend + '[' + li_count + '][values]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input[type=text]').val());
							break;
						case 'phone':
							serialStr += opts.prepend + '[' + li_count + '][values]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input[type=text]').val());
							break;
						case 'website':
							serialStr += opts.prepend + '[' + li_count + '][values]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input[type=text]').val());
							break;
						case 'upload_file':
							serialStr += opts.prepend + '[' + li_count + '][values]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input[type=text]').val());
							break;
						case 'captcha':
							break;
						case 'input_block':
							serialStr += opts.prepend + '[' + li_count + '][values]=' + encodeURIComponent(tinyMCE.get($('textarea', this).attr('id')).getContent());
							break;
						case 'section':
							serialStr += opts.prepend + '[' + li_count + '][values]=' + encodeURIComponent(tinyMCE.get($('textarea', this).attr('id')).getContent());
							break;
						case 'textarea':
							serialStr += opts.prepend + '[' + li_count + '][values]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input[type=text]').val());
							break;
						case 'states':
							serialStr += opts.prepend + '[' + li_count + '][values]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input[type=text]').val());
							break;
						case 'countries':
							serialStr += opts.prepend + '[' + li_count + '][values]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input[type=text]').val());
							break;
						case 'payment_field':
							serialStr += opts.prepend + '[' + li_count + '][test_cc]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input.test_cc').attr('checked'));
							serialStr += opts.prepend + '[' + li_count + '][generate_invoice]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input.generate_invoice').attr('checked'));
							serialStr += opts.prepend + '[' + li_count + '][values]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input[type=text]').val());
							serialStr += opts.prepend + '[' + li_count + '][payment_processor]=' + encodeURIComponent($('#' + $(this).attr('id') + ' select.payment_processor').val());
							serialStr += opts.prepend + '[' + li_count + '][authamount]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input.authamount').val());
							serialStr += opts.prepend + '[' + li_count + '][multiplier]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input.multiplier').val());
							serialStr += opts.prepend + '[' + li_count + '][bill_code]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input.bill_code').val());
							payment=true;
							break;
						case 'discount_code':
							serialStr += opts.prepend + '[' + li_count + '][values]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input[type=text]').val());
							serialStr += opts.prepend + '[' + li_count + '][discount_code]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input.discount_code').val());
							serialStr += opts.prepend + '[' + li_count + '][discount_amount]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input.discount_amount').val());
							serialStr += opts.prepend + '[' + li_count + '][discount_multiplier]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input.discount_multiplier').val());
							break;
						case 'checkbox':
							c = 1;
							$('#' + $(this).attr('id') + ' input[type=text]').each(function () {
								if ($(this).attr('name') === 'title') {
									serialStr += opts.prepend + '[' + li_count + '][title]=' + encodeURIComponent($(this).val());
								}
								else {
									serialStr += opts.prepend + '[' + li_count + '][values][' + c + '][value]=' + encodeURIComponent($(this).val());
									serialStr += opts.prepend + '[' + li_count + '][values][' + c + '][baseline]=' + $(this).prev().attr('checked');
								}
								c++;
							});
							break;
						case 'radio':
							c = 1;
							$('#' + $(this).attr('id') + ' input[type=text]').each(function () {
								if ($(this).attr('name') === 'title') {
									serialStr += opts.prepend + '[' + li_count + '][title]=' + encodeURIComponent($(this).val());
								}
								else {
									serialStr += opts.prepend + '[' + li_count + '][values][' + c + '][value]=' + encodeURIComponent($(this).val());
									serialStr += opts.prepend + '[' + li_count + '][values][' + c + '][baseline]=' + $(this).prev().attr('checked');
								}
								c++;
							});
							break;
						case 'select':
							c = 1;
							serialStr += opts.prepend + '[' + li_count + '][multiple]=' + $('#' + $(this).attr('id') + ' input[name=multiple]').attr('checked');
							$('#' + $(this).attr('id') + ' input[type=text]').each(function () {
								if ($(this).attr('name') === 'title') {
									serialStr += opts.prepend + '[' + li_count + '][title]=' + encodeURIComponent($(this).val());
								}
								else {
									serialStr += opts.prepend + '[' + li_count + '][values][' + c + '][value]=' + encodeURIComponent($(this).val().replace(/<(?:.|\n)*?>/gm, ''));
									serialStr += opts.prepend + '[' + li_count + '][values][' + c + '][baseline]=' + $(this).prev().attr('checked');
								}
								c++;
							});
							break;
						}
					}
				}
				li_count++;
			});
			if (payment){
				serialStr+='&frmb['+li_count+'][cssClass]=transaction_id&frmb['+li_count+'][required]=undefined&frmb['+li_count+'][readonly]=undefined&frmb['+li_count+'][validate_date]=undefined&frmb['+li_count+'][word_count]=undefined&frmb['+li_count+'][defaultvalue]=undefined&frmb['+li_count+'][use_for_confirmation]=undefined&frmb['+li_count+'][character_count]=undefined&frmb['+li_count+'][values]=Transaction_Id';
				li_count++;
				serialStr+='&frmb['+li_count+'][cssClass]=cc_type&frmb['+li_count+'][required]=undefined&frmb['+li_count+'][readonly]=undefined&frmb['+li_count+'][validate_date]=undefined&frmb['+li_count+'][word_count]=undefined&frmb['+li_count+'][defaultvalue]=undefined&frmb['+li_count+'][use_for_confirmation]=undefined&frmb['+li_count+'][character_count]=undefined&frmb['+li_count+'][values]=CC_Type';
				li_count++;
				serialStr+='&frmb['+li_count+'][cssClass]=registration_sequence&frmb['+li_count+'][required]=undefined&frmb['+li_count+'][readonly]=undefined&frmb['+li_count+'][validate_date]=undefined&frmb['+li_count+'][word_count]=undefined&frmb['+li_count+'][defaultvalue]=undefined&frmb['+li_count+'][use_for_confirmation]=undefined&frmb['+li_count+'][character_count]=undefined&frmb['+li_count+'][values]=Registration_Sequence';

			//	field += '<div class="frm-fld"><label for="test_cc-'+last_id+'">' +  opts.messages.test_cc+'</label><input class="test_cc" type="checkbox" value="1" name="test_cc-' + last_id + '" id="test_cc-' + last_id + '"' + (test_cc ? ' checked="checked"' : '') + ' /></div>';
			//	field += '<div class="false-label">' + opts.messages.payment_processor + '</div><div> <select class="payment_processor" id="payment_processor-'+last_id+'" name="payment_processor-'+last_id+'"><option '+(payment_processor=='authorizepayment'? 'selected="selected"' : '')+' value="authorizepayment">Authorize.NET</option></select></div>';
			//	field += '<div class="false-label">' + opts.messages.authamount + '</div><div><span style="float:left; font-size:24px; margin-top:7px;">\$</span><input class="authamount" size="5" value="'+authamount+'" style="width:80px !important; float:none;" type="text" name="authamount-'+last_id+'" id="authamount-'+last_id+'" /></div>';
			//	field += '<div class="false-label">' + opts.messages.bill_code + '</div><div><input class="bill_code" value="'+bill_code+'" type="text" name="bill_code-'+last_id+'" id="bill_code-'+last_id+'" /></div>';
			}
		});
		
		return (serialStr);
	};
})(jQuery);