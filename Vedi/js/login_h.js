
$(document).ready(function(){  
var uName, pWord, eMail, secQ, secQA;
	  $("#login_button").click(function(event){
	  	uName = $('#login_username').val();
		pWord = $('#login_password').val();
		
		$("#loginform").valid();
		if(uName!="")
		{	
		if(pWord!=""){}
			else login_password.focus();
		}	else login_username.focus();
			
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
		$("#forgotpform").valid();
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
		switch(getCookie('securityQuestionNumber'))
		{
			case '1':
				$('#securityQ').attr('value', 'What\'s your mother\'s maiden name?');
				break;
			case '2':
				$('#securityQ').attr('value', 'Where did you first attend school?');
				break;
			case '3':
				$('#securityQ').attr('value', 'What was the name for your first pet?');
				break;
			case '4':
				$('#securityQ').attr('value', 'What was your first telephone number?');
				break;
		}
		uName = getCookie("userName");
		pWord = $('#newPassword').val();
		cWord = $('#confirmPassword').val();
		secQA = $('#securityQA').val();
		
		$("#resetpform").valid();
		if(pWord!="")
		{	
		if(seQA!=""){}
			else securityQA.focus();
		}	else confirmPassword.focus();
		
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
		$("#forgotuform").valid();
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
	  
	  
	  $("#registerform").validate();
	  
	  $("#signup_button").click(function(event){

		var fName = $('#su_fname').val();
		var lName = $('#su_lname').val();
		var uName = $('#su_uname').val();
		var pWord = $('#su_pass').val();
		var cWord = $('#su_confirmpass').val();
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
		if(rKey==="")
			rKey = -1;
		/*var numbererror="Cant Give Numbers";
		
		var invalidChars = /[^0-9]/gi;
		if(invalidChars.test(fName)
		{
			alert("here");
			//$('#fname').after(numbererror);
			//'#fName'.after('<span>'+numbererror+'</span>');
		}*/
		
		
		
		
		
		$.validator.addMethod("NumbersOnly", function(value, element){
			return this.optional(element) || /^[?+\-0-9]+$/i.test(value);}
			, "Phone must contain only numbers, + and -.");
			
		/*$.validator.addMethod("onlytext", function(value, element){
			return this.optional(element) || /^[a-zA-z]+$/i.test(value);}
			, "This must contain only letters");
		*/
		var check_username=/^[a-zA-Z0-9_]+$/;
		var check_text=/^[a-zA-Z]+$/;
		
			$("#registerform").valid();
		
			
		//text
		if(check_text.test(fName) == true)
		{
			
			
		//alert("here");
		if(check_text.test(lName) == true)
		{
			
			
		
		
		//username
		if(check_username.test(uName)== true)
		{
			
			
		if(pWord.length!=0)
		{
		
		if(cWord.length!=0)
		{
		if(eMail.length<45 && eMail!="")
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
			
			
		if(secA!="")
		{
		
		}else{su_secA.focus();}
			}else{alert("Input Only Text for State");
			su_state.focus();}
			}else{alert("Input Only Text for City");
			su_city.focus();}
			}else{su_dob.focus();}
			}else{alert("Input 10 digit Number");
			su_phone.focus();}
			}else{alert("Input Email please");
			su_email.focus();}
			}else{su_confirmpass.focus();}
			}else{su_pass.focus();}
			}else{alert("Invalid Username");
			su_uname.focus();}
			}else{alert("Input Only Text in Last Name");
			su_lname.focus();}
			}else{alert("Input Only Text in First Name");
			su_fname.focus();}
	
		/*$("#registerform").validate({
			rules: {
						su_fname: {
							maxlength: 10,
							minlength: 2,
							}
					},
			messages : {
				su_fname : {
					required : " City must be  filled in",
					minlength : "At least 3 characters long",
					maxlength : "Should not exceed 30 characters",
						}
			}
			});*/
		
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






		
		
		