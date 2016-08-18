jQuery(document).ready(function($) {
	function gmwRemoveCf() {
		$('.gmw-custom-fields-remove-btn').click(function() {
			$(this).closest('.gmw-cf-single-wrapper').remove();
		});
	}
	
	function gmwDateType() {
		$('.gmw-cf-type').change(function() {
			dateType = $(this).closest('.gmw-cf-single-wrapper').find('.gmw-cf-date-type');
			if ( $(this).val() == 'DATE' ) dateType.show();
			else dateType.hide();
		});
	}
	
	$('.gmw-custom-fields-btn').click(function() {
		
		cfId = $(this).attr('form_id');
		formSection = $(this).attr('section');
		
		var cfName = $(this).closest('.gmw-custom-fields').find('.gmw-custom-fields-select').val();

		var cfCreate = '';
		cfCreate += '<tr class="gmw-cf-single-wrapper">';
		cfCreate += 	'<td class="cf-title">';
		cfCreate += 		'<input type="hidden" value="1" name="gmw_forms['+cfId+']['+formSection+'][custom_fields]['+cfName+'][on]" />'+cfName;
		cfCreate +=		'</td>';
		cfCreate +=		'<td style="min-width: 150px;">';
	 	cfCreate += 		'<input type="text" name="gmw_forms['+cfId+']['+formSection+'][custom_fields]['+cfName+'][name]" size="20" />';
		cfCreate +=		'</td>';
		cfCreate += 	'<td>';
	 	cfCreate += 		'<select class="gmw-cf-type" name="gmw_forms['+cfId+']['+formSection+'][custom_fields]['+cfName+'][type]">';
	 	cfCreate += 			'<option value="CHAR">CHAR</option>';
	 	cfCreate += 			'<option value="NUMERIC">NUMERIC</option>';
	 	cfCreate += 			'<option value="DATE">DATE</option>';
	 	cfCreate += 		'</select>';
	 	cfCreate += 	'</td>';
		cfCreate += 	'<td>';
	 	cfCreate +=			'<select name="gmw_forms['+cfId+']['+formSection+'][custom_fields]['+cfName+'][compare]">';
	 	cfCreate += 			'<option value="&#61;">&#61;</option>';
	 	cfCreate += 			'<option value="&#33;&#61;">&#33;&#61;</option>';
	 	cfCreate += 			'<option value="&#62;">&#62;</option>';
	 	cfCreate += 			'<option value="&#62;&#61;">&#62;&#61;</option>';
	 	cfCreate += 			'<option value="&#60;">&#60;</option>';
	 	cfCreate += 			'<option value="&#60;&#61;">&#60;&#61;</option>';
	 	cfCreate += 			'<option value="LIKE">LIKE</option>';
	 	cfCreate += 			'<option value="NOT LIKE">NOT LIKE</option>';
	 	cfCreate += 			'<option value="BETWEEN">BETWEEN</option>';
	 	cfCreate += 			'<option value="NOT BETWEEN">NOT BETWEEN</option>';
	 	cfCreate += 		'</select>';
	 	cfCreate += 	'</td>';
	 	cfCreate += 	'<td style="width:110px;">';
	 	cfCreate += 		'<select class="gmw-cf-date-type" name="gmw_forms['+cfId+']['+formSection+'][custom_fields]['+cfName+'][date_type]" style="display:none">';
	 	cfCreate += 			'<option value="mm/dd/yyyy">MM/DD/YYYY</option>';
	 	cfCreate += 			'<option value="dd/mm/yyyy">DD/MM/YYYY</option>';
	 	cfCreate += 			'<option value="yyyy/mm/dd">YYYY/MM/DD</option>';
	 	cfCreate += 		'</select>';
	 	cfCreate += 	'</td>';
	 	cfCreate += 	'<td>';
	 	cfCreate += 		'<input type="button" value="Delete" class="button action gmw-custom-fields-remove-btn">';
		cfCreate += 	'</td>';
	 	cfCreate += '</tr>';
	 	
		$(this).closest('.gmw-custom-fields').find('.gmw-cf-holder').append(cfCreate);

		gmwRemoveCf();
		gmwDateType();
	 });

	gmwRemoveCf();
	gmwDateType();
	
	if ( $('#gmw-af input[type="radio"]:checked').val() == 'multiple' ) {
		$('#gmw-af-single').hide();
		$('#gmw-af-multiple').show();
	}
	
	$('.gmw-st-btns').change(function() {
		var thisHide = $(this).closest('.gmw-single-taxonomy').find('.gmw-st-settings');
		if ( $(this).val() == 'na') {
			if ( thisHide.is(':visible') ) thisHide.slideToggle();
		} else if ( thisHide.is(':visible') ) { } else { thisHide.slideToggle(); }
	});
	
	$('.gmw-saf input[type="radio"]:checked').each(function() {
		gmwThis = $(this).closest('.gmw-saf');
		if ( $(this).val() == 'exclude' || $('#gmw-af input[type="radio"]:checked').val() != 'multiple' ) {	
			gmwThis.find('.gmw-saf-settings').hide();
			gmwThis.find('.gmw-saf-default').hide();
		} else if ( $(this).val() == 'include' ) {
			if ( gmwThis.find('.gmw-saf-settings').is(':hidden') ) gmwThis.find('.gmw-saf-settings').slideToggle();
			if ( gmwThis.find('.gmw-saf-default').is(':visible') ) gmwThis.find('.gmw-saf-default').slideToggle();
		} else if ( $(this).val() == 'default' ) {
			if ( gmwThis.find('.gmw-saf-settings').is(':visible') ) gmwThis.find('.gmw-saf-settings').slideToggle();
			if ( gmwThis.find('.gmw-saf-default').is(':hidden') ) gmwThis.find('.gmw-saf-default').slideToggle();
		}
	});
	
	$('.gmw-saf-btn').change(function() {
		gmwThis = $(this).closest('.gmw-saf');
		if ( $(this).val() == 'exclude' ) {
			if ( gmwThis.find('.gmw-saf-settings').is(':visible') ) gmwThis.find('.gmw-saf-settings').slideToggle();
			if ( gmwThis.find('.gmw-saf-default').is(':visible') ) gmwThis.find('.gmw-saf-default').slideToggle();
		} else if ( $(this).val() == 'include' ) {
			if ( gmwThis.find('.gmw-saf-settings').is(':hidden') ) gmwThis.find('.gmw-saf-settings').slideToggle();
			if ( gmwThis.find('.gmw-saf-default').is(':visible') ) gmwThis.find('.gmw-saf-default').slideToggle();
		} else if ( $(this).val() == 'default' ) {
			if ( gmwThis.find('.gmw-saf-settings').is(':visible') ) gmwThis.find('.gmw-saf-settings').slideToggle();
			if ( gmwThis.find('.gmw-saf-default').is(':hidden') ) gmwThis.find('.gmw-saf-default').slideToggle();
		}	
	});
	
	$('.gmw-af-buttons').change(function() {
		$('#gmw-af-single').slideToggle();
		$('#gmw-af-multiple').slideToggle();
	});

	if ( $('#gmw-ks input[type="radio"]:checked').val() != 'dont' ) {
		$('#gmw-ks-title').show();
	}
	
	$('.gmw-ks-buttons').change(function() {
		if ( $('#gmw-ks input[type="radio"]:checked').val() == 'dont' && $('#gmw-ks-title').is(':visible') ) {
			$('#gmw-ks-title').slideToggle();
		} else if ( $('#gmw-ks input[type="radio"]:checked').val() != 'dont' && $('#gmw-ks-title').is(':hidden') ) {
			$('#gmw-ks-title').slideToggle();
		}
	});
});