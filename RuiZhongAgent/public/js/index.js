$(window).ready(function(){
	$(".font_main")[0].style.marginLeft= "0";
	if (navigator.userAgent.indexOf('Mac OS X') !== -1) {
		
        $(".index>.right").addClass('mac');
        $(".mac").css("top","126px");
        $(".mac").css("right","220px");
        $(".mac>img").css("width","870px");
        $(".mac>img").css("height","550px");

} else {
      $(".index>.right").addClass('win');
}
})
