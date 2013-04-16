var saved_tag;
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
});

function tag_display(){
	saved_tag = getCookie('tag');
	if(undefined != saved_tag)
		{
			alert(saved_tag);
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
						jQuery('#s_tag_header').after('<div id="s_question_0"><br><h4 class="entry-title"><span class="title"><a class="question" onClick="quesClick(this)" id="' + data.body[0].QID + '">'+ data.body[0].string +'</a></span><span class="entry-commentsn"><a href="single.html#comments" title="Upvotes" class="poshytip">' + data.body[0].voteUp + '</a></span><span class="entry-commentsq"><a href="single.html#comments" title="Downvotes" class="poshytip">' + data.body[0].voteDown + '</a></span><span class="entry-commentsp"><a href="single.html#comments" title="Reported abuses" class="poshytip">23</a></span></h4></div>');
						for(var i=1; i<data.body.length; i++)
						{
							jQuery('#s_question_' + (i-1)).after('<div id="s_question_' + i + '"><br><h4 class="entry-title"><span class="title"><a class="question" onClick="quesClick(this)" id="' + data.body[i].QID + '">'+ data.body[i].string +'</a></span><span class="entry-commentsn"><a href="single.html#comments" title="Upvotes" class="poshytip">' + data.body[i].voteUp + '</a></span><span class="entry-commentsq"><a href="single.html#comments" title="Downvotes" class="poshytip">' + data.body[i].voteDown + '</a></span><span class="entry-commentsp"><a href="single.html#comments" title="Reported abuses" class="poshytip">23</a></span></h4></div>');
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