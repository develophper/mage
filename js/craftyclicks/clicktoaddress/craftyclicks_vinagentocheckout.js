/*
// This is a collection of JavaScript code to allow easy integration of 
// the Crafty Clicks postcode / address finder functionality into these
// checkout extensions :
//
// ONE CLICK FAST CHECKOUT extension for Magento 
// http://www.vinagento.com/one-click-fast-checkout-step.html
//
// Provided by www.CraftyClicks.co.uk
//
// Requires standard CraftyClicks JS - tested with v4.9.2 
//
// If you copy/use/modify this code - please keep this
// comment header in place
//
// Copyright (c) 2009-2012 Crafty Clicks (http://www.craftyclicks.com)
//
// This code relies on prototype js, you must have a reasonably recent version loaded 
// in your template. Magento should include it as standard.
// 
// If you need any help, contact support@craftyclicks.co.uk - we will help!
//	
**********************************************************************************/    
function CraftyClicksMagentoClass () {
	this.prefix = ""; 
	this.fields = { "postcode_id"	: "", // required
					"company_id"	: "", // optional 
					"street1_id"	: "", // required	
					"street2_id"	: "", // optional
					"street3_id"	: "", // optional
					"street4_id"	: "", // optional
					"town_id"	: "", // required
					"county_id"	: "", // optional 
					"country_id"	: "" // required
					};

	this.current_setup			= 'initial'; // can be 'uk' or 'non_uk'
	this.uk_postcode_width		= ''
	this.old_postcode_width 	= '';
	this.cp_obj					= 0;
	this.div_depth				= 0;
	this.li_class 				= "clearfix";
	
	this.elem_move = function(e1, e2) {
	    e1.insert({after : e2}); 
	}

	// test div depth - some magento templates wrap fields in two layers of div in a li
	this.set_div_depth = function() {
		if ($(this.fields.postcode_id).up('div', 1).descendantOf($(this.fields.postcode_id).up('li'))) {
			this.div_depth = 1;
		}
	}
	
	this.rearrange_fields = function() {
		var fields = this.fields;

		// check we have a postcode field
		if ($(fields.postcode_id)) {
			if ('' != $(fields.town_id).getValue()) {
				_cp_hide_fields = false;
			}

			// postcode could be bundled with country 
			if ($(fields.country_id).up('.fields') && $(fields.country_id).up('.fields') == $(fields.postcode_id).up('.fields')) {
				// move country on its own and put it at the top
				$(fields.country_id).up('.os-address-set').down('.fields').insert( {before: $(fields.country_id).up('.field') } );
				$(fields.country_id).up('.field').className = "fields";
			}
			
			if ($(fields.company_id)) {
				$(fields.company_id).up('.fields').addClassName(this.prefix+'_cp_address_class');
			}
			if ($(fields.street1_id)) {
				$(fields.street1_id).up('.fields').addClassName(this.prefix+'_cp_address_class');
			}
			if ($(fields.street2_id)) {
				$(fields.street2_id).up('.fields').addClassName(this.prefix+'_cp_address_class');
			}
			if ($(fields.street3_id)) {
				$(fields.street3_id).up('.fields').addClassName(this.prefix+'_cp_address_class');
			}
			if ($(fields.street4_id)) {
				$(fields.street4_id).up('.fields').addClassName(this.prefix+'_cp_address_class');
			}
			if ($(fields.town_id)) {
				$(fields.town_id).up('.fields').addClassName(this.prefix+'_cp_address_class');
			}
			if ($(fields.county_id)) {
				$(fields.county_id).up('.fields').addClassName(this.prefix+'_cp_address_class');
			}

			return (true);
		} else {
			return (false);
		}
	}

	this.setup_for_uk = function() {
		// check if we need to do anything
		if ('uk' != this.current_setup) {
			// do the magic for UK
			// move postcode to the uk position after the country
			$(this.fields.country_id).up('.fields').insert(  {after: $(this.fields.postcode_id).up('.fields')} );
			// add result box
			if (!$(this.prefix+'_cp_result_display')) {
				var tmp_html = '<div class="fields" style="display: none"><div class="input-box" id="'+this.prefix+'_cp_result_display">&nbsp;</div></div>'
				$(this.fields.postcode_id).up('.fields').insert( {after: tmp_html} );
			}
			// show result box
			$(this.prefix+"_cp_result_display").up('.fields').show();
			// add button
			if (!$(this.prefix+'_cp_button_div_id')) {
				var tmp_html = '';
				tmp_html = '<div class="field eightcol last" id="'+this.prefix+'_cp_button_div_id"><label>&nbsp;</label><div>';
				if ('' != _cp_button_image) {
					tmp_html += '<img style="cursor: pointer;" src="'+_cp_button_image+'" id="'+this.prefix+'_cp_button_id" class="'+_cp_button_class+'" title="'+_cp_button_text+'"/>';
				} else {
					tmp_html += '<button type="button" id="'+this.prefix+'_cp_button_id" class="'+_cp_button_class+'"><span><span>'+_cp_button_text+'</span></span></button>';
				}
				tmp_html += '</div>';							
				$(this.fields.postcode_id).up('.field').insert( {after : tmp_html} );
				$(this.prefix+"_cp_button_id").observe('click', this.button_clicked.bindAsEventListener(this));
			}
			// show button 
			$(this.prefix+"_cp_button_div_id").show();
			
			// shrink postcode field if needed
			if ('' != this.uk_postcode_width) {
				this.old_postcode_width = $(this.fields.postcode_id).getStyle("width");
				$(this.fields.postcode_id).setStyle({width: this.uk_postcode_width});
			}

			// hide county if requested (and if it exists in the html at all)
			if (_cp_hide_county) {
				ne = $(this.fields.county_id);
				if (ne) {
					ne.up('.field').hide();
				}
			}
		}	
		
		if ('initial' == this.current_setup && _cp_hide_fields) {
			// first time and default to UK, hide address fields
			$$('.'+this.prefix+'_cp_address_class').invoke('hide');
		}
		
		// set state
		this.current_setup = 'uk';
	}	

	this.setup_for_non_uk = function() {
		// check if we need to do anything
		if ('non_uk' != this.current_setup) {
			// hide result box (if it exist already)
			if ($(this.prefix+"_cp_result_display")) {
				this.cp_obj.update_res(null);
				$(this.prefix+"_cp_result_display").up('.fields').hide();
			}
			// hide button (if it exist already)
			if ($(this.prefix+"_cp_button_div_id")) {
				$(this.prefix+"_cp_button_div_id").hide();
			}
			// move postcode to the non-uk position after the town/county li
			if ($(this.fields.county_id)) {
				$(this.fields.county_id).up('.fields').insert(  {after: $(this.fields.postcode_id).up('.fields')} );
			} else if ($(this.fields.town_id)) {
				$(this.fields.town_id).up('.fields').insert(  {after: $(this.fields.postcode_id).up('.fields')} );
			}
			// restore postcode field width if needed
			if ('' != this.old_postcode_width) {
				$(this.fields.postcode_id).setStyle({width: this.old_postcode_width});
			}
			// show county if it was hidden (and exists in the html at all)
			if (_cp_hide_county) {
				ne = $(this.fields.county_id);
				if (ne) {
					ne.up('.field', this.div_depth).show();
				}
			}
						
			// show all other addres lines
			$$('.'+this.prefix+'_cp_address_class').invoke('show');
			// set state
			this.current_setup = 'non_uk';
		}	
	}	

	this.add_lookup = function(setup) {
		cp_obj = CraftyPostcodeCreate();
		this.cp_obj = cp_obj;
	 	// config 
	 	this.prefix = setup.prefix;
	 	this.fields = setup.fields;
		cp_obj.set("access_token", _cp_token_fe); 
		cp_obj.set("res_autoselect", "0");
		cp_obj.set("result_elem_id", this.prefix+"_cp_result_display");
		cp_obj.set("form", "");
		cp_obj.set("elem_company"  , this.fields.company_id); // optional
		cp_obj.set("elem_street1"  , this.fields.street1_id);
		cp_obj.set("elem_street2"  , this.fields.street2_id);
		cp_obj.set("elem_street3"  , this.fields.street3_id); 
		cp_obj.set("elem_town"     , this.fields.town_id);
		if (_cp_hide_county) {
			cp_obj.set("elem_county"   , ""); // optional
		} else {
			cp_obj.set("elem_county"   , this.fields.county_id); // optional
		}
		cp_obj.set("elem_postcode" , this.fields.postcode_id);
		cp_obj.set("single_res_autoselect" , 1); // don't show a drop down box if only one matching address is found
		cp_obj.set("max_width" , _cp_result_box_width);
		if (1 < _cp_result_box_height) {
			cp_obj.set("first_res_line", ""); 
			cp_obj.set("max_lines" , _cp_result_box_height);
		} else {
			cp_obj.set("first_res_line", "----- please select your address ----"); 
			cp_obj.set("max_lines" , 1);
		}
		cp_obj.set("busy_img_url" , _cp_busy_img_url);
		cp_obj.set("hide_result" , _cp_clear_result);
		cp_obj.set("traditional_county" , 1);
		cp_obj.set("on_result_ready", this.result_ready.bindAsEventListener(this));
		cp_obj.set("on_result_selected", this.result_selected.bindAsEventListener(this));
		cp_obj.set("on_error", this.result_error.bindAsEventListener(this));
		cp_obj.set("first_res_line", _cp_1st_res_line);
		cp_obj.set("err_msg1", _cp_err_msg1);
		cp_obj.set("err_msg2", _cp_err_msg2);
		cp_obj.set("err_msg3", _cp_err_msg3);
		cp_obj.set("err_msg4", _cp_err_msg4);
		// initial page setup
		this.set_div_depth();
		if (this.rearrange_fields()) {
			if (_cp_enable_for_uk_only) {
				this.country_changed();
				$(this.fields.country_id).observe('change', this.country_changed.bindAsEventListener(this));
			} else {
				this.setup_for_uk();
			}
		} else {
//			alert ('Postcode Lookup could not be added!');
		}
	}

	this.country_changed = function(e) {
		// show postcode lookup for:
		// "GB" UK
		// "JE" Jersey
		// "GG" Guernsey
		// "IM" Isle of Man
		var curr_country = $(this.fields.country_id).getValue();
		if ('GB' == curr_country || 'JE' == curr_country || 'GG' == curr_country || 'IM' == curr_country) {
			this.setup_for_uk();
		} else {
			this.setup_for_non_uk();
		}
	}

	this.button_clicked = function(e) {
		if ('' != _cp_error_class) $(this.prefix+'_cp_result_display').removeClassName(_cp_error_class);
		this.cp_obj.doLookup();
	}
	
	this.result_ready = function() {
/*
		if ($(this.fields.street1_id)) {
			$(this.fields.street1_id).stopObserving(); $(this.fields.street1_id).value = "";
		}
*/
	}
		
	this.result_selected = function() {
		if (_cp_clear_result) this.cp_obj.update_res(null);
		$$('.'+this.prefix+'_cp_address_class').invoke('show');

		switch($(this.fields.postcode_id).getValue().substring(0,2)){
			case "GY":
				$(this.fields.country_id).setValue("GG");
				break;
			case "JE":
				$(this.fields.country_id).setValue("JE");
				break;
			case "IM":				
				$(this.fields.country_id).setValue("IM");
				break;
			default:
				$(this.fields.country_id).setValue("GB");
				break;
		}
/* need to fix this - how do we hide the $%^&% labels ??!!!
		var tmp_val;
		if ($(this.fields.company_id)) {
			tmp_val = $(this.fields.company_id).getValue(); 
			$(this.fields.company_id).focus();
			$(this.fields.company_id).value = '';
			$(this.fields.company_id).fire('change');
			$(this.fields.company_id).value = tmp_val;
			$(this.fields.company_id).fire('change');
		}
		if ($(this.fields.street1_id)) {
			$(this.fields.street1_id).focus(); $(this.fields.street1_id).fire('change');
		}
		if ($(this.fields.street2_id)) {
			$(this.fields.street2_id).focus(); $(this.fields.street2_id).fire('change');
		}
		if ($(this.fields.street3_id)) {
			$(this.fields.street3_id).focus(); //simulate('change');
		}
		if ($(this.fields.street4_id)) {
			$(this.fields.street4_id).focus(); //simulate('change');
		}
		if ($(this.fields.town_id)) {
			$(this.fields.town_id).focus(); //simulate('change');
		}
		if ($(this.fields.county_id)) {
			$(this.fields.county_id).focus(); //simulate('change');
		}
*/
	}
	
	this.result_error = function() { 
		$$('.'+this.prefix+'_cp_address_class').invoke('show');
		if ('' != _cp_error_class) $(this.prefix+'_cp_result_display').addClassName(_cp_error_class);
	}
}

document.observe("dom:loaded", function() {
	
	if (!_cp_integrate) return;
	
	if ($("billing:postcode")) {
		var cc1 = new CraftyClicksMagentoClass();
		cc1.add_lookup({
		"prefix"				: "billing", 
		"fields"				: { "postcode_id" : "billing:postcode", 
									"company_id"  : "billing:company", 
									"street1_id"  : "billing:street1", 
									"street2_id"  : "billing:street2", 
									"street3_id"  : "billing:street3", 
									"street4_id"  : "billing:street4", 
									"town_id"	  : "billing:city",
									"county_id"   : "billing:region", 
									"country_id"  : "billing:country_id" }
		});
	}
	
	if ($("shipping:postcode")) {
		var cc2 = new CraftyClicksMagentoClass();
		cc2.add_lookup({
		"prefix"				: "shipping", 
		"fields"				: { "postcode_id" : "shipping:postcode", 
									"company_id"  : "shipping:company", 
									"street1_id"  : "shipping:street1", 
									"street2_id"  : "shipping:street2", 
									"street3_id"  : "shipping:street3", 
									"street4_id"  : "shipping:street4", 
									"town_id"	  : "shipping:city",
									"county_id"   : "shipping:region", 
									"country_id"  : "shipping:country_id" }
		});
	}

});

