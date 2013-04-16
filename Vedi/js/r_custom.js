// Custom jquery without conflict
$.noConflict();
jQuery(document).ready(function($){

	//##########################################
	// Accordion box
	//##########################################

	$('.accordion-container').hide(); 
	$('.accordion-trigger:first').addClass('active').next().show();
	$('.accordion-trigger').click(function(){
		if( $(this).next().is(':hidden') ) { 
			$('.accordion-trigger').removeClass('active').next().slideUp();
			$(this).toggleClass('active').next().slideDown();		}
		return false;
	});
	
	
	//##########################################
	// Tweet feed
	//##########################################
	
	$("#tweets").tweet({
        count: 3,
        username: "ansimuz"
    });


	//##########################################
	// Tabs
	//##########################################

    $(".tabs").tabs("div.panes > div", {effect: 'fade'});
    
    //##########################################
	// Slides
	//##########################################
	
	$(function(){
		$('.slides-js').slides({
			preload: true,
			preloadImage: 'img/loading.gif',
			play: 5000,
			pause: 2500,
			hoverPause: true
		});
	});
	
    
    //##########################################
	// PrettyPhoto
	//##########################################
	
	$("a[rel^='prettyPhoto']").prettyPhoto();

	//##########################################
	// Tool tips
	//##########################################
	
    $('.poshytip, #social-bar li a').poshytip({
    	className: 'tip-twitter',
		showTimeout: 1,
		alignTo: 'target',
		alignX: 'center',
		offsetY: 5,
		allowTipHover: false
    });
    
    $('.form-poshytip').poshytip({
		className: 'tip-twitter',
		showOn: 'focus',
		alignTo: 'target',
		alignX: 'right',
		alignY: 'center',
		offsetX: 5
	});
	
	
	//##########################################
	// Link button
	//##########################################

	$('.link-button').css( {backgroundPosition: "-776 0"} )
	.mouseover(function(){
		$(this).stop().animate(
			{backgroundPosition:"(0px 0px)"}, 
			{duration:300})
		})
	.mouseout(function(){
		$(this).stop().animate(
			{backgroundPosition:"(-776px 0px)"}, 
			{duration:300})
	});
	
	//##########################################
	// Expandable boxy
	//##########################################

	$('.boxy .more-info').click(function(){
		$(this).hide();
		$(this).parent().children(".less-info").show();
		$(this).parents().children(".boxy-content").slideDown();
		return false;
	});
	
	$('.boxy .less-info').click(function(){
		$(this).hide();
		$(this).parent().children(".more-info").show();
		$(this).parents().children(".boxy-content").slideUp();
		return false;
	});
	
	//##########################################
	// Nav menu
	//##########################################
	
	$("ul.sf-menu").superfish({ 
        animation: {height:'show'},   // slide-down effect without fade-in 
        delay:     500 ,              // 1.2 second delay on mouseout 
        autoArrows:  false,
        speed:         'fast'
    });
    
    //##########################################
	// Nav over
	//##########################################
	
	$("#nav>li>a").hover(function() {
		if( !$(this).parent().hasClass('current-menu-item') ){
			// on rollover	
			$(this).stop().animate({ 
				marginLeft: "7" 
			}, "fast");
		}
	} , function() { 
		// on out
		$(this).stop().animate({
			marginLeft: "0" 
		}, "fast");
	});
    
    
    //##########################################
	// sidebar over
	//##########################################
	
	// show/hide sidebar
	
	$("#sidebar-dock").hover(function(){
		$(this).children("#sidebar").stop().animate({
			marginLeft: 0
		}, 200);
		$("#sidebar-opener").stop().hide();
	} , function(){
		$(this).children("#sidebar").stop().animate({
			marginLeft: -220
		},200);	
		$("#sidebar-opener").stop().show();
	});
    
	
	// recent posts
	$('#sidebar .recent-posts .recent-thumb img').hover(function(){
		$(this).stop().animate({ opacity: "0.5"}, {duration: 300} ); 
			
		
	
	},function(){
		$(this).stop().animate({ opacity: "1"}, {duration: 300}); 
	});
	
	//##########################################
	// Work hover
	//##########################################
	
	$(".work a img").hover(function(){
		$(this).stop().animate({opacity: "0.7"}, 300);
	}, function(){
		$(this).stop().animate({opacity: "1"}, 300);
	});
	
	//##########################################
	// Entry hover
	//##########################################
	
	$("#posts .feature-image").hover(function(){
		$(this).children(".entry-buttons").stop().animate({
			height:'70px',
			marginTop: "-35"
		},100);
	},function(){
		$(this).children(".entry-buttons").stop().animate({
			height:'0px',
			marginTop: "0"
		},100);
	});
	
    //##########################################
	// Scroll to top
	//##########################################
	
	// default hidden
	$("#to-top").hide();

	
	$(window).scroll(function() {
    if ($(this).scrollTop() == 0) {
        $("#to-top:visible").fadeOut();
    }
    else {
        $("#to-top:hidden").fadeIn();
    }});
        
    $('#to-top').click(function(){
		$('html, body').animate({ scrollTop: 0 }, 300);
	});
	
	//##########################################
	// Comments switcher
	//##########################################

	var $comments_switcher = $(".show-comments");
	var $comments_holder = $(".comments-switcher");
	
	$comments_switcher.click(function(){
		if($comments_holder.css("display") == "block"){
			$comments_switcher.children("span").text("click to show");		
		}else{
			$comments_switcher.children("span").text("click to hide");
		}
		$comments_holder.slideToggle();
	});

	var result = {};
	result['userName']=getCookie("username");
	result['password']=getCookie("password");
	$.post( 
         "/qcorner/login",
		 result,
         function(data) {
			if(data.head.status==200)
			{
				setCookie('username', uName, 1);
				setCookie('firstname', data.body.firstName, 1);
				setCookie('lastname', data.body.lastName, 1);
				setCookie('reputation', data.body.reputation, 1);
				setCookie('city', data.body.city, 1);
				setCookie('affiliation', data.body.affiliation, 1);
				setCookie('reviewer', data.body.isReviewer, 1);
				window.location = 'dashboard.html';
			}
			else
				alert("Wrong username or password");
         },
		 "json"

      );

	var first = getCookie('firstname');
	var last = getCookie('lastname');
	var rep = getCookie('reputation');
	var city = getCookie('city');
	var affili = getCookie('affiliation');
	var rev = getCookie('reviewer');
	jQuery('#s_first').html(first);
	jQuery('#s_last').html(last);
	jQuery('#s_rep').html(rep);
	jQuery('#s_city').html(city);
	jQuery('#s_affili').html(affili);
	jQuery('#s_rev').html(rev);	

	var result= {};
	result['type']="timestamp";
	result['number']=10;
	result['latestQuestionTime']= -1;
	result['scroll']="after";
	$.get( 
             "/qcorner/questions",
			 result,
             function(data) {
				var answers = new Array();
				for(var i=0;i<data.length;i++)
				{
					if(data[i].bestAnswer != null)
					{
						answers[i]=data[i].bestAnswer.string;
					}
					else
					{
						answers[i]="Answer not available.";
					}
				}
				var answerString;
				$('#moreBefore').after('<br><h4 class="entry-title"><span class="title"><a onClick="quesClick(this)" id="' + data[0].question.QID + '">'+data[0].question.string+'</a></span><span class="entry-commentsn"><a href="#" title="Upvotes" class="poshytip" onClick="vote('+data[0].question.QID+', 1)">'+data[0].question.voteUp+'</a></span><span class="entry-commentsq"><a href="#" title="Downvotes" class="poshytip" onClick="vote('+data[0].question.QID+', -1)">'+data[0].question.voteDown+'</a></span><span class="entry-commentsp"><a href="#" title="Report abuse" class="poshytip">23</a></span></h4><div class="entry-excerpt" id="best_answer_1">'+answers[0]+'</div>');	
				for (var j=1;j<10;j++)
				{
					$('#best_answer_' + j).after('<br><h4 class="entry-title"><span class="title"><a onClick="quesClick(this)" id="' + data[j].question.QID + '">'+data[j].question.string+'</a></span><span class="entry-commentsn"><a href="#" title="Upvotes" class="poshytip" onClick="vote('+data[j].question.QID+', 1)">'+data[j].question.voteUp+'</a></span><span class="entry-commentsq"><a href="#" title="Downvotes" class="poshytip" onClick="vote('+data[j].question.QID+', -1)">'+data[j].question.voteDown+'</a></span><span class="entry-commentsp"><a href="#" title="Report abuse" class="poshytip">23</a></span></h4><div class="entry-excerpt" id="best_answer_'+(j+1)+'">'+answers[j]+'</div>');	
				}
				setCookie('oldestQuestionTime', data[9].question.timeStamp, 1);
				setCookie('latestQuestionTime', data[0].question.timeStamp, 1);
			 },
			 "json"
	);
	
	$.get( 
             "/qcorner/list/favourite/timestamp",
             function(data) {
				if(data.body.length != 0)
				{
					document.getElementById('favorite_0').innerHTML=data.body[0].string;
					for (var i=1;i<data.body.length;i++)
					{ 
						jQuery('#fav_list').append('<li><a href="#" id="favorite_'+(i)+'">'+data.body[i].string+'</a></li>');
					}
				}
				
			 },
			 "json"
	);

$.get( 
             "/qcorner/notifications",
             function(data) {
				document.getElementById('notif_number').innerHTML=data.body.length;
				if(data.body.length != 0)
				{
					document.getElementById('notif_0').innerHTML=data.body[0].string;
					for (var i=1;i<data.body.length;i++)
					{ 
						jQuery('#notifications').append('<li><a href="#"><span class="notifon" id="notif_'+(i)+'">'+data.body[i].string+'</span></a></li>');
					}
				}
				
			 },
			 "json"
	);



$.get( 
             "/qcorner/list/subscriptionList",
             function(data) {
				if(data.body.length!=0)
				{	
					document.getElementById('subtag_0').innerHTML=data.body[0];
					
					for (var i=1;i<data.body.length;i++)
					{ 
						jQuery('#sub_list').append('<li><a href="#" id="subtag_'+i+'">'+ data.body[i] +'</a></li>');	
					}
				}
				
			 },
			 "json"
	);



$.get( 
             "/qcorner/list/myContributions",
             function(data) {
				if(data.body.length!=0)
				{	
					document.getElementById('contributed_0').innerHTML=data.body[0].string;
					
					for (var i=1;i<data.body.length;i++)
					{ 
						jQuery('#contributed').append('<li><a href="#" id="contributed_'+i+'">'+ data.body[i].string +'</a></li>');	
					}
				}
				
			 },
			 "json"
	);


	$.get( 
             "/qcorner/list/myQuestions",
             function(data) {
				if(data.body.length!=0)
				{	
					document.getElementById('myquestions_0').innerHTML=data.body[0].string;
					
					for (var i=1;i<data.body.length;i++)
					{ 
						jQuery('#myquestions').append('<li><a href="#" id="myquestions_'+i+'">'+ data.body[i].string +'</a></li>');	
					}
				}
				
			 },
			 "json"
	);


	$.get( 
             "/qcorner/list/watchLater/timestamp",
             function(data) {
				if(data.body.length != 0)
				{
					document.getElementById('wl_0').innerHTML=data.body[0].string;
					for (var i=1;i<data.body.length;i++)
					{ 
						jQuery('#wl_list').append('<li><a href="#" id="wl_'+(i)+'">'+data.body[i].string+'</a></li>');
					}
				}
				
			 },
			 "json"
	);
	
	
	$.get( 
             "/qcorner/list/history/timestamp",
             function(data) {
				if(data.body.length != 0)
				{
					document.getElementById('history_0').innerHTML=data.body[0].string;
					for (var i=1;i<data.body.length;i++)
					{ 
						jQuery('#view_history').append('<li><a href="#" id="history_'+(i)+'">'+data.body[i].string+'</a></li>');
					}
				}
				
			 },
			 "json"
	);

	var firstName=getCookie('firstName');
	var lastName=getCookie('lastName');
	var affiliation=getCookie('affiliation');
	var city=getCookie('city');
	var reputation=getCookie('reputation');
	
	document.getElementById('firstName_lastName').innerHTML=firstName+lastName;
	document.getElementById('city').innerHTML=city;
	document.getElementById('affiliation').innerHTML=affiliation;
	document.getElementById('reputation').innerHTML="Reputation: " + reputation;
					

}); // close jquery

// create HTML 5 elements
document.createElement("article");
document.createElement("footer");
document.createElement("header");
document.createElement("hgroup");
document.createElement("nav");

// search clearance	

function defaultInput(target, string){
	if((target).value == string){(target).value=''}
}

function clearInput(target, string){
	if((target).value == ''){(target).value=string}
}

