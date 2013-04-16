jQuery(document).ready(function topTenDisplay()
{
	alert("Hello");
	jQuery.get(
					"/qcorner/topten",
					function(data){
						alert(data);
						if(data.head.status == 200)
						{
							for(var i=0; i<data.body.length; i++)
								{
									jQuery('#topten_' + i).after('<li><a id="topten_' + (i+1) + '">' + data.body[i] +'</a></li>');
								}
							}
						}
						else
						{
							alert("Something went wrong.");
						}
					},
					"json"
				);
});
