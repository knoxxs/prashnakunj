
$(document).ready(function(){  
var uName, pWord, eMail, secQ, secQA;
	  $("#login_button").click(function(event){
	  	uName = $('#login_username').val();
		pWord = $('#login_password').val();
		var temp;
		temp = $("#loginform").valid();
		if(uName!="")
		{	
		if(pWord!=""){}
			else login_password.focus();
		}	else login_username.focus();
	
		if(temp) { 
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
					alert(data.head.message);
             },
			 "json"

          ); }
      });
	  $("#forgotp_un_button").click(function(event){
		uName = $('#forgotp_username').val();
		var temp1 = $("#forgotpform").valid();
		var result = {};
		result['userName']=uName;
		if(temp1) {
		$.post( 
             "/qcorner/forgotpwd/checkuname",
			 result,
             function(data) {
				if(data.head.status==200) {
						window.location = 'login.html#torp';
						setCookie('userName', uName, 1);
						setCookie('securityQuestionNumber', data.body, 1);
						alert(data.body);
						if (data.body == 0)	{
							jQuery('#securityQ').html("What is your mother's maiden name?");
						} else if (data.body == 1) {
							jQuery('#securityQ').html("Where did you first attend school?");
						} else if (data.body == 2) {
							jQuery('#securityQ').html("What was the name of your first pet?");
						} else if (data.body == 3) {
							jQuery('#securityQ').html("What was yur first telephone number?");
						} else {
							jQuery('#securityQ').html("Incorrect data from backend.");
						}
					}
					else
						{
							alert("That username doesn't exist. Please try again!");
						}
             },
			 "json"
          ); }
	  });
	  $("#resetp_button").click(function(event){
	  	//alert(getCookie('securityQuestionNumber'));
		switch(getCookie('securityQuestionNumber'))
		{
			case '0':
				$('#securityQ').html('What is your mother\'s maiden name?');
				break;
			case '1':
				$('#securityQ').html('Where did you first attend school?');
				break;
			case '2':
				$('#securityQ').html('What was the name for your first pet?');
				break;
			case '3':
				$('#securityQ').html('What was your first telephone number?');
				break;
		}
		uName = getCookie("userName");
		pWord = $('#newPassword').val();
		cWord = $('#confirmPassword').val();
		secQA = $('#securityQA').val();
		
		var temp2 = $("#resetpform").valid();
		if(pWord!="")
		{	
		if(secQA=="")
			 securityQA.focus();
		}	else newPassword.focus();
			
		var result = {};
		result['userName']=uName;
		result['newPassword']=pWord;
		//"securityQuestionID":"",
		result['securityAnswer']=secQA;
		result['securityQuestionNumber']=getCookie('securityQuestionNumber');
		if(temp2) {
		$.post( 
             "/qcorner/forgotpwd/updatepwd",
			 result,
             function(data) {
					if(data.head.status == 200)
					{
						alert("Password changed successfully!");
						setCookie('username', uName, 1);
						window.location = 'dashboard.html';
					}
					else
						alert("Authentication failed!")
             },
			 "json"

          ); }
	  });
	  $("#forgotusername_button").click(function(event){
		eMail = $('#forgotusername_email').val();
		var temp4 = $("#forgotuform").valid();
		var result = {};
		result['email']=eMail;
		if(temp4) {
		$.post( 
             "/qcorner/",
			 result,
             function(data) {
				
             },
			 "json"

          ); }
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
		if(rKey=="")
			rKey = -1;
		
		
		$.validator.addMethod("NumbersOnly", function(value, element){
			return this.optional(element) || /^[?+\-0-9]+$/i.test(value);}
			, "Phone must contain only numbers, + and -.");
			
		$.validator.addMethod("DatesOnly", function(value, element){
			return this.optional(element) || /^[?\0-9]+$/i.test(value);}
			, "Dates must follow the format yyyy/mm/dd");
		var check_username=/^[a-zA-Z0-9_]+$/;
		var check_text=/^[a-zA-Z]+$/;
		
		var temp10 = $("#registerform").valid();
			
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

			var x=dob.split("-");
                var year=x[0];
                var month=x[1];
                var day=x[2];
                
                var today=new Date();
                var to_year=today.getFullYear();
                var flag;
                var max;
                if(year%4==0)
                        flag=1;
                else
                        flag=0;
                        
                if(year.length==4)
                {
                        if(month.length<=2)
                        {
                                if(day.length<=2)
                                {
                                        if(year<=(to_year-5))
                                        {
                                                if(month>0 && month<=12)
                                                {
                                                        if(flag==1 && month==2)
                                                        {
                                                                if(day>0 && day<=29)
                                                                {}
                                                                else
                                                                        {alert("day not valid");}
                                                        }
                                                        if(flag==0 && month==2)
                                                        {
                                                                if(day>0 && day<=28)
                                                                {}
                                                                else{alert("day not valid");}
                                                        }
                                                        if(flag!=1 && month%2==0)
                                                        {
                                                                if(day>0 && day<=30)
                                                                {}
                                                                else{alert("day not valid");}
                                                        }
                                                        if(flag!=1 && month%2!=0)
                                                        {
                                                                if(day>0 && day<=31)
                                                                {}
                                                                else{alert("day not valid");}
                                                        }
                                                } 
                                                else 
                                                {
                                                        alert("Month is not valid");
                                                }
                                        }
                                        else
                                        {
                                                alert("Too young to be a member");
                                        }
                                }
                                else
                                {
                                        alert("Day length not valid");
                                }
                        }
                        else
                        {
                                alert("Month length not valid");
                        }
                }
                else
                {
                        alert("Year length not valid");
                }
	
		
		var result = {};
		result['firstName']=fName;
		result['lastName']=lName;
		result['userName']=uName;
		result['password']=pWord;
		result['email']=eMail;
		result['phone']=phone;
		result['dob']=dob;
		result['gender']=gender;
		result['interests']=newIntrst;
		result['country']=country;
		result['city']=city;
		result['state']=state;
		result['qualification']=qual;
		result['affiliation']=affili;
		result['areasOfExpertise']=newAOE;
		result['securityQuestionID']=secQues;
		result['securityAnswer']=secA;
		result['reviewerKey']=rKey;
		setCookie('username', uName, 1);
		setCookie('firstname', fName, 1);
		setCookie('lastname', lName, 1);
		setCookie('reputation', 0, 1);
		setCookie('city', city, 1);
		setCookie('affiliation', affili, 1);
		setCookie('reviewer', false, 1);
		if(temp10) {
			
		$.post( 
             "/qcorner/register",
			 result,
             function(data) {
				if(data.head.status == 201)
				{
					rx = {};
					rx['userName'] = uName;
					rx['password'] = pWord;
					$.post( 
		             "/qcorner/login",
					 rx,
		             function(data) 
		            {
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
						else{
							alert(data.head.message);
							window.location = 'login.html';
						}
					}, "json" );	
				}
				else
					alert(data.head.message);
			}
			,"json"

          );
		}

            
	  });
});