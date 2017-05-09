
// desktop fixed-nav function
var triggerheight = $(window).height() * 0.1;
var $nav = $("#navigation");

$(window).scroll(function() {
  if (this.scrollY > triggerheight) {
    $nav.addClass("fixed-nav");
  } else {
    $nav.removeClass("fixed-nav");
  }
});


// smooth scrolling function
$("nav a").click(function(){
	var href = $.attr(this, 'href');

	if($("nav").hasClass("open")){
		$('html, body').animate({
	    	scrollTop: $($.attr(this, "href")).offset().top - 59
		}, 500, function(){
			window.location.hash = href;
		});

	} else {
		$('html, body').animate({
	    	scrollTop: $($.attr(this, "href")).offset().top
		}, 500, function(){
			window.location.hash = href;
		});
	}

	return false;
});



//mobile nav click events
$(".nav-toggle").click(function(){
	$(this).toggleClass('open');
	$("nav").toggleClass('open');
});

$(".nav-link").click(function(){
	$(".nav-toggle").removeClass('open');
	$("nav").removeClass('open');
});


//disable hover effects on touch devices
document.addEventListener('touchstart', function addtouchclass(e){
    document.documentElement.classList.add('is-touch');
    document.removeEventListener('touchstart', addtouchclass, false);
}, false);




