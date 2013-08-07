/*
// <Coman Javascript file>
//
// version: <1.0>

*/
$(document).ready( function (){
	$('body.page-template-team-php .topslide').css('top',$('.team-listing').height()-171);
	$('#menu-main-navigation li:last-child').addClass('last');
	$('.content  .member-detail p:first').addClass('first');
	$('.kontaktinfo .alignleft').eq(0).addClass('sci');
	$('.kontaktinfo .alignleft').eq(2).addClass('last');
	$('.team-listing li:nth-child(3n)').addClass('last');
	$('.servicelist li').eq(0).children('span').addClass('first');
	$('.servicelist li').eq(1).addClass('middle');
	$('.servicelist li:last-child').addClass('last');
	$('.postentrylist li:last-child').addClass('last');
	$('.servicelist li img').parent('a').addClass('image')
	$('.ubersicht-listing .entry:last-child').addClass('last');
	$('.testimonial-comment .entry:first-child').addClass('openmore');
	$('.testimonial-comment p:last-child').addClass('moredetail');
	$('.children li:last-child').addClass('last');
$(".top").click(function(){
		var top = $("#header").offset();
		$("html, body").animate({scrollTop: top.top}, 1500);
	});
	$("#bgcolor").val($('#select_bg_color_team').val());
	$("#bgcolor_aktuelles").val($('#select_bg_color_aktuelles').val());
	
$('#bgcolor').change(function(){
	$('#select_bg_color_team').attr('value',$(this).val());
});

$('#bgcolor_aktuelles').change(function(){
	$('#select_bg_color_aktuelles').attr('value',$(this).val());
});

$('#footer-widget-area li:last-child').addClass('last');
$('#contact-widget li.widget-container:last-child').addClass('last');

  
    $("#bookmarkme").click(function() {
      if (window.sidebar) { // Mozilla Firefox Bookmark
        window.sidebar.addPanel(location.href,document.title,"");
      } else if(window.external) { // IE Favorite
        window.external.AddFavorite(location.href,document.title); }
      else if(window.opera && window.print) { // Opera Hotlist
        this.title=document.title;
        return true;
  }
});

	 $(".testimonial-comment .openmore").find(".moredetail").show();
	 $(".testimonial-comment .openmore .more").addClass("scrolldown");
	 
	 $(".testimonial-comment .entry .more").click(function()
	 {
		if($(this).hasClass("scrolldown"))
		{
			$(this).removeClass("scrolldown");
			$(this).parents(".testimonial-comment .entry").find(".moredetail").slideUp('slow');	
		}
		else
		{
			$(this).parents(".testimonial-comment").find(".moredetail").slideUp(500);
			$(this).parents(".testimonial-comment").find(".more").removeClass("scrolldown");
			$(this).addClass("scrolldown");
			$(this).parents(".testimonial-comment .entry").find(".moredetail").slideDown('slow');
		}
		
	});
	$('.widgetlinks ul li.last iframe').hover(function(){
		$(this).parent('div').css('text-decoration','underline');
	}, function(){
		$('.widgetlinks ul li.last div').css('text-decoration','none');
	});
	$('.widgetlinks ul li.last div.textwidget').hover(function(){
		$(this).css('text-decoration','underline');
	}, function(){
		$('.widgetlinks ul li.last div').css('text-decoration','none');
	});
	/*$('#list1b').accordion({
			autoheight: false
		});
*/

	/*$('.leftwrapbg.violet, .leftwrapbg.gray, .leftwrapbg.yellow').hide();
		$('.slideshow li').each(function() {
			if($(this).css('display') == "list-item")
			{
				$('.leftwrapbg.'+$(this).attr('class')).show();
			}
			else{
				$('.leftwrapbg.'+$(this).attr('class')).hide();
				}
	});

$('.gallery-cantrol a, .pager li a').click(function(){
	$('.leftwrapbg.violet, .leftwrapbg.gray, .leftwrapbg.yellow').fadeOut();
	$('.slideshow li').each(function() {
		if($(this).css('display') == "block")
		{
			$('.leftwrapbg.'+$(this).attr('class')).fadeIn();
		}
	});
    });*/
});