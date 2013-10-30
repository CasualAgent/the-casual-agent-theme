function isScrolledIntoView(elem)
{
    var docViewTop = $(window).scrollTop();
    var docViewBottom = docViewTop + $(window).height();
    var elemTop = $(elem).offset().top;
    var elemBottom = elemTop + $(elem).height();
    return ((elemBottom >= docViewTop) && (elemTop <= docViewBottom) && (elemBottom <= docViewBottom) && (elemTop >= docViewTop));
}
$posts = jQuery("post-content.brick");
$(window).scroll(function() {    
	$posts.each(function(){
		$this = jQuery(this);
		
		if(isScrolledIntoView($this)){
        	$this.trigger('mouseenter');
		}else{
			$this.trigger('mouseleave');
		}    
	});
    
});