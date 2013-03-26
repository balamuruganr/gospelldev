/**
 * JavaScript used on Special:UpdateProfile
 * Displays the "State" dropdown menu if selected country is the United States
 */
 
////////////////////////////////////// Common js /////////////////////////////////////////////////
 //global settings it wil be written on common.js 
 wgGospellSettingsProfileAboutMaxLenth = 512;
 wgGospellSettingsProfileMinAgeLimit   = 13;
 

////////////////////////////////////////////////////////////////////////////////////////
// Function Name: calculateAge()                                                      //
//
// Return Value: Age                                                                  //
//
// Description: To get the Age of given DOB(date)                                     // 
// 
// Input Parameters: DOB obj                                                          //  
// 
// Created Date: 25 March 2013                                                        //
//
// Created By: Mathivanan.S                                                           //
////////////////////////////////////////////////////////////////////////////////////////
function calculateAge(dateOfBirth)
{                                         
                         
  if($(dateOfBirth).val() != '')   //$(dateOfBirth).val() MM/DD/YYYY
   {
     var now = new Date();
     var dob = $(dateOfBirth).val().split('/');
     
     if(dob.length === 2){ 
       
       dob.push($(".ui-datepicker-year").val());
       $('#hiddenbirthday').val(dob[2]);
       
     }
     
	 if (dob.length === 3) 
	 {
	   var born = new Date(dob[2], dob[0] * 1 - 1, dob[1]);  // new Date(year, month, day, hours, minutes, seconds, milliseconds)
	   var age = Math.floor((now.getTime() - born.getTime()) / (365.25 * 24 * 60 * 60 * 1000));
       
       if(isNaN(age))
	   {
            alert("Please enter correct Date Of Birth.");
            $(dateOfBirth).val('');
    		$(dateOfBirth).focus();
       }
      else
       { 
	     if(age <=0)
		  {
			  alert("Please enter correct Date Of Birth.");
			  $(dateOfBirth).val('');
			  $(dateOfBirth).focus();
		  }
		  else if(age < wgGospellSettingsProfileMinAgeLimit)
		  {
			  alert("Your minimum age must be in above " + wgGospellSettingsProfileMinAgeLimit);
			  $(dateOfBirth).val('');
			  $(dateOfBirth).focus();
		  }        
       }
    } 
  }
}
////////////////////////////////////// Common js /////////////////////////////////////////////////

var countries = new Array();
countries[0] = {
	country: 'United States',
	sections: [
		'Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado',
		'Connecticut', 'Delaware', 'Florida', 'Georgia', 'Hawaii', 'Idaho',
		'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana',
		'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota',
		'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada',
		'New Hampshire', 'New Jersey', 'New Mexico', 'New York',
		'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon',
		'Pennsylvania', 'Puerto Rico', 'Rhode Island', 'South Carolina',
		'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia',
		'Washington', 'Washington, D.C.', 'West Virginia', 'Wisconsin', 'Wyoming'
	]
};
countries[1] = {
	country: 'India',
	sections: [
		'Andra Pradesh', 'Tamil Nadu', 'Kerala'
	]
};

function displaySection( id, country, section ) {
	country_id = -1;
	for( var x = 0; x <= countries.length-1; x++ ) {
		if( country == countries[x].country ) {
			country_id = x;
		}
	}

	var section_select = '';
	if( countries[country_id] ) {
		section_select += '<option value="">-Select State-</option>';
		for( x = 0; x <= countries[country_id].sections.length-1; x++ ) {
			section_select += '<option value="' + countries[country_id].sections[x] + '"' +
				( ( countries[country_id].sections[x] == section ) ? ' selected="selected"' : '' ) + '>' + countries[country_id].sections[x] + '</option>';
		}
		section_select += '';
	}

	document.getElementById( id  ).innerHTML = section_select;
}


$(document).ready(function(){
    //$( 'form[name="profile"]' ).validate();
    $('#about').keyup(function(){
       if($(this).val().length !=0 && $(this).val().length > wgGospellSettingsProfileAboutMaxLenth ){ 
        //var str = $(this).val().substring(0,512);//$(this).val(str);//$(this).focus();
        alert("Not allowed above " + wgGospellSettingsProfileAboutMaxLenth + " characters.");
        //$("#about_check").text("Not allowed above 512 characters.").css('color',"#ff0000");
       }
    });
    
    $('#birthday').change(function(){
       calculateAge($(this)); 
    });
    
});

mw.loader.using( ['jquery.validate','jquery.ui.datepicker'], function() {
	jQuery( function( jQuery ) {
	    jQuery( '.required' ).each(function(){
	       $(this).attr('title', $(this).attr('title').replace("*") + " is required.");
	    });
		jQuery( '#birthday' ).datepicker({
			changeYear: true,
            changeMonth: true,
			 yearRange: '-100:'+((-1)*wgGospellSettingsProfileMinAgeLimit), //yearRange: '2001:c',  
			dateFormat: jQuery( '#birthday' ).hasClass( 'long-birthday' ) ? 'mm/dd/yy' : 'mm/dd'
		});
        
        jQuery( 'form[name="profile"]' ).validate();
        /*{
              rules: {
                real_name: "required"//,
                //password_again: {
                //  equalTo: "#password"
                //}
              }
        }*/
	});
});

