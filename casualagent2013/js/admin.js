function ajax_action(data, method, cb){
		cb = cb || function(){console.debug(arguments); window.location.reload();}
		
		if(typeof cb != 'function'){
			switch(cb){
				case 'reload': cb = function(){
							window.location.reload();
						}
					break;
				default:
					cb = function(){
						console.debug(arguments); 
						window.location.reload();
					}
			}
			console.debug(cb);
		}
		cbc = function(){console.debug(arguments);}
		
		
		method = method || 'GET';
		jQuery.ajax({
			  type: method,
			  url: "/blog/wp-admin/admin-ajax.php",
			  data: data,
			  success: cb,
			  complete: cbc,
			  dataType: 'json'
			});		
	}
jQuery(document).ready(function(){
	
jQuery('body').delegate(".ajax_action", 'click', function(){
		alert('test');	
		var $this = jQuery(this);	
		var data = $this.attr('data-args');
		data = JSON.parse(data);
		console.debug(data);
		ajax_action(data, 'GET','reload');
	});
});