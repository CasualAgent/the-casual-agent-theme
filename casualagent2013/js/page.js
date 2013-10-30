jQuery(document).ready(function(){
	var $bricks = jQuery('div.brick-wall');


	$bricks.each(function(){
		var $cnt = jQuery(this);
		
		
		console.debug($cnt);
		
		$cnt.imagesLoaded(function(){
			var width = $cnt.width();
			var cols = Math.floor(width/300);
			$cnt.masonry({
				columnWidth: width/cols,
				itemSelector: '.brick',
				isResizeBound: true
			});
		});
		
			
		
		/*Masonry.data(this).on('layoutComplete', function( msnry, items ) {
			console.debug('layoutComplete');
			var runLayout = false;
			
			items.forEach(function(item){
				
				if(item.size.height <=0){
					jQuery(item.element).resize(function(){
						item.layout.layout();	
					});
				}	
			});
			
		});*/
	});
		
	$posts = jQuery('.post-content.brick');/*.hover(function(){
		$this = jQuery(this);
	//	$overlay = $this.parent().children('.overlay');
		$overlay = $this.children('.overlay');
		
		if($overlay.css('display')=='none'){
			$this.toggleClass('grey-out', false);
			$overlay.show();
			$excerpt = $overlay.children('.excerpt');
			if($overlay.data('hasLayout') != 'yes'){
				$excerpt = $overlay.children('.excerpt');
				$title = $overlay.children('.title');
				$action = $overlay.children('.action');
				var h = $overlay.height() - $title.outerHeight() - $action.outerHeight();
				$excerpt.height(h+"px");
		//		$tags = $overlay.children('.tags');
				//$excerpt.height($overlay.height()-$title.height()-$tags.height()-5);
				//$excerpt.height($overlay.height()-$title.outerHeight()-48);		
//					$excerpt.css('max-height', $title.height()+"px");
				$overlay.data('hasLayout', 'yes');
			}	
		}			
	},function(){
		
		$this = jQuery(this);
		$this.toggleClass('grey-out', true);
		$overlay = $this.children('.overlay');
		if($overlay.css('display')!='none'){
			$overlay.hide();
		}
		
	});*/
	var navstr = JSON.stringify(navigator.platform);
	$("#navexport").html(navstr);
	
	var meta = $.browser;
	meta.platform = navigator.platform;
	meta.product = navigator.product;
	meta.productSub = navigator.productSub;
	
	//alert(JSON.stringify(meta));
	
	//alert(screen.width+" x "+screen.height);
	
	function isScrolledIntoView(elem){
	    var docViewTop = $(document).scrollTop()+$('#header').height();
	    var docViewBottom = docViewTop + $(elem).height();
	    var elemTop = $(elem).offset().top;
	    var elemBottom = elemTop + $(elem).height();
	  
	  	return (elemTop >= docViewTop) && (elemTop <= docViewBottom);
//	    return ((elemBottom >= docViewTop) && (elemBottom <= docViewBottom) && (elemTop >= docViewTop) && (elemTop <= docViewBottom)); 
	  //  return ((elemBottom >= docViewTop) && (elemTop <= docViewBottom) && (elemBottom <= docViewBottom) && (elemTop >= docViewTop));
	}
	
	function is_touch_device() {
		return 'ontouchstart' in window // works on most browsers 
		|| 'onmsgesturechange' in window; // works on ie10
	};

	function scrollHandler(){
		var $items = jQuery(this);
				
		if($(document).scrollTop() == 0){
		//	$items.trigger('mouseleave');		
		//	$($items[0]).trigger('mouseenter');
			return $items;
		}else if($(document).scrollTop() >= ($(document).height()-$(window).height()-$('#header').height())){
		//	$items.trigger('mouseleave');		
		//	$($item[$items.length-1]).trigger('mouseenter');	
			return $items;
		}else{
			return $items.each(function(){
				var $self = jQuery(this);
			
				if(isScrolledIntoView(this)){
		  //      	$self.trigger('mouseenter');
				}else{
			//		$self.trigger('mouseleave');
				}  
			});
		}
		
	}
	//if(screen.width<=480 && is_touch_device()){
		/*$posts.bind('onScroll.mobile', function(){

			$self = jQuery(this);
			
			if(isScrolledIntoView(this)){
	        	$self.trigger('mouseenter');
	        	$self.toggleClass('grey-out', false);
			}else{
				$self.trigger('mouseleave');
				$self.toggleClass('grey-out', true);
			}  
		});
		*/
		
		$posts.children(".overlay").click(function(){
			var $ex = jQuery(this);
			$ex.toggleClass('showExcerpt');
		});
		
		$posts.resize(function(){
			Masonry.data(this).layout();
		});
		
		$posts.bind('onScroll.mobile', scrollHandler);
		
		$posts.trigger('onScroll.mobile');
		$(document).scroll(function() {   
			$posts.trigger('onScroll.mobile');
		});
		
	//}
	
	jQuery(".menu-bttn").click(function(){
		$('.menu').toggle();
	});
});

function get_more_rss_posts(page){
	jQuery.ajax({
			  method: 'GET',
			  url: "/blog/wp-admin/admin-ajax.php",
			  data: {type:'content-box', action:'get_post_qry_results', args:{cat:window.location.pathname, paged:page, tax_query:[{
			      taxonomy:'post_tag',
			      field:'slug',
			      terms:['rss', 'rssfeed'],
			      operator:'IN'}]}},
			  complete: function(res){var items = jQuery(res.responseText); var m = $("div.brick-wall"); items.each(function(idx, item){ var sel = $(item).attr('id'); var el = $('#'+sel); if(el){m.masonry('remove', el[0]);} console.debug(item); });  items.appendTo(m); m.imagesLoaded(function(){m.masonry('addItems', items); m.masonry('layout');}); console.debug(m); console.debug(items);},
			  dataType: 'json'
			});	
}
	
	