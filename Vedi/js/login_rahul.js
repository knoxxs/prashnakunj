$(document).ready(function() {
	$("#login_button").click(function(event){
		alert("Hello");
		uName = $('#login_username').val();
		pWord = $('#login_password').val();
		var result = {};
		result['userName']=uName;
		result['password']=pWord;
		alert(uName);
		alert(pWord);
		$.post( 
             "http://54.249.240.120/qcorner/login",
			 result,
             function(data) {
				alert("Function after post.");

				if (data.head.status == 200)
					{
					setCookie('userName', uName, 1);
					alert("Login successful.");
					alert(getCookie("userName"));
					}
				else 
					alert ("Wrong username or password!");
				var jsonString = JSON.stringify(data);
				alert(jsonString);
		});
   });
});