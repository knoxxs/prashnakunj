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
});

function listClick(e)
{
	switch(e.id)
	{
		case 'fav':
			setCookie('list', 'fav', 1);
			window.location = 'list.html';
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
function searchClick(){
	var tag = jQuery('#s').val();
	setCookie('tag', tag, 1);
	var saved_tag = getCookie('username');
	if(undefined != saved_tag)
	{
		window.location = 'tag_signed.html';
	}
	else
		window.location = 'tag_unsigned.html';
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