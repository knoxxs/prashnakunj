function edit_button_click() {
	var firstName = document.getElementById('fname').value;
	var lastName = document.getElementById('lname').value;
	var email = document.getElementById('email').value;
	var phone = document.getElementById('phone').value;
	var dob = document.getElementById('dob').value;
	var gender = document.getElementById('gender').value;
	var pass = document.getElementById('pass').value;
	var interests = document.getElementById('interest').value;
	var country	= document.getElementById('country').value;
	var city = 	document.getElementById('city').value;
	var state = document.getElementById('state').value;
	var qualification = document.getElementById('quali').value;
	var affiliation = document.getElementById('affili').value;
	var aoe = document.getElementById('aoe').value;
	var secQ = document.getElementById('scope').value;
	var sa = document.getElementById('sa').value;
	
	setCookie('firstname', firstName);
	setCookie('lastname', lastName);
	setCookie('firstname', firstName);
	setCookie('city', city);
	setCookie('affiliation', affiliation);
	
	
		jQuery.validator.addMethod("NumbersOnly", function(value, element){
			return this.optional(element) || /^[?+\-0-9]+$/i.test(value);}
			, "Phone must contain only numbers, + and -.");
			
		jQuery.validator.addMethod("DatesOnly", function(value, element){
			return this.optional(element) || /^[?\0-9]+$/i.test(value);}
			, "Dates must follow the format yyyy/mm/dd");
		/*$.validator.addMethod("onlytext", function(value, element){
			return this.optional(element) || /^[a-zA-z]+$/i.test(value);}
			, "This must contain only letters");
		*/
		var check_username=/^[a-zA-Z0-9_]+$/;
		var check_text=/^[a-zA-Z]+$/;
		
		var temp10 = jQuery("#edit_form").valid();
		
			
		//text
		if(check_text.test(firstName) == true)
		{
			
			
		//alert("here");
		if(check_text.test(lastName) == true)
		{
			
			
		if(pass.length!=0)
		{
		
		
		if(email.length<45 && email!="")
		{
			
		
		if(phone.length==10 || phone.length ==15)
		{
			
		
		if(dob!="")
		{
		
		
		
		//text
		if(check_text.test(city) == true)
		{
			
			
		
		if(check_text.test(state) == true)
		{
			
			
		if(sa!="")
		{
		
		}else{alert("Input Security Answer");}
			}else{alert("Input Only Text for State");
			}
			}else{alert("Input Only Text for City");
			}
			}else{alert("Input birthday");}
			}else{alert("Input 10 digit Number");
			}
			}else{alert("Input Email please");
			}
			}else{alert("Input Pass please");}
			}else{alert("Input Only Text in Last Name");
			}
			}else{alert("Input Only Text in First Name");
			}
	
	
	
	
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
	if(temp10){
	jQuery.post( 
		"/qcorner/modifydetail",
		result,
		function(data) {
			if(data.head.status == 200) 
				window.location = 'dashboard.html';
			},
		"json"
	); }
				
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
	var tag = jQuery('#s').val();
	setCookie('tag', tag, 1);
	var saved_tag = getCookie('username');
	if(undefined != saved_tag)
		window.location = 'tag_signed.html';
	else
		window.location = 'tag_unsigned.html';
}

