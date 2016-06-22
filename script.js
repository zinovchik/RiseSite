jQuery(document).ready(function(){
	
	//выделение выбраного типа файлов
	jQuery("#panel > a:not('#choise_none, #choise_rise')").click(function(){ 
		jQuery("input."+this.id).prop("checked", true);
	});
	
	//снимаем все выделение				    
	jQuery("#choise_none").click(function(){ 
		jQuery("input.checkbox").prop("checked", false);
	});
	
	//снимаем выделение с уже востановленых файлов				    
	jQuery("#choise_rise").click(function(){ 
		jQuery("input.checkbox.choise_rise").prop("checked", false);
	});
	
	//запуск постановления				    
	jQuery("#choise_start").click(function(){ 
		jQuery("#terminal").css("display", "block");
		
		var counter = 1;
		var all = jQuery('input.checkbox:checked').length;
		var progress = 0;
		var test = '';
		jQuery('input.checkbox:checked').each(function(i,elem) { 
		//test = test++; 
		tmp1 = this.value;
		tmp1=tmp1.replace(/&/g,'ampersandtoreplase');
		//tmp1 = encodeURIComponent(tmp1);
			jQuery("#terminal #progress_status").load("ajax.php"+"?url="+tmp1+"&limit=500&i="+i, function (response, status, xhr) {
				jQuery("#terminal #progress_list").append(response); 	
				jQuery("#terminal #progress_status").text("");	
				
				progress = Math.round(counter/all*100);
				counter=counter+1;
				jQuery("#terminal #progress_line div").css('width', progress+'%');	
				jQuery("#terminal #progress_line span").text(progress+" %");		
				
//if (counter > 1000){alert(counter);return;}				
								  	 
			})
		}); 	
		alert(counter+' are sending');				              
	});
	
	//закрыть окно терминала и очистить его				    
	jQuery("#terminal .close").click(function(){ 
		jQuery("#terminal").css("display", "none");
		jQuery("#terminal #progress_list").text("");
	});
	
	//выводит количество разных типов файлов				    
	jQuery("#panel > a#choise_html > span").text( jQuery(".choise_html").length);
	jQuery("#panel > a#choise_php > span").text( jQuery(".choise_php").length);
	jQuery("#panel > a#choise_css > span").text( jQuery(".choise_css").length);
  	jQuery("#panel > a#choise_js > span").text( jQuery(".choise_js").length);
	jQuery("#panel > a#choise_jpg > span").text( jQuery(".choise_jpg").length);
	jQuery("#panel > a#choise_png > span").text( jQuery(".choise_png").length);
  	jQuery("#panel > a#choise_gif > span").text( jQuery(".choise_gif").length);
  	jQuery("#panel > a#choise_pdf > span").text( jQuery(".choise_pdf").length);
  	jQuery("#panel > a#choise_else > span").text( jQuery(".choise_else").length);
  	jQuery("#panel > a#choise_rise > span").text( jQuery(".choise_rise").length);
  	jQuery("#panel > a#choise_none > span").text( jQuery(".checkbox").length);
				       
});