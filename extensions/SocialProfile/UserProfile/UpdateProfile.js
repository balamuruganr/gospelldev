/**
 * JavaScript used on Special:UpdateProfile
 * Displays the "State" dropdown menu if selected country is the United States
 */

 //global settings it wil be written on common.js 
 wgGospellSettingsProfileAboutMaxLenth = 512;
 wgGospellSettingsProfileMinAgeLimit   = 13;

var countries = new Array();
countries[0] = {
	country: 'United States',
	name: 'State',
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

function displaySection( id, country, section ) {
	country_id = -1;
	for( var x = 0; x <= countries.length-1; x++ ) {
		if( country == countries[x].country ) {
			country_id = x;
		}
	}

	var section_select = '';
	if( countries[country_id] ) {
		document.getElementById( id + '_label' ).innerHTML = countries[country_id].name;
		section_select += '<select class="profile-form" name="' + id + '" id="' + id + '"><option></option>';
		for( x = 0; x <= countries[country_id].sections.length-1; x++ ) {
			section_select += '<option value="' + countries[country_id].sections[x] + '"' +
				( ( countries[country_id].sections[x] == section ) ? ' selected="selected"' : '' ) + '>' + countries[country_id].sections[x] + '</option>';
		}
		section_select += '</select>';
	}

	document.getElementById( id + '_form' ).innerHTML = section_select;
}

function emailCheck(obj)
{
 
           var str = $(obj).val();
		   var aflag = 0;
		   var dflag = 0;
		   var z;
		   var len;
		   
		   len=str.length;
		   var spflag = 0; 
		   for(z=0;z<len;z++)
		   {
			  if(str.charAt(z)=='@')
			  {
				 aflag++;
			  }
			  if(str.charAt(z)=='.')
			  {
				 dflag++;
			  }
			  if(str.charAt(z)=="'" || str.charAt(z)=='"' || str.charAt(z)=='+' || str.charAt(z)==';' || str.charAt(z)==':'
				 || str.charAt(z)=='!' || str.charAt(z)=='*' || str.charAt(z)=='$' || str.charAt(z)=='%' || str.charAt(z)=='A..Z' || str.charAt(z)=='a..z')
			  {
				 spflag++;
			  }
		   }
		   if(spflag==1)
		   {
			  alert('Sorry, Special Characters can not be entered.');
			  $(obj).val('');
			  $(obj).focus();
			  return false;
		   }
		   else if((aflag==1)&&(dflag>=1))
		   {
			  return true;
		   }
		   else if(str!='')
		   {
			  alert('Please enter a valid Email ID, Thanks!');
			  $(obj).val('');
			  $(obj).focus(); 
			  return false;
		   }
}
////////////////////////////////////////////////////////////////////////////////////////
// Function Name: calculateAge()                                                      //
//
// Return Value: Age                                                                  //
//
// Description: To get the Age of given DOB(date)                                     // 
// 
// Input Parameters: DOB obj, ageField obj                                            //  
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

$(document).ready(function(){
    $('#about').keyup(function(){
       if($(this).val().length !=0 && $(this).val().length > wgGospellSettingsProfileAboutMaxLenth ){ 
        //var str = $(this).val().substring(0,512);//$(this).val(str);//$(this).focus();
        alert("Not allowed above " + wgGospellSettingsProfileAboutMaxLenth + " characters.");
        //$("#about_check").text("Not allowed above 512 characters.").css('color',"#ff0000");
       }
    });
    
    $('#email').blur(function(){ 
        emailCheck($(this));
    });
    
    $('#birthday').change(function(){
       calculateAge($(this)); 
    });
    
});

 
mw.loader.using( 'jquery.ui.datepicker', function() {
	jQuery( function( jQuery ) {
		jQuery( '#birthday' ).datepicker({
			changeYear: true,
            changeMonth: true,
			yearRange: '2001:c',  
			dateFormat: jQuery( '#birthday' ).hasClass( 'long-birthday' ) ? 'mm/dd/yy' : 'mm/dd'
		});
	});
});

