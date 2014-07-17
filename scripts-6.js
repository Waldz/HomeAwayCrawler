/* all script functions of this site defined in this file */
function confirm_on_del(val)
{

	if(val.length==0)
	val = "Are you sure?";
	
		if(confirm(val))
			return true;
		else
			return false;
}


function alert_delete(val1,val2,val3)
{
    if(confirm_on_del(''))
    {
        str=val2+"&id="+val1+"&type="+val3;
        window.location.href=str;
    }
}


function chk_email(str)
{
    var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i
    if (filter.test(str))
    {
       testresults = true;   
    }
    else
    {
       testresults = false;
    }
    return (testresults)
}



function chk_characters(obj, max_length)
{
	if(obj.value.length > max_length)
	{
		obj.value=obj.value.substr(0, max_length);
	}
}


function doformaction(pageno)
{
    document.search_form.page.value=pageno;
    document.search_form.submit();
}


function isValidEmail($email)
{
    return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
}


function onFormSubmit ()
{
    document.banner.email.value = "Email";
    return true; // allow form submission to continue
}


function do_login()
{
    var data =  $("#login_form").serialize();
    $.post('includes/login.php',
            data,
            function (data)
            {
              if(data.status == 1)
              {
               window.location.href='http://flatfindr.com/user/';
              }
              else
              {
               alert(data.msg);   
              }
              
             },'json'
            
           )
    
}

function do_page_login()
{
    var data =  $("#loginfrm").serialize();
    $.post('includes/login.php',
            data,
            function (data)
            {
              if(data.status == 1)
              {
               window.location.href='http://flatfindr.com/user/';
              }
              else
              {
               alert(data.msg);   
              }
              
             },'json'
            
           )
    
}

$(document).ready(function()
{
               
			    $("#register").click(function(){
					
					alert('vishal');
                var element = $(this);
                var Id = element.attr("id");
                var loadplacecomment = $("#message");
                
                var username = $("#name").val();
                var email = $("#email").val();
                var password = $("#password").val();
                var mobile = $("#phonenumber").val();
                var dataString = 'name='+ username + '&email='+ email + '&password='+ password + '&phonenumber='+ mobile;
                
                if(username=='' || email=='' || password=='' || mobile=='' || username=='Name' || email=='Email' || password=='Password' || mobile=='Phone Number')
                {
                 document.getElementById('message').innerHTML='<div style="color:#FF0000; font-weight:bold;">Please enter all field.</div>';
                }
								
                else
                {
                    $("#flash").show();
                    $("#flash").fadeIn(400).html('<img src="images/ajax-loader.gif" align="absmiddle"> loading.....');

                    $.ajax({
                        type: "POST",
                        url: "includes/ajaxinsert.php",
                        data: dataString,
                        cache: false,
                        success: function(html)
                        {
                            loadplacecomment.html('');
                            $("#message").append(html);
                            $("#flash").hide();
                        }
                    });
                }
                return false;});
}
);
    
    
function register_user()
{ 
        var username = $("#uname").val();
        var email    = $("#uemail").val();
        var password = $("#upassword").val();
        var mobile   = $("#uphonenumber").val();
        var dataString = 'name='+ username + '&email='+ email + '&password='+ password + '&phonenumber='+ mobile;

        if(username=='' || email=='' || password=='' || mobile=='' || username=='Name' || email=='Email' || password=='Password' || mobile=='Phone Number')
        {
            $('#error_message').html('Please enter all field.');
        }
        else
        {
            if(chk_email(email))
            {
                $.ajax({
                type: "POST",
                url: "includes/ajaxinsert.php",
                data: dataString,
                cache: false,
                success: function(html)
                { 
                    $("#error_message").html(html);
                }
                });
            }
            else
            {
                $('#error_message').html('Please enter valid email.');
            }

        }
        return false;
}    

$(document).ready(function() {
	
	//if submit button is clicked
	$('#submit').click(function () {		
		
		//Get the data from all the fields
		var name = $('input[name=name]');
		var email = $('input[name=email]');
		var phone = $('input[name=phone]');
		var location = $('textarea[name=location]');
		var txtwantto = $('select[name=txtwantto]');
		var txtprotype = $('select[name=txtprotype]');
		var image = $('input[type=file]');

		//Simple validation to make sure user entered something
		//If error found, add hightlight class to the text field
		if (name.val()=='') {
			name.addClass('hightlight');
			return false;
		} else name.removeClass('hightlight');
		
		if (email.val()=='') {
			email.addClass('hightlight');
			return false;
		} else email.removeClass('hightlight');

		if (phone.val()=='') {
			phone.addClass('hightlight');
			return false;
		} else phone.removeClass('hightlight');
		
		if (location.val()=='') {
			location.addClass('hightlight');
			return false;
		} else location.removeClass('hightlight');
		
		if (txtwantto.val()=='') {
			txtwantto.addClass('hightlight');
			return false;
		} else txtwantto.removeClass('hightlight');
		if (txtprotype.val()=='') {
			txtprotype.addClass('hightlight');
			return false;
		} else txtprotype.removeClass('hightlight');

		//organize the data properly
		var data = 'name=' + name.val() + '&email=' + email.val() + '&phone=' + 
		phone.val() + '&location='  + encodeURIComponent(location.val()) + '&txtwantto=' + txtwantto.val() + '&txtprotype=' + txtprotype.val() + '&image=' + image.val();
		//alert(data);
		//disabled all the text fields
		$('.text').attr('disabled','true');
		
		//show the loading sign
		$('.loading').show();
		
		//start the ajax
		$.ajax({
				
			//GET method is used
			type: "GET",
		//this is the php file that processes the data and send mail
			url: "includes/process.php",	
			//pass the data			
			data: data,		
			
			//Do not cache the page
			cache: false,
			
			//success
			success: function (html) {//alert(html);				
				//if process.php returned 1/true (send mail success)
				if (html==1) {			
					//hide the form
					$('.form').fadeOut('slow');					
					
					//show the success message
					$('.done').fadeIn('slow');
					
				//if process.php returned 0/false (send mail failed)
				} else alert('Sorry, unexpected error. Please try again later.');				
			}		
		});
		
		//cancel the submit button default behaviours
		return false;
	});	
});