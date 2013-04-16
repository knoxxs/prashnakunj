var saved_list;
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
	saved_list = getCookie('list');
	listDisplay(saved_list);
});
function listClick(e)
{
	switch(e.id)
	{
		case 'fav':
			listDisplay('fav');
			break;
		case 'lat':
			listDisplay('lat');
			break;
		case 'qvh':
			listDisplay('qvh');
			break;
	}
}

function listDisplay(x){
	switch(x){
		case 'fav': 
			jQuery.get(
					"/qcorner/list/favourite/timestamp",
					function(data){
						if(data.head.status == 200)
						{
							jQuery('#posts').html('<h1 class="title" id="list_header" style="margin-left: 250px">List Empty. Relogin!</h1><br><br><button style="color: rgb(249,249,238); background-color: rgb(72,72,72); border: 0px; width: 700px; font-size: 18px; padding: 5px; border-radius: 5px" type="submit">More</button><br><br>');
							jQuery('#list_header').html("Favourite");
							if(data.body.length != 0)
							{
								jQuery('#list_header').after('<div id="list_question_0"><br><h4 class="entry-title"><span class="title"><a class="question" onClick="quesClick(this)" id="' + data.body[0].QID + '">'+ data.body[0].string +'</a></span><span class="entry-commentsn"><a href="single.html#comments" title="Upvotes" class="poshytip">' + data.body[0].voteUp + '</a></span><span class="entry-commentsq"><a href="single.html#comments" title="Downvotes" class="poshytip">' + data.body[0].voteDown + '</a></span><span class="entry-commentsp"><a href="single.html#comments" title="Reported abuses" class="poshytip">23</a></span></h4></div>');
								for(var i=1; i<data.body.length; i++)
								{
									jQuery('#list_question_' + (i-1)).after('<div id="list_question_' + i + '"><br><h4 class="entry-title"><span class="title"><a class="question" onClick="quesClick(this)" id="'+ data.body[i].QID +'"">'+ data.body[i].string +'</a></span><span class="entry-commentsn"><a href="single.html#comments" title="Upvotes" class="poshytip">' + data.body[i].voteUp + '</a></span><span class="entry-commentsq"><a href="single.html#comments" title="Downvotes" class="poshytip">' + data.body[i].voteDown + '</a></span><span class="entry-commentsp"><a href="single.html#comments" title="Reported abuses" class="poshytip">23</a></span></h4></div>');
								}
							}
							else
								jQuery('#list_header').after('<br><h4 class="entry-title"><span class="title">No questions have been added to this list!</span></h4>');
						}
						else
						{
							jQuery('#posts').html('<h1 class="title" id="list_header" style="margin-left: 250px">List Empty. Relogin!</h1><br><br><button style="color: rgb(249,249,238); background-color: rgb(72,72,72); border: 0px; width: 700px; font-size: 18px; padding: 5px; border-radius: 5px" type="submit">More</button><br><br>');
							jQuery('#list_header').html("There was an error. Try again!");
						}
					},
					"json"
				);
			break;
		case 'lat': 
			jQuery.get(
					"/qcorner/list/watchLater/timestamp",
					function(data){
						if(data.head.status == 200)
						{
							jQuery('#posts').html('<h1 class="title" id="list_header" style="margin-left: 250px">List Empty. Relogin!</h1><br><br><button style="color: rgb(249,249,238); background-color: rgb(72,72,72); border: 0px; width: 700px; font-size: 18px; padding: 5px; border-radius: 5px" type="submit">More</button><br><br>');
							jQuery('#list_header').html("View Later");
							if(data.body.length != 0)
							{
								jQuery('#list_header').after('<div id="list_question_0"><br><h4 class="entry-title"><span class="title"><a class="question" onClick="quesClick(this)" id="' + data.body[0].QID + '">'+ data.body[0].string +'</a></span><span class="entry-commentsn"><a href="single.html#comments" title="Upvotes" class="poshytip">' + data.body[0].voteUp + '</a></span><span class="entry-commentsq"><a href="single.html#comments" title="Downvotes" class="poshytip">' + data.body[0].voteDown + '</a></span><span class="entry-commentsp"><a href="single.html#comments" title="Reported abuses" class="poshytip">23</a></span></h4></div>');
								for(var i=1; i<data.body.length; i++)
								{
									jQuery('#list_question_' + (i-1)).after('<div id="list_question_' + i + '"><br><h4 class="entry-title"><span class="title"><a class="question" onClick="quesClick(this)" id="'+ data.body[i].QID +'"">'+ data.body[i].string +'</a></span><span class="entry-commentsn"><a href="single.html#comments" title="Upvotes" class="poshytip">' + data.body[i].voteUp + '</a></span><span class="entry-commentsq"><a href="single.html#comments" title="Downvotes" class="poshytip">' + data.body[i].voteDown + '</a></span><span class="entry-commentsp"><a href="single.html#comments" title="Reported abuses" class="poshytip">23</a></span></h4></div>');
								}
							}
							else
								jQuery('#list_header').after('<br><h4 class="entry-title"><span class="title">No questions have been added to this list!</span></h4>');
						}
						else
						{
							jQuery('#posts').html('<h1 class="title" id="list_header" style="margin-left: 250px">List Empty. Relogin!</h1><br><br><button style="color: rgb(249,249,238); background-color: rgb(72,72,72); border: 0px; width: 700px; font-size: 18px; padding: 5px; border-radius: 5px" type="submit">More</button><br><br>');
							jQuery('#list_header').html("There was an error. Try again!");
						}
					},
					"json"
				);
			break;
		case 'qvh': 
			jQuery.get(
					"/qcorner/list/history/timestamp",
					function(data){
						if(data.head.status == 200)
						{
							jQuery('#posts').html('<h1 class="title" id="list_header" style="margin-left: 250px">List Empty. Relogin!</h1><br><br><button style="color: rgb(249,249,238); background-color: rgb(72,72,72); border: 0px; width: 700px; font-size: 18px; padding: 5px; border-radius: 5px" type="submit">More</button><br><br>');
							jQuery('#list_header').html("Question View History");
							if(data.body.length != 0)
							{
								jQuery('#list_header').after('<div id="list_question_0"><br><h4 class="entry-title"><span class="title"><a class="question" onClick="quesClick(this)" id="' + data.body[0].QID + '">'+ data.body[0].string +'</a></span><span class="entry-commentsn"><a href="single.html#comments" title="Upvotes" class="poshytip">' + data.body[0].voteUp + '</a></span><span class="entry-commentsq"><a href="single.html#comments" title="Downvotes" class="poshytip">' + data.body[0].voteDown + '</a></span><span class="entry-commentsp"><a href="single.html#comments" title="Reported abuses" class="poshytip">23</a></span></h4></div>');
								for(var i=1; i<data.body.length; i++)
								{
									jQuery('#list_question_' + (i-1)).after('<div id="list_question_' + i + '"><br><h4 class="entry-title"><span class="title"><a class="question" onClick="quesClick(this)" id="'+ data.body[i].QID +'"">'+ data.body[i].string +'</a></span><span class="entry-commentsn"><a href="single.html#comments" title="Upvotes" class="poshytip">' + data.body[i].voteUp + '</a></span><span class="entry-commentsq"><a href="single.html#comments" title="Downvotes" class="poshytip">' + data.body[i].voteDown + '</a></span><span class="entry-commentsp"><a href="single.html#comments" title="Reported abuses" class="poshytip">23</a></span></h4></div>');
								}
							}
							else
								jQuery('#list_header').after('<br><h4 class="entry-title"><span class="title">No questions have been added to this list!</span></h4>');
						}
						else
						{
							jQuery('#posts').html('<h1 class="title" id="list_header" style="margin-left: 250px">List Empty. Relogin!</h1><br><br><button style="color: rgb(249,249,238); background-color: rgb(72,72,72); border: 0px; width: 700px; font-size: 18px; padding: 5px; border-radius: 5px" type="submit">More</button><br><br>');
							jQuery('#list_header').html("There was an error. Try again!");
						}
					},
					"json"
				);
			break;

	}
}

function quesClick(e)
{
	setCookie('questionID', e.id, 1);
	var username = getCookie('username');
	var reviewer = getCookie('reviewer');
	if(reviewer=='true')
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