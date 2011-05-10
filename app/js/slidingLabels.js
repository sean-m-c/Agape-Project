$(function(){
$('label').each(function(){
	var restingPosition = '5px';

	// style the label with JS for progressive enhancement
	$(this).css({
		 	'position' : 'absolute',
	 		'top' : '2px',
			'left' : restingPosition,
			'display' : 'inline',
    		        'z-index' : '99'
	});

	var inputval = $(this).next().val();

	// grab the label width, then add 5 pixels to it
	var labelwidth = $(this).width();
	var labelmove = labelwidth + 5 +'px';

	// onload, check if a field is filled out, if so, move the label out of the way
	if(inputval !== ''){
		$(this).stop().animate({ 'left':'-'+labelmove }, 1);
	}

	// if the input is empty on focus move the label to the left
	// if it's empty on blur, move it back
	$('input, textarea').focus(function(){
		var label = $(this).prev('label');
		var width = $(label).width();
		var adjust = width + 5 + 'px';
		var value = $(this).val();

		if(value == ''){
			label.stop().animate({ 'left':'-'+adjust }, 'fast');
		} else {
			label.css({ 'left':'-'+adjust });
		}
	}).blur(function(){
		var label = $(this).prev('label');
		var value = $(this).val();

		if(value == ''){
			label.stop().animate({ 'left':restingPosition }, 'fast');
		}

	});
}); // End "each" statement
}); // End loaded jQuery