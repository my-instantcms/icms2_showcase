$(document).ready(function(){

	var select = $('select.makeMeFancy');

	var selectBoxContainer = $('<div>',{
		width		: select.outerWidth(),
		class		: 'tzSelect',
		html		: '<div class="selectBox"></div>'
	});

	var dropDown = $('<ul>',{class:'dropDown'});
	var selectBox = selectBoxContainer.find('.selectBox');
	
	select.find('option').each(function(i){
		var option = $(this);
		
		selectBox.html(select.find('option:selected').text());
		
		if(option.data('skip')){
			return true;
		}
		
		var disabled = option.attr('disabled') ? true : false;

		var li = $('<li>',{
			class:'sc_v_id_' + option.val(),
			html:	'<img src="' + option.data('icon') + '" /><span>' + option.text() + '</span>'
		});
		
		if (icms.showcase.seted_variant && icms.showcase.seted_variant == option.val()){
			li.addClass('is_v_active');
		}

		if (disabled){
			li.addClass('is_v_disabled');
		} else {
			li.click(function(){
				
				selectBox.html(option.text());
				$('li', dropDown).removeClass('is_v_active');
				li.addClass('is_v_active');
				
				if (!$('.variants_opened').length){
					dropDown.trigger('hide');
				}

				select.val(option.val());
				selectBox.removeClass('sc_error_empty');
				icms.showcase.setVariant(select);
				
				return false;
			});
		}
		
		dropDown.append(li);
	});
	
	selectBoxContainer.append(dropDown.hide());
	select.hide().after(selectBoxContainer);
	
	dropDown.bind('show',function(){
		
		if(dropDown.is(':animated')){
			return false;
		}
		
		selectBox.addClass('expanded');
		dropDown.slideDown();
		
	}).bind('hide',function(){
		
		if(dropDown.is(':animated')){
			return false;
		}
		
		selectBox.removeClass('expanded');
		dropDown.slideUp();
		
	}).bind('toggle',function(){
		if(selectBox.hasClass('expanded')){
			dropDown.trigger('hide');
		}
		else dropDown.trigger('show');
	});
	
	selectBox.click(function(){
		dropDown.trigger('toggle');
		return false;
	});
	
	$(document).click(function(e){
		if (!$('.variants_opened').length){
			var container = $(".tzSelect li.is_v_disabled");
			if(!container.is(e.target) && container.has(e.target).length === 0){dropDown.trigger('hide');}
		}
	});
});