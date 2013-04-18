var saved_tag;
var forvar;
jQuery(document).ready(function(){
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
	tag_display();

	jQuery.get( 
             "/qcorner/list/favourite/timestamp",
             function(data) {
				if(data.body.length != 0)
				{
					document.getElementById('favorite_0').innerHTML=data.body[0].string;
					if(data.body.length < 3)
						forvar = data.body.length;
					else if (data.body.length >= 3)
						forvar = 3;
					for (var i=1;i<forvar;i++)
					{ 
						jQuery('#fav_list').append('<li><span id="favorite_'+(i)+'">'+data.body[i].string+'</span></li>');
					}
				}
				
			 },
			 "json"
	);

	jQuery.get( 
             "/qcorner/notifications",
             function(data) {
				document.getElementById('notif_number').innerHTML=data.body.length;
				if(data.body.length != 0)
				{
					document.getElementById('notif_0').innerHTML=data.body[0].string;
					for (var i=1;i<data.body.length;i++)
					{ 
						jQuery('#notifications').append('<li><span><span class="notifon" id="notif_'+(i)+'">'+data.body[i].string+'</span></span></li>');
					}
				}
				
			 },
			 "json"
	);



jQuery.get( 
             "/qcorner/list/subscriptionList",
             function(data) {
				if(data.body.length!=0)
				{	
					document.getElementById('subtag_0').innerHTML=data.body[0];
					if(data.body.length < 3)
						forvar = data.body.length;
					else if (data.body.length >= 3)
						forvar = 3;
					for (var i=1;i<forvar;i++)
					{ 
						jQuery('#sub_list').append('<li><span id="subtag_'+i+'">'+ data.body[i] +'</span></li>');	
					}
				}
				
			 },
			 "json"
	);



jQuery.get( 
             "/qcorner/list/myContributions",
             function(data) {
				if(data.body.length!=0)
				{	
					document.getElementById('contributed_0').innerHTML=data.body[0].string;
					if(data.body.length < 3)
						forvar = data.body.length;
					else if (data.body.length >= 3)
						forvar = 3;
					for (var i=1;i<forvar;i++)
					{ 
						jQuery('#contributed').append('<li><span id="contributed_'+i+'">'+ data.body[i].string +'</span></li>');	
					}
				}
				
			 },
			 "json"
	);


	jQuery.get( 
             "/qcorner/list/myQuestions",
             function(data) {
				if(data.body.length!=0)
				{	
					document.getElementById('myquestions_0').innerHTML=data.body[0].string;
					if(data.body.length < 3)
						forvar = data.body.length;
					else if (data.body.length >= 3)
						forvar = 3;
					for (var i=1;i<forvar;i++)
					{ 
						jQuery('#myquestions').append('<li><span id="myquestions_'+i+'">'+ data.body[i].string +'</span></li>');	
					}
				}
				
			 },
			 "json"
	);


	jQuery.get( 
             "/qcorner/list/watchLater/timestamp",
             function(data) {
				if(data.body.length != 0)
				{
					document.getElementById('wl_0').innerHTML=data.body[0].string;
					if(data.body.length < 3)
						forvar = data.body.length;
					else if (data.body.length >= 3)
						forvar = 3;
					for (var i=1;i<forvar;i++)
					{ 
						jQuery('#wl_list').append('<li><span id="wl_'+(i)+'">'+data.body[i].string+'</span></li>');
					}
				}
				
			 },
			 "json"
	);
	
	
	jQuery.get( 
             "/qcorner/list/history/timestamp",
             function(data) {
				if(data.body.length != 0)
				{
					document.getElementById('history_0').innerHTML=data.body[0].string;
					if(data.body.length < 3)
						forvar = data.body.length;
					else if (data.body.length >= 3)
						forvar = 3;
					for (var i=1;i<forvar;i++)
					{ 
						jQuery('#view_history').append('<li><span id="history_'+i+'">'+data.body[i].string+'</span></li>');
					}
				}
				
			 },
			 "json"
	);

});

function tag_display(){
	saved_tag = getCookie('tag');
	if(undefined != saved_tag)
		{
			var result = {};
			result['tag'] = saved_tag;
			jQuery.get( "/qcorner/search",result,function(data) {
				var username = getCookie('username');
				if(undefined != username){
					if(data.head.status == 200)
					{
						var t = "Tag: ";
						var r = t + saved_tag;
						jQuery('#s_tag_header').html(r);
						jQuery('#s_tag_header').after('<div id="s_question_0"><br><h4 class="entry-title"><span class="title" style="text-align:left"><a class="question" onClick="quesClick(this)" id="' + data.body[0].QID + '">'+ data.body[0].string +'</a></span><br><br><span class="entry-commentsn"><a title="Upvotes" class="poshytip" onClick="vote('+data.body[0].QID+', 1)">' + data.body[0].voteUp + '</a></span><span class="entry-commentsq"><a title="Downvotes" class="poshytip" onClick="vote('+data.body[0].QID+', -1)">' + data.body[0].voteDown + '</a></span><span class="entry-commentfav"><a href="#" title="Add to Favorites" class="poshytip" onClick="addToFavorites('+data.body[0].QID+')">*</a></span><span class="entry-commentsv"><a href="#" title="Visit later" class="poshytip" onClick="visitLater('+data.body[0].QID+')">*</a></span></h4></div>');
						for(var i=1; i<data.body.length; i++)
						{
							jQuery('#s_question_' + (i-1)).after('<div id="s_question_' + i + '"><br><h4 class="entry-title"><span class="title" style="text-align:left"><a class="question" onClick="quesClick(this)" id="' + data.body[i].QID + '">'+ data.body[i].string +'</a></span><br><br><span class="entry-commentsn"><a title="Upvotes" class="poshytip" onClick="vote('+data.body[i].QID+', 1)">' + data.body[i].voteUp + '</a></span><span class="entry-commentsq"><a title="Downvotes" class="poshytip" onClick="vote('+data.body[i].QID+', 1)">' + data.body[i].voteDown + '</a></span><span class="entry-commentfav"><a href="#" title="Add to Favorites" class="poshytip" onClick="addToFavorites('+data.body[i].QID+')">*</a></span><span class="entry-commentsv"><a href="#" title="Visit later" class="poshytip" onClick="visitLater('+data.body[i].QID+')">*</a></span></h4></div>');
						}
					}
					else if(data.head.status == 206)
					{
						jQuery('#s_tag_header').html("No questions with that tag were found. Try something else!");
					}
					else
						jQuery('#s_tag_header').html("There was some error. Try again!");
				}
				else{
					if(data.head.status == 200)
					{
						var t = "Tag: ";
						var r = t + saved_tag;
						jQuery('#u_tag_header').html(r);
						jQuery('#u_tag_header').after('<div id="u_question_0"><br><h4 class="entry-title"><span class="title"><a class="question" onClick="quesClick(this)" id="' + data.body[0].QID + '">'+ data.body[0].string +'</a></span></h4></div>');
						for(var i=1; i<data.body.length; i++)
						{
							jQuery('#u_question_' + (i-1)).after('<div id="u_question_0"><br><h4 class="entry-title"><span class="title"><a class="question" onClick="quesClick(this)" id="' + data.body[i].QID + '">'+ data.body[i].string +'</a></span></h4></div>');
						}
					}
					else if(data.head.status == 206)
					{
						jQuery('#u_tag_header').html("No questions with that tag were found. Try something else!");
					}
					else
						jQuery('#u_tag_header').html("There was some error. Try again!");
				}
             },"json");
		}
	else
		{}
}
function quesClick(e)
{
	setCookie('questionID', e.id, 1);
	var username = getCookie('username');
	var reviewer = getCookie('reviewer');
	if(reviewer== 'true')
	{
		window.location = 'question_reviewer.html';
	}
	else
	{
		if(undefined != username)
			window.location = 'question_signed.html';
		else
			window.location = 'question_unsigned.html';
	}
}
function listClick(e)
{
	switch(e.id)
	{
		case 'fav':
			setCookie('list', 'fav', 1);
			window.location = "list.html";
			break;
		case 'lat':
			setCookie('list', 'lat', 1);
			window.location = "list.html";
			break;
		case 'qvh':
			setCookie('list', 'qvh', 1);
			window.location = "list.html";
			break;
	}
}
function logout(){
	eraseCookie('username');
	eraseCookie('firstname');
	eraseCookie('lastname');
	eraseCookie('reputation');
	eraseCookie('city');
	eraseCookie('affiliation');
	eraseCookie('reviewer');
	eraseCookie('list');
	eraseCookie('questionID');
	eraseCookie('tag');
	jQuery.post(
			"/qcorner/logout",
			function(data)
			{
				alert("You've successfully logged out.");
			}
		);
}

function buttonClick(){
	var tag = jQuery('#s').val();
	setCookie('tag', tag, 1);
	var saved_tag = getCookie('username');
	if(undefined != saved_tag)
		window.location = 'tag_signed.html';
	else
		window.location = 'tag_unsigned.html';
};

function vote(e, f)
{
	var result = {};
	result['QID'] = e;
	result['nature'] = f;
	jQuery.get( 
             "/qcorner/question/vote",
			 result,
             function(data) {
				if(data.head.status == 200)
				{
					alert("Vote registered");
					location.reload();
				} else
				{
					alert("There was an error");
				}
			},
		"json"
		);
}

function visitLater(e)
{
	var result= {};
	result['QID']=e;
	jQuery.get( 
             "/qcorner/addWatchLater",
			 result,
             function(data) {
				alert(data.head.status);
				if(data.head.status == 200) {
					alert("Added successfully");
					location.reload();	
				}
				else if (data.head.status == 409)
					alert("Already added");
				else if (data.head.status == 500)
					alert("Internal server error");
				else if (data.head.status == 206)
					alert("There was an error");
					
			 },
			 "json"
	);
}

function addToFavorites(e)
{
	var result= {};
	result['QID']=e;
	jQuery.get( 
             "/qcorner/addFavourite",
			 result,
             function(data) {
				alert(data.head.status);
				if(data.head.status == 200) {
					alert("Added successfully");
					location.reload();	
				}
				else if (data.head.status == 409)
					alert("Already added");
				else if (data.head.status == 500)
					alert("Internal server error");
				else if (data.head.status == 206)
					alert("There was an error");
					
			 },
			 "json"
	);
}

