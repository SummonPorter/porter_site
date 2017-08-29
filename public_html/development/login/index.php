<?php

if (empty($_SERVER['HTTPS'])) {
    header("Location: https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
    exit;
}


$pageClass = 'login';
$pageName = 'login';
require_once incl('snippets/header');

?>

<section class="login banner">
	<div class="inner">
		<div class="apply__formWrapper">
			<h1>Login</h1>
			<div class="apply__form">
				<form mathod="post" id="login" onsubmit="return false">
					<div class="inputGroup inputGroup--form singleInput">
						<input type="email" name="email" id="login-email" placeholder="Email Address" />
					</div>
					<div class="inputGroup inputGroup--form singleInput">
						<input type="password" name="password" id="login-password" placeholder="Password" />
					</div>

                    <div class="apply__checkboxes">
                        <div class="inputGroup inputGroup--checkbox">
    						<input type="checkbox" name="rememberme" id="rememberme" value="rememberme">
    						<label for="rememberme">Remember Me</label>
    					</div>
                    </div>

					<input class="btn btn--primary btn--full btn--rect btn--large" type="submit" value="Login" />
				</form>
			</div>
		</div>
	</div>
</section>

<section class="order1 graybg" id="customer-details">
	<div class="inner">
		<div class="">
			<h1 class="blue">Register</h1>
            <p>Registration blurb.</p>
			<div class="apply__form">
				<form method="post" id="register" onsubmit="return false">

                    <fieldset  class="inputGroup inputGroup--form " style="margin-top: 1rem;">
                        <div class="form2col">
                            <label for="firstname">Firstname</label>
                            <input type="text" name="firstname" id="firstname" placeholder="First name" required />
                            <label for="lastname">Lastname</label>
                            <input type="text" name="lastname" id="lastname" placeholder="Last name" required />
                            <label for="mobile">Mobile</label>
                            <input type="tel" patten="\+?\d*[\- ]?\d{3}[\- ]?\d{3}[\- ]?\d{4}|\+?\d{10,}"
                              name="mobile" class="mobile-bg" id="mobile" placeholder="Mobile Number" required />

                            <label for="email">Email</label>
                            <input type="email" name="email" class="email-bg" id="email" placeholder="Email Address" required />
                            <i class="fa fa-email-after"></i>

                            <label for="password">Password</label>
                            <input type="password" name="password" class="lock-icon password" id="password" placeholder="Password" required />

                            <label for="password2">Password Confirm</label>
                            <input type="password" name="password2" class="lock-icon password2" id="password2" placeholder="Password Confirm" required />

                        </div>


                        <div class="center">
                            <input class="btn btn--primary btn--large" type="submit" value="Continue" />
                        </div>
                    </fieldset>

                <br />
				</form>
			</div>
		</div>
	</div>
</section>
<section class="steps verticalSpacing--large">
	<div class="inner">
	</div>
</section>


<style id="cssConsole"></style>

<script type="text/javascript">
$(function(){
    $('#register').on('submit', function(e){
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        $.ajax({
            url: '<?=$url_prefixes?>/internal/services/customer/new',
            dataType: 'json',
            method: 'POST',
            data: {
                firstname: $('#firstname').val(),
                lastname: $('#lastname').val(),
                password: $('#password').val(),
                password2: $('#password2').val(),
                email: $('#email').val(),
                mobile: $('#mobile').val(),
            }
        }).done(function(data) {
            console.log(data);
            if (data['inputDeclaredValid'] == true) {
                $('.apply__form form').css('opacity', 0.5);
                $('.apply__form input').attr('disabled', 'disabled');
                data.returnMessages = ["Your registration was valid"];
            }
            cssConsole({
                '.form2col:after': {
                    background: 'rgba(181, 151, 76, 1)',
                    content: '"'+data.returnMessages.join("\n")+'"',
                    display: 'block',
                    padding: '1rem',
                    marginTop: '1rem',
                    marginBottom: '1rem',
                    color:'#1d1d51',
                    clear: "both",
                    animationName: 'shake-horizontal',
                    animationDuration: '.8s',
                    animationTimingFunction: 'ease-in-out'
                }
            });
        }).fail(function(xhr, status, error){
            console.log("Status: " + status + " Error: " + error);
            console.log(xhr);
        });
    });


    $('#login').on('submit', function(e){
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        $.ajax({
            url: '<?=$url_prefixes?>/internal/services/customer/login',
            dataType: 'json',
            method: 'POST',
            data: {
                email: $('#login-email').val(),
                password: $('#login-password').val(),
                remember: $('#rememberme').is(':checked') ? 'yes' : 'no'
            }
        }).done(function(data) {
            console.log(data);
            if (data['inputDeclaredValid'] == true) {
                $('.apply__form form').css('opacity', 0.5);
                $('.apply__form input').attr('disabled', 'disabled');
                data.returnMessages = ["Your registration was valid"];
            }
            cssConsole({
                '#login .apply__checkboxes:after': {
                    background: 'rgba(181, 151, 76, 1)',
                    content: '"'+data.returnMessages.join("\n")+'"',
                    display: 'block',
                    padding: '1rem',
                    marginTop: '1rem',
                    marginBottom: '1rem',
                    color:'#1d1d51',
                    clear: "both",
                    animationName: 'shake-horizontal',
                    animationDuration: '.8s',
                    animationTimingFunction: 'ease-in-out'
                }
            });
        }).fail(function(xhr, status, error){
            console.log("Status: " + status + " Error: " + error);
            console.log(xhr);
        });;
    });
});


</script>

<?php

include incl('snippets/footer'); ?>
