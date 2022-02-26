$('#scrolltotop').click(function(){
	$('html,body').animate({scrollTop: 0}, 'slow');
});
$(window).scroll(function(){
	if($(window).scrollTop()<500){
		$('#scrolltotop').fadeOut();
	}else{
		$('#scrolltotop').fadeIn();
	}
});