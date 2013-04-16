var qID;
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
	qID = getCookie('questionID');
	quesDisplay();
});
function quesDisplay()
{
	qID = getCookie('questionID');
	var a = "/qcorner/question/" ;
	var b = a + qID;
	jQuery.get(
					b,
					function(data)
					{
						jQuery('#quesTitle').html(data.question.string);
						jQuery('#quesUpVotes').html(data.question.voteUp);
						var alFav = data.question.alreadyFav;
						if(alFav==true)
						{
							jQuery('#quesFav').remove();
						}
						
						for(var i=0;i<data.question.commentList.length;i++)
						{
							jQuery('#quesCommentList').append('<li style="border-bottom: 4px dotted rgb(72,72,72)" class="comment even thread-even depth-1" id="li-comment-'+i+'"><div id="div_ques_comment_'+i+'" class="comment-body clearfix"><img src="http://0.gravatar.com/avatar/4f64c9f81bb0d4ee969aaf7b4a5a6f40?s=35&amp;d=&amp;r=G" class="avatar avatar-35 photo" height="35" width="35" /><div class="comment-author vcard"><strong>'+data.question.commentList[i].userName+'</strong></div><div class="comment-meta commentmetadata"><span class="comment-date" id="ques_comment_'+i+'_date">'+data.question.commentList[i].timeStamp+'</span></div><h4 class="entry-title"><span class="entry-commentsn"><a href="#" id="ques_comment_'+i+'_upvote" title="Upvotes" class="poshytip">'+data.question.commentList[i].voteUp+'</a></span><span class="entry-commentsq"><a href="#" id="ques_comment_'+i+'_downvote" title="Downvotes" class="poshytip">'+data.question.commentList[i].voteDown+'</a></span><span class="entry-commentsp"><a href="#" title="Report abuse" class="poshytip">23</a></span></h4><div class="comment-inner" id="ques_comment_'+i+'"><p>'+data.question.commentList[i].string+'</p></div><br/></div></li>');
						}

						jQuery('#quesCommentList').append('<div id="respond"><form id="commentform"><h5>Comment on the question:</h5><textarea name="comment" id="new_comment" style="height: 30px; width: 650px; border: 1px dotted rgb(72,72,72);" tabindex="4"></textarea><p><input name="submit" id="submit" tabindex="5" value="Post" onClick="commentPost()"/></p><input type="hidden" name="comment_post_ID" value="" id="comment_post_ID" /><input type="hidden" name="comment_parent" id="comment_parent" value="0" />	</form></div><div class="clearfix"></div>');
						
						if(data.bestAnswer != null)
						{
							jQuery('#quesAnswer').html(data.bestAnswer.string);
						}

						if(data.suggestions == null)
						{
							jQuery('#sugList').html('<div id="respond"><form id="commentform"><h5>Post a suggestion:</h5><textarea name="comment" id="new_suggestion" style="height: 30px; width: 650px; border: 1px dotted rgb(72,72,72);" tabindex="4"></textarea><p><input name="submit" id="submit" tabindex="5" value="Post" onClick="suggPost()"/></p><input type="hidden" name="comment_post_ID" value="" id="comment_post_ID" /><input type="hidden" name="comment_parent" id="comment_parent" value="0" />	</form></div><div class="clearfix"></div>');
						}
						else
						{
							for(var i=0;i<data.suggestions.length;i++)
							{
								jQuery('#sugList').append('<li style="border-bottom: 4px dotted rgb(72,72,72)" class="comment even thread-even depth-1" id="li-sug-'+i+'"><div id="sug-div_'+i+'" class="comment-body clearfix"><img src="http://0.gravatar.com/avatar/4f64c9f81bb0d4ee969aaf7b4a5a6f40?s=35&amp;d=&amp;r=G" class="avatar avatar-35 photo" height="35" width="35" /><div class="comment-author vcard">'+data.suggestions[i].userName+'</div><div class="comment-meta commentmetadata"><span class="comment-date">'+data.suggestions[i].timeStamp+'</span></div><h4 class="entry-title"><span class="entry-commentsn"><a href="single.html#comments" title="Upvotes" class="poshytip">'+data.suggestions[i].voteUp+'</a></span><span class="entry-commentsq"><a href="single.html#comments" title="Downvotes" class="poshytip">'+data.suggestions[i].voteDown+'</a></span><span class="entry-commentsp"><a href="single.html#comments" title="Report abuse" class="poshytip">23</a></span></h4><div class="comment-inner"><p>'+data.suggestions[i].string+'</p></div><br/></div><!-- Comments on Suggestion --><div id="qcomments" ></div><div id="qcomments-wrap"><ol class="commentlist" id="sug_com_list_'+i+'"></ol></div><div class="clearfix"></div></div>');						
							}
								jQuery('#sugList').append('<div id="respond"><form id="commentform"><h5>Post a suggestion:</h5><textarea name="comment" id="new_suggestion" style="height: 30px; width: 650px; border: 1px dotted rgb(72,72,72);" tabindex="4"></textarea><p><input name="submit" id="submit" tabindex="5" value="Post" onClick="suggPost()"/></p><input type="hidden" name="comment_post_ID" value="" id="comment_post_ID" /><input type="hidden" name="comment_parent" id="comment_parent" value="0" />	</form></div><div class="clearfix"></div>');
						}

						for(var i=0;i<=data.suggestions.length;i++)
						{
							for(var j=0;j<data.suggestions[i].commentList.length;j++)
							{
								jQuery('#sug_com_list_' + i).append('<li style="border-bottom: 4px solid rgb(72,72,72); border-top: 4px solid rgb(72,72,72); margin-left: 100px" class="comment-body" id="li-sug-com-'+i+'"><div id="sug-com-div_'+i+'" class="comment-body clearfix"><img src="http://0.gravatar.com/avatar/4f64c9f81bb0d4ee969aaf7b4a5a6f40?s=35&amp;d=&amp;r=G" class="avatar avatar-35 photo" height="35" width="35" /><div class="comment-author vcard">'+data.suggestions[i].commentList[j].userName+'</div><div class="comment-meta commentmetadata"><span class="comment-date">'+data.suggestions[i].commentList[j].timeStamp+'</span></div><h4 class="entry-title"><span class="entry-commentsn"><a href="single.html#comments" title="Upvotes" class="poshytip">'+data.suggestions[i].commentList[j].voteUp+'</a></span><span class="entry-commentsq"><a href="single.html#comments" title="Downvotes" class="poshytip">'+data.suggestions[i].commentList[j].voteDown+'</a></span><span class="entry-commentsp"><a href="single.html#comments" title="Report abuse" class="poshytip">23</a></span></h4><div class="comment-inner"><p>'+data.suggestions[i].commentList[j].string+'</p></div><br/></div></div>');
							}
						}

					},	
					"json"
				);
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
//New Functions
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
function commentPost()
{
	var x = jQuery('#new_comment').val();
	result = {};
	result['id']=getCookie('questionID');
	result['string']=x;
	jQuery.post(
			"/qcorner/question/qcomment/post",
			result,
			function(data)
			{
				if(data.head.status == 200)
				{
					window.location = 'question_signed.html';
				}
				else
					alert("Comment not posted.");
			}, 
			"json"	
		);
}
function suggPost()
{
	var x = jQuery('#new_suggestion').val();
	result = {};
	result['QID']=getCookie('questionID');
	result['suggestionString']=x;
	jQuery.post(
			"/qcorner/question/suggestion/post",
			result,
			function(data)
			{
				if(data.head.status == 200)
				{
					window.location = 'question_signed.html';
				}
				else
					alert("Suggestion not posted.");
			}, 
			"json"	
		);
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