<?php
$pageClass = 'order';
$pageName = 'order1';
include '../snippets/header.php';
?>

<script type="text/javascript" src="js/jquery.payment.min.js"></script>
<script type="text/javascript" src="http://www.payfirma.com/media/payfirma.minified.js"></script>

<section class="banner order2-banner">

    <div class="inner">
        <h3 class="nob">Task Summary</h3>

        <div class="whitebg-rounded" style="margin-top: 0.6rem">

            <div class="order icon">
                <i class="icon-grocery-bag"></i>
            </div>
            <div class="order description ">
                <h2 class="blue">Shopping &amp; Delivery  	<b class="down-hat"></b></h2>


                <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque ladantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto gbeatae vitae dicta sunt explicabo. Nemo enim epsam voluptatem.</p>
            </div>

            <i class="clear"></i>
        </div>
    </div>
</section>

<section class="breadcrumbs">
    <div class="inner">
        <div class=" step">
            1. Summon Details
        </div>
        <div class="blue step">
            2. Enter Payment Details
        </div>
        <div class="step">
            3. Chose a Porter
        </div>

        <b class="clear"></b>
    </div>
</section>



<section class="order1 graybg" id="">
	<div class="inner">
		<div class="">
            <div class="pricetag">$450</div>
			<h1 class="blue">Payment Details</h1>
			<div class="apply__form">
				<form method="post" action="order/index6.php">

                    <fieldset  class="inputGroup inputGroup--form form-group" style="margin-top: 1rem;">
                        <div class="flex row validation">
							<flex-1 style="min-width: 50%;">
                                <label for="cardholder">Cardholer name</label>
	                            <input type="text" name="cardholder" id="cardholder" class="cc-name" placeholder="Cardholder Name" required />
							</flex-1>
							<flex-1 class="cc-brand" style="min-width: 50%;">
                                <img src="images/card-types.jpg" alt="" title="" style="vertical-align: middle;" />
							</flex-1>
                        </div>
                        <div class="flex row ">
							<flex-2 style="min-width: 50%; flex-basis: 15rem; margin-bottom:0.75rem;">
                                <label for="cardno">Card Number</label>
                            	<input type="tel" name="cardno" id="cardno" class="cc-number" placeholder="Card Number" required />
							</flex-2>
							<flex-2 style="min-width: 50%; flex-basis: 15rem;" class="flex row">
    							<flex-1 style="flex-basis:8rem;" class="">
                                    <label for="cardno">MM / YY</label>
    	                            <input type="tel" name="exp" id="cc-exp" class="cc-exp" autocomplete="cc-exp" placeholder="MM / YY" required />
    							</flex-1>
    							<flex-1 style="flex-basis:5rem;">
                                    <label for="cardno">CVC</label>
    								<input type="tel" name="cvc" id="cc-cvc" class="cc-cvc" autocomplete="off" placeholder="CVC"  />
    							</flex-1>
                            </flex-2>
                        </div>



                        <div class="center ">
                            Subtotal: $450<br /><br />

                            <input class="btn btn--primary btn--large" type="submit" value="Summon Porter" />
                            <br /><br />
                            <p>
                            You are charged only after your summon is completed. Summons have a one-hour minimum. a 5% Trust and Safety fee is added to the porter total rate. If you cancel your task within 24 hours of the scheduled start time, you will be charged a $50 cancellation fee.<br />
                            I agree to the terms of service &amp; privacy policy.
                            </p>
                        </div>
                    </fieldset>

                <br />
				</form>
			</div>
		</div>
	</div>
</section>

<script type="text/javascript">
jQuery(function($) {
    $('[data-numeric]').payment('restrictNumeric');
    $('.cc-number').payment('formatCardNumber');
    $('.cc-exp').payment('formatCardExpiry');
    $('.cc-cvc').payment('formatCardCVC');
    $.fn.toggleInputError = function(erred) {
        this.parent('.form-group').toggleClass('has-error', erred);
        return this;
    };
    $('form').submit(function(e) {
        e.preventDefault();
        var cardType = $.payment.cardType($('.cc-number').val());
        $('.cc-number').toggleInputError(!$.payment.validateCardNumber($('.cc-number').val()));
        $('.cc-exp').toggleInputError(!$.payment.validateCardExpiry($('.cc-exp').payment('cardExpiryVal')));
        $('.cc-cvc').toggleInputError(!$.payment.validateCardCVC($('.cc-cvc').val(), cardType));
        //$('.cc-brand').text(cardType);
        $('.validation').removeClass('text-danger text-success');
        $('.validation').addClass($('.has-error').length ? 'text-danger' : 'text-success');

		var card_number = $('.cc-number').val();
        var card_expiry = $('.cc-exp').val().split('/');
		var card_expiry_month = parseInt(card_expiry[0], 10);
		var card_expiry_year = parseInt(card_expiry[1], 10);
		var cvv2 = $('.cc-cvc').val();

        var name = $('#cardholder').val().split(' ');

		var first_name = name[0];
		var last_name = name[1];

		var key = '2d2d2d2d2d424547494e205055424c4943204b45592d2d2d2d2d4d494942496a414e42676b71686b6947397730424151454641414f43415138414d49494243674b434151454132354a532f77393066686b79506b726576356431476a3162766d686a314e6f2f3633355a47757636474c7639477939675a58354b527762706b6635676330524e4536316a7665456136537069624e52556a4e4a7267657433596b4a6f6e43545771727435796d36305a485769436c473439475834713264496d324856654c4957465538354154337273426f65587576646376687a685a51613061325a42463457524a376177596831646245724e667736393531593258594349396b6a484667337775553949464f5a70397548703264442b4d4e69515279694979514d4263594c4e6670304650727348523151715a504c5749474e6333636d70724a4e6f636650554d42656154766750656b73495a32567866694f6b5858756e636b71753050356b494a3743622f576c674d6c4c30347a703945744a6f39673559433151376e32426265486f4b75566557433858654b367673733976774944415141422d2d2d2d2d454e44205055424c4943204b45592d2d2d2d2d';
		var z = new Payfirma(key, {
			'card_number': card_number,
			'card_expiry_month': card_expiry_month,
			'card_expiry_year':  card_expiry_year,
			'cvv2': cvv2
		}, {
			'first_name': first_name,
			'last_name': last_name
		}, 'auth_server-side.php', callback);


    });

    function callback(response) {
		console.log(response);
	};

	$('input[type="text"], input[type="tel"]').keyup(function(){
		$(this).prev().css('opacity', this.value.length == 0 ? 0 : 1);
	})
});
  </script>

<?php include '../snippets/footer.php'; ?>
