window.addEvent('domready', function() {
	    document.formvalidator.setHandler('category',
                function (value) {
	    				if(value==0){
	    					 return false;
	    					}else{
	    						 return true;
	    					}
                        
        });
});

window.addEvent('domready', function() {
    document.formvalidator.setHandler('country',
            function (value) {
    				if(value==0){
    					 return false;
    					}else{
    						 return true;
    					}
                    
    });
});

window.addEvent('domready', function() {
    document.formvalidator.setHandler('phone',
        function (value) {
    	    regex= /^((\d{2,4}-\d{2,20})(,\d{2,4}-\d{2,20})*)$/;
            return regex.test(value);
                    
    });
});