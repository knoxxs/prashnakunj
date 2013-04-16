function edit_button_click() {
	var firstName = document.getElementById('fname').value;
	var lastName = document.getElementById('lname').value;
	var email = document.getElementById('email').value;
	var phone = document.getElementById('phone').value;
	var dob = document.getElementById('dob').value;
	var gender = document.getElementById('gender').value;
	
	var interests = document.getElementById('interest').value;
	var country	= document.getElementById('country').value;
	var city = 	document.getElementById('city').value;
	var state = document.getElementById('state').value;
	var qualification = document.getElementById('quali').value;
	var affiliation = document.getElementById('affili').value;
	var aoe = document.getElementById('aoe').value;
	var secQ = document.getElementById('scope').value;
	var sa = document.getElementById('sa').value;
	
	
	var result = {};
	result['firstName']=firstName;
	result['lastName']=lastName;
	result['email']=email;
	result['phone']=phone;
	result['DOB']=dob;
	result['gender']=gender;
	result['affiliation']=affiliation;
	result['areasOfExpertise']=aoe;
	result['country']=country;
	result['state']=state;
	result['city']=city;
	result['interests']=interests;
	result['qualification']=qualification;
	result['securityQuestionID']=secQ;
	result['securityAnswer']=sa;
	
		jQuery.post( 
			"/qcorner/modifydetail",
			result,
			function(data) {
				if(data.head.status == 200) 
					window.location = 'dashboard.html';
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

function buttonClick(){
	var tag = $('#s').val();
	setCookie('tag', tag, 1);
	var saved_tag = getCookie('username');
	if(undefined != saved_tag)
		window.location = 'tag_signed.html';
	else
		window.location = 'tag_unsigned.html';
}
