
$(document).ready(function(){  
var uName, pWord, eMail, secQ, secQA;
	  $("#login_button").click(function(event){
	  	uName = $('#login_username').val();
		pWord = $('#login_password').val();
		var result = {};
		result['userName']=uName;
		result['password']=pWord;
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
      });
	  $("#forgotp_un_button").click(function(event){
		uName = $('#forgotp_username').val();
		var result = {};
		result['userName']=uName;
		$.post( 
             "/qcorner/forgotpwd/checkuname",
			 result,
             function(data) {
				if(data.head.status==200) {
						window.location = 'login.html#torp';
						setCookie('userName', uName, 1);
						setCookie('securityQuestionNumber', data.body, 1);
						
						if (data.body == 0)	{
							document.getElementById('securityQ').innerHTML="What is your mother's maiden name?";
						} else if (data.body == 1) {
							document.getElementById('securityQ').innerHTML="Where did you first attend school?";
						} else if (data.body == 2) {
							document.getElementById('securityQ').innerHTML="What was the name of your first pet?";
						} else if (data.body == 3) {
							document.getElementById('securityQ').innerHTML="What was your first telephone number?";
						}
					}
					else
						{}
             },
			 "json"
          );
	  });
	  $("#resetp_button").click(function(event){
		uName = getCookie("userName");
		pWord = $('#newPassword').val();
		cWord = $('#confirmPassword').val();
		secQA = $('#securityQA').val();
		
		if (pWord === cWord)
		{
			
		}
		
		var result = {};
		result['userName']=uName;
		result['newPassword']=pWord;
		//"securityQuestionID":"",
		result['securityAnswer']=secQA;
		result['securityQuestionNumber']=getCookie('securityQuestionNumber');
		$.post( 
             "/qcorner/forgotpwd/updatepwd",
			 result,
             function(data) {
				
             },
			 "json"

          );
	  });
	  $("#forgotusername_button").click(function(event){
		eMail = $('#forgotusername_email').val();
		var result = {};
		result['email']=eMail;
		$.post( 
             "http://54.249.240.120/qcorner/",
			 result,
             function(data) {
				
             },
			 "json"

          );
	  });
	  $("#signup_button").click(function(event){
		var fName = $('#su_fname').val();
		var lName = $('#su_lname').val();
		var uName = $('#su_uname').val();
		var pWord = $('#su_pass').val();
		var cWord = $('#su_confirmpass').val();
		
		if(pWord != cWord) {
			
		}
		
		var eMail = $('#su_email').val();
		var phone = $('#su_phone').val();
		var dob = $('#su_dob').val();
		var gender = $('#su_gender').val();
		var intrsts = $('#su_intrst').val();
		var newIntrst = intrsts.split(",");
		var country = $('#su_country').val();
		var city = $('#su_city').val();
		var state = $('#su_state').val();
		var qual = $('#su_qual').val();
		var affili = $('#su_affili').val();
		var aoe = $('#su_aoe').val();
		var newAOE = aoe.split(",");
		var secQues = $('#su_secques').val();
		var secA = $('#su_secA').val();
		var rKey = $('#su_rkey').val();
		if(rKey==NULL)
			rKey = -1;
		var result = {};
		result['firstName']=fName;
		result['lastName']=lName;
		result['userName']=uName;
		result['password']=pWord;
		result['email']=eMail;
		result['phone']=phone;
		result['dob']=dob;
		result['gender']=gender;
		result['interest']=newIntrst;
		result['country']=country;
		result['city']=city;
		result['state']=state;
		result['qualification']=qual;
		result['affiliation']=affili;
		result['areasOfExpertise']=newAOE;
		result['securityQuestionID']=secQues;
		result['securityAnswer']=secA;
		result['reviewerKey']=rKey;
		
		$.post( 
             "http://54.249.240.120/qcorner/",
			 result,
             function(data) {
			
             },
			 "json"

          );
	  });
});