jQuery(document).ready(function(){
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
						
						for(var i=0;i<data.question.commentList.length;i++)
						{
							jQuery('#quesCommentList').append('<li style="border-bottom: 4px dotted rgb(72,72,72)" class="comment even thread-even depth-1" id="li-comment-'+i+'"><div id="div_ques_comment_'+i+'" class="comment-body clearfix"><img src="http://0.gravatar.com/avatar/4f64c9f81bb0d4ee969aaf7b4a5a6f40?s=35&amp;d=&amp;r=G" class="avatar avatar-35 photo" height="35" width="35" /><div class="comment-author vcard"><strong>'+data.question.commentList[i].userName+'</strong></div><div class="comment-meta commentmetadata"><span class="comment-date" id="ques_comment_'+i+'_date">'+data.question.commentList[i].timeStamp+'</span></div><h4 class="entry-title"><span class="entry-commentsn"><a href="#" id="ques_comment_'+i+'_upvote" title="Upvotes" class="poshytip">'+data.question.commentList[i].voteUp+'</a></span><span class="entry-commentsq"><a href="#" id="ques_comment_'+i+'_downvote" title="Downvotes" class="poshytip">'+data.question.commentList[i].voteDown+'</a></span><span class="entry-commentsp"><a href="#" title="Report abuse" class="poshytip">23</a></span></h4><div class="comment-inner" id="ques_comment_'+i+'"><p>'+data.question.commentList[i].string+'</p></div><br/></div></li>');
						}
						
						jQuery('#quesAnswer').html(data.bestAnswer.string);

						for(var i=0;i<data.suggestions.length;i++)
						{
							jQuery('#sugList').append('<li style="border-bottom: 4px dotted rgb(72,72,72)" class="comment even thread-even depth-1" id="li-sug-'+i+'"><div id="sug-div_'+i+'" class="comment-body clearfix"><img src="http://0.gravatar.com/avatar/4f64c9f81bb0d4ee969aaf7b4a5a6f40?s=35&amp;d=&amp;r=G" class="avatar avatar-35 photo" height="35" width="35" /><div class="comment-author vcard">'+data.suggestions[i].userName+'</div><div class="comment-meta commentmetadata"><span class="comment-date">'+data.suggestions[i].timeStamp+'</span></div><h4 class="entry-title"><span class="entry-commentsn"><a href="single.html#comments" title="Upvotes" class="poshytip">'+data.suggestions[i].voteUp+'</a></span><span class="entry-commentsq"><a href="single.html#comments" title="Downvotes" class="poshytip">'+data.suggestions[i].voteDown+'</a></span><span class="entry-commentsp"><a href="single.html#comments" title="Report abuse" class="poshytip">23</a></span></h4><div class="comment-inner"><p>'+data.suggestions[i].string+'</p></div><br/></div><div class="clearfix"></div></div>');						
						}
						
					},	
					"json"
				);
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