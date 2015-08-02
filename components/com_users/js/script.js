/*
 * 
 * Script for Abbreviation, we are using the autocomplete jQuery plugin........
 */

	$(document).ready(function() {
		var sometext;
		var othertextchurch;
		searchreligion();
		searchlocalchurch('p_scnt_1');
		$('#jform_religion').keyup(function(e1) {
			searchreligion();
		});
		$('.church').keyup(function(e2){
			var  u = $(this).attr("id"); 
			searchlocalchurch(u);
	    });
		searchotherchurch('p_other_1');
});

	function searchreligion()
	{	
		if($('#jform_religion').length){
			var ch1 = $('#jform_religion').val();  // to get the value of input filed   
			var chlen = ch1.length; 
	        if(chlen > 1) { 
	            ch1 = ch1[chlen-1];	
	           } 
	             else { 
	             	  ch1 = ch1; 
	             	  }
	       var url='index.php?option=com_users&task=profile.religion&format=raw';	 
	       if(chlen<=1) { 
 
	    	   $.ajax({
		        url: url,
		        type: "POST",
		        data: { ch: ch1 },
		        dataType: "json",
		        timeout: 4000,
		        success: function(data) {	
		        	var newdata = eval(data.test);
		        	abcd(newdata);
		        },
		        error: function() {
		            
		        }
		  });
	   }
		}
	}

	function abcd(newArray) { 
	    $('#jform_religion').autocomplete(newArray).result(function(event, data1, formatted) { // result is a separate function
	      
	      var selectedabb1 = data1;  
	      var selectedabb = selectedabb1.toString();
	      var c = selectedabb.split("<p style='display:none;'>");
	      var t =  c[1].split("</p>",1);
	      $('#jform_religion').val(c[0]);
	      $('#jform_religionid').val(t);
	       });
		
	} 
/*
*code for the local church autocomplete functionality...
*/
	function searchlocalchurch(e){
		if($('#'+e).length){
		var church = $('#'+e).val();  // to get the value of input filed   
		var churchlen = church.length;
        if(churchlen > 1) { 
        	church = church[churchlen-1];	
           } 
             else { 
            	 church = church; 
             	  }

       var url='index.php?option=com_users&task=profile.localchurch&format=raw';	 
       if(churchlen<=1) { 

    	   $.ajax({
	        url: url,
	        type: "POST",
	        data: { church: church },
	        dataType: "json",
	        timeout: 4000,
	        success: function(data) {	
	        	var churchdata = eval(data.test);
	        	localchurch(churchdata, e);
	        },
	        error: function() {
	            
	        }
	  });
   }
		}
}

	function localchurch(churchdata, e) { 
		
	    $("#"+e).autocomplete(churchdata).result(function(event, data1, formatted) { // result is a separate function
	     var churchboxlength = $('#localchurchs_parent p').size(); 
	     

	         if(churchboxlength > 1)
	        	{ 
     			      var selectedabb1 = data1; 
				      var selectedabb = selectedabb1.toString();
				      var c = selectedabb.split("<p style='display:none;'>");
				      var t =  c[1].split("</p>",1);
				      $('#'+e).val(c[0]);
	                var tr = 1;
	                jQuery.each($('#localchurchs_parent p input'), function(i, val) {

	                	if(i>0 && ($('#p_scnt_1').val().length == 0)){
	                		 $('#firstchurch').css({'color': 'red', 'display': 'block'}); tr = 0; return;
	                		}
	                
	    	    	 });
	                 if(tr){
				      var selectedabb1 = data1; 
				      var selectedabb = selectedabb1.toString();
				      var c = selectedabb.split("<p style='display:none;'>");
				      var t =  c[1].split("</p>",1);
				      $('#'+e).val(c[0]); 
				      $('#firstchurch').css({'display': 'none'});
				      sometext = $('#localchurch').val();
				      if($('#localchurch').val() != '')	
				      {
				    	  sometext = sometext + ',' + t;
				    	  $('#localchurch').val(sometext);
				      }
				      else
				      {   
				    	  sometext = t;
				    	  $('#localchurch').val(sometext);
				      } 

	                 }else{
	                	 $('#'+e).val(''); 
					     $('#'+e).next().attr('id', '');
                     } 
	        	}
	         else{
		      var selectedabb1 = data1; 
		      var selectedabb = selectedabb1.toString();
		      var c = selectedabb.split("<p style='display:none;'>");
		      var t =  c[1].split("</p>",1);
		      $('#'+e).val(c[0]); 
		      if($('#localchurch').val() != '')	
		      {
		    	  sometext = sometext + ',' + t;
		    	  $('#localchurch').val(sometext);
		      }
		      else
		      {   
		    	  sometext = t;
		    	  $('#localchurch').val(sometext);
		      } 

	    }
	     }); 
	} 

	
	/*
	*code for the other church autocomplete functionality...
	*/
		function searchotherchurch(e){
			if($('#'+e).length){
			var otherchurch = $('#'+e).val();  // to get the value of input filed   
			var churchlen1 = otherchurch.length;
	        if(churchlen1 > 1) { 
	        	otherchurch = otherchurch[churchlen1-1];	
	           } 
	             else { 
	            	 otherchurch = otherchurch; 
	             	  }

	       var url='index.php?option=com_users&task=profile.localchurch&format=raw';	 
	       if(churchlen1<=1) { 

	    	   $.ajax({
		        url: url,
		        type: "POST",
		        data: { otherchurch: otherchurch },
		        dataType: "json",
		        timeout: 4000,
		        success: function(data1) {	
		        	var otherchurchdata = eval(data1.test);
		        	otherchurchfind(otherchurchdata, e);
		        },
		        error: function() {
		            
		        }
		  });
	     }
		}
	}

	function otherchurchfind(otherchurchdata, e) { 
			
		    $("#"+e).autocomplete(otherchurchdata).result(function(event, data1, formatted) { // result is a separate function
		     var otherchurchboxlength = $('#otherchurchs_parent p').size(); 
		     

		         if(otherchurchboxlength > 1)
		        	{ 
	     			      var selectedabb1 = data1; 
					      var selectedabb = selectedabb1.toString();
					      var c = selectedabb.split("<p style='display:none;'>");
					      var t =  c[1].split("</p>",1);
					      $('#'+e).val(c[0]);
			              var pr = 1;
			              jQuery.each($('#otherchurchs_parent p input'), function(p, val) {
	
			                if(p>0 && ($('#p_other_1').val().length == 0)){
			                	$('#firstotherchurch').css({'color': 'red', 'display': 'block'}); pr = 0; return;
			                }
			    	      });
		                 if(pr){
					      var selectedabb1 = data1; 
					      var selectedabb = selectedabb1.toString();
					      var c = selectedabb.split("<p style='display:none;'>");
					      var t =  c[1].split("</p>",1);
					      $('#'+e).val(c[0]); 
					      $('#firstotherchurch').css({'display': 'none'});
					      othertextchurch = $('#otherchurch').val();
					      if($('#otherchurch').val() != '')	
					      {
					    	  othertextchurch = othertextchurch + ',' + t;
					    	  $('#otherchurch').val(othertextchurch);
					      }
					      else
					      {   
					    	  othertextchurch = t;
					    	  $('#otherchurch').val(othertextchurch);
					      } 

		                 }else{
		                	 $('#'+e).val(''); 
						     $('#'+e).next().attr('id', '');
	                     } 
		        	}
		         else{
			      var selectedabb1 = data1; 
			      var selectedabb = selectedabb1.toString();
			      var c = selectedabb.split("<p style='display:none;'>");
			      var t =  c[1].split("</p>",1);
			      $('#'+e).val(c[0]); 
			      if($('#otherchurch').val() != '')	
			      {
			    	  othertextchurch = othertextchurch + ',' + t;
			    	  $('#otherchurch').val(othertextchurch);
			      }
			      else
			      {   
			    	  othertextchurch = t;
			    	  $('#otherchurch').val(othertextchurch);
			      } 

		      }
		     }); 
		} 
