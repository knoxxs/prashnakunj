
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
					alert("Success");
					window.location = 'dashboard.html';
				}
				else
					alert("Wrong username or password");
             },
			 "json"

          );
      });
	  
	  /*
	  $("#forgot_password_first").click(function(event){
		//uName = $('#login_username').val();
		alert("Here");
		uName = $('#forgotp_username').val();
		alert("Le liya");
		alert(uName);
		var result = {};
		result['userName']=uName;
		//result['password']=pWord;
		$.post( 
             "http://54.249.240.120/qcorner/forgotpwd/checkuname",
			 result,
             function(data) {
			 alert("Pohonch gaya");
			 /*
				if(data.head.status==200)
				{
					setCookie('username', uName, 1);
					setCookie('firstname', data.body.firstName, 1);
					setCookie('lastname', data.body.lastName, 1);
					setCookie('reputation', data.body.reputation, 1);
					setCookie('city', data.body.city, 1);
					setCookie('affiliation', data.body.affiliation, 1);
					setCookie('reviewer', data.body.isReviewer, 1);
					alert("Success");
					window.location = 'homepage.html';
				}
				else
					alert("Wrong username or password");*/
      //       },
		//	 "json"

         // );
      //});
	  
	  $("#forgotp_un_button").click(function(event){
		uName = $('#forgotp_username').val();
		alert($('#forgotp_username').val());
		var result = {};
		result['userName']=uName;
		$.post( 
             "/qcorner/forgotpwd/checkuname",
			 result,
             function(data) {
				/*if(data.head.status==200)
				{
					setCookie('username', uName, 1);
					window.location = 'login.html#torp';
				}
				else
					alert("Username doesn't exist!");*/
					console.log(data);
					alert(data);
             },
			 "json"

          );
	  });
	  $("#resetp_button").click(function(event){
		uName = getCookie("username");
		pWord = $('#newPassword').val();
		//secQuesID
		secQA = $('#securityQA').val();
		var result = {};
		result['username']=uName;
		result['password']=pWord;
		//"securityQuestionID":"",
		result['securityAnswer']=secQA;
		$.post( 
             "/",
			 result,
             function(data) {
				/*if(data.head.status==200)
				{
					window.location = 'dashboard.html';
				}
				else
					alert("Incorrect answer for the security question. Try again!");*/
					alert(JSON.stringify(data));
             },
			 "json"

          );
	  });
	  $("#forgotusername_button").click(function(event){
		eMail = $('#forgotusername_email').val();
		//secQuesID
		var result = {};
		result['email']=eMail;
		$.post( 
             "http://54.249.240.120/qcorner/",
			 result,
             function(data) {
				/*if(data.head.status==200)
				{
					alert("An email with the password reset link has been sent to you!");
					window.location = 'login.html';
				}
				else
					alert("There is no username associated with the email id. Try again!");*/
					alert(JSON.stringify(data));
             },
			 "json"

          );
	  });
	  $("#signup_button").click(function(event){
		var fName = $('#su_fname').val();
		var lName = $('#su_lname').val();
		var uName = $('#su_uname').val();
		var pWord = $('#su_pass').val();
		var eMail = $('#su_email').val();
		var phone = $('#su_phone').val();
		var dob = $('#su_dob').val();
		var gender = $('#su_gender').val();
		var intrsts = $('#su_intrst').val();
		var country = $('#su_country').val();
		var city = $('#su_city').val();
		var state = $('#su_state').val();
		var qual = $('#su_qual').val();
		var affili = $('#su_affili').val();
		var aoe = $('#su_aoe').val();
		var secQues = $('#su_secques').val();
		var secA = $('#su_secA').val();
		var rKey = $('#su_rkey').val();
		var result = {};
		result['firstName']=fName;
		result['lastName']=lName;
		result['userName']=uName;
		result['password']=pWord;
		result['email']=eMail;
		result['phone']=phone;
		result['dob']=dob;
		result['gender']=gender;
		result['interests']=intrsts;
		result['country']=country;
		result['city']=city;
		result['state']=state;
		result['qualification']=qual;
		result['affiliation']=affili;
		result['areasOfExpertise']=aoe;
		result['securityQuestionID']=secQues;
		result['securityAnswer']=secA;
		result['reviewerKey']=rKey;
		$.post( 
             "/qcorner/register",
			 result,
             function(data) {
				if(data.head.status==201)
				{
					window.location = 'homepage.html';
				}
				else
					alert("There was some error. Please check your data and try again!");
					alert(JSON.stringify(data));
             },
			 "json"

          );
	  });
});