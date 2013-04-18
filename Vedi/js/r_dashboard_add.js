function moreAfterClick() {
	var result= {};
	result['type']="timestamp";
	result['number']=15;
	result['latestQuestionTime']=getCookie('oldestQuestionTime');
	result['scroll']="before";
	jQuery.get( 
             "/qcorner/questions/timestamp",
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
				for (var j=9;j<19;j++)
				{
					$('#best_answer_' + j).after('<br><h4 class="entry-title"><span class="title" style="text-align:left"><a href="single.html" id="question_' + (j+1) + '">'+data[j-9].question.string+'</a></span><br><br><span class="entry-commentsn"><a href="#" title="Upvotes" class="poshytip">'+data[j-9].question.voteUp+'</a></span><span class="entry-commentsq"><a href="#" title="Downvotes" class="poshytip">'+data[j-9].question.voteDown+'</a></span><span class="entry-commentfav"><a href="#" title="Add to Favorites" class="poshytip" onClick="addToFavorites('+data[j-9].question.QID+')">*</a></span><span class="entry-commentsv"><a href="#" title="Visit later" class="poshytip" onClick="visitLater('+data[j-9].question.QID+')">*</a></span></h4><div class="entry-excerpt" id="best_answer_'+(j+1)+'" style="text-align:left">'+answers[j-9]+'</div>');	
				}
				},
			 "json"
          );
	
}

function postQuestion() {
	var questionString = jQuery("textarea#questionString").val();
    //$("#tag_1").children("option").filter(":selected").text();
	var tag = new Array();
	var transferTag = new Array();
	
	transferTag[0]=$('#tag_1').val();
	transferTag[1]=$('#tag_2').val();
	transferTag[2]=$('#tag_3').val();
	
	
	for (var i=0;i<5;i++)
	{ 
		tag[i]=$('#tag_'+(i+4)).val();
	}
	
	
	
	var count = 3;
	for (var j=0;j<5;j++)
	{ 
		if(!tag[j] || 0 === tag[j].length){ 
		
		}	
		else
		{
			transferTag[count++] = tag[j];
	
		}
	}
		
	var difficultyLevel = $('#difficultyLevel').val();
	
	var dln;
	if(difficultyLevel === 'Very easy') {
		dln = 0;
	} else if (difficultyLevel === 'Easy') {
		dln = 1;
	} else if (difficultyLevel === 'Medium') {
		dln = 2;
	} else if (difficultyLevel === 'Hard') {
		dln = 3;
	} else if (difficultyLevel === 'Very hard') {
		dln = 4;
	} 
	
	var result= {};
	result['questionString']=questionString;
	result['tags']=transferTag;
	result['difficultyLevel']=dln;
	$.post( 
             "/qcorner/question/post",
			 result,
             function(data) {
				if(data.head.status == 200)
					alert("The question has been put up for review");
				else if (data.head.status == 409)
					alert("Invalid tags");
				else 
					alert("There was an error");
				//$('#bodyslider').toggle;
				location.reload();
			 },
			 "json"
	);
	
}

function editProfile() {
	window.location = 'editprofile.html';
}

function buttonClick(){
	var tag = $('#s').val();
	setCookie('tag', tag, 1);
	var saved_tag = getCookie('username');
	if(undefined != saved_tag)
		window.location = 'tag_signed.html';
	else
		window.location = 'tag_unsigned.html';
};

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
		case 'con':
			setCookie('list', 'con', 1);
			window.location = "list.html";
			break;
		case 'myq':
			setCookie('list', 'myq', 1);
			window.location = "list.html";
			break;
		case 'sub':
			setCookie('list', 'sub', 1);
			window.location = "list.html";
			break;

	}
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

function visitLater(e)
{
	var result= {};
	result['QID']=e;
	$.get( 
             "/qcorner/addWatchLater",
			 result,
             function(data) {
				if(data.head.status == 200) {
					alert("Added successfully");
					location.reload();	
				}
				else if (data.head.status == 409)
					alert("Already added");
				else if (data.head.status == 500)
					alert("Internal server error");
				else if (data.head.status == 206)
					alert("System error");
					
			 },
			 "json"
	);
}

function addToFavorites(e)
{
	var result= {};
	result['QID']=e;
	$.get( 
             "/qcorner/addFavourite",
			 result,
             function(data) {
				if(data.head.status == 200) {
					alert("Added successfully");
					location.reload();	
				}
				else if (data.head.status == 409)
					alert("Already added");
				else if (data.head.status == 500)
					alert("Internal server error");
				else if (data.head.status == 206)
					alert("System error");
					
			 },
			 "json"
	);
}


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
					window.location = 'dashboard.html';
				}
			},
		"json"
		);
}
