<?php
$pageClass = 'order';
$pageName = 'order1';
include '../snippets/header.php';
?>

<script type="text/javascript" src="js/jquery.payment.min.js"></script>

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
		<h1 class="blue">Payment Details</h1>
        <form method="post" action="order/index6.php">
    		<div class="flex">
                <flex-1 style="min-width:60%;flex-basis:26rem;">
        			<div class="apply__form">
                        <fieldset  class="inputGroup inputGroup--form form-group small" style="margin-top: 1rem;">
    			            <h6 class="blue">Credit Card Details</h6>
                            <br />
                            <div class="flex row validation">
                                <input type="text" name="cardholder" id="cardholder" class="cc-name" placeholder="Cardholder Name" required />
                            </div>
                            <div class="flex row validation" style="">
                                <input type="tel" name="cardno" id="cardno" class="cc-number" placeholder="Card Number" required />
                            </div>
                            <div class="flex row ">
                                <flex-1 style="min-width:50%; flex-basis: 8rem;">
                                    <input type="tel" name="exp" id="cardno" class="cc-exp" autocomplete="cc-exp" placeholder="MM / YY" required />
                                </flex-1>
                                <flex-1 style="min-width:50%; padding-left:1rem; flex-basis: 6rem;">
                                    <input type="tel" name="cvc" id="cardno" class="cc-cvc" autocomplete="off" placeholder="CVC" />
                                </flex-1>
                            </div>

                            <h6 class="blue">Discount</h6>
                            <br />
                            <input type="text" name="discount_code" id="discount_code" class="discount_code" autocomplete="discount_code" placeholder="Promo Code (Case Sensitive)" />


                        </fieldset>
                        <br />
        			</div>
                </flex-1>
                <flex-1 style="flex-basis:22rem;">
                    <div class="apply__form two-rem-left" style="">
                        <fieldset  class="inputGroup inputGroup--form form-group small" style="margin-top: 1rem;">
                            <h6 class="blue">Order Summary</h6>
                            <br />
                            <div class="center ">

                                <table class="order-summary" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td>Order Subtotal:</td>
                                        <td>$60.00</td>
                                    </tr>
                                    <tr>
                                        <td>Trust and Safety Fee:</td>
                                        <td>$5.00</td>
                                    </tr>
                                    <tr>
                                        <td>Discount:</td>
                                        <td>$10.00</td>
                                    </tr>
                                    <tr>
                                        <td>Tax:</td>
                                        <td>$8.00</td>
                                    </tr>
                                    <tr class="blue">
                                        <td>Estimated Quote:</td>
                                        <td>$62.00</td>
                                    </tr>
                                </table>

                                <input class="btn btn--primary" type="submit" value="Summon Porter" />
                                <br /><br />
                                <p>
                                You are charged only after your summon is completed. Summons have a one-hour minimum. a 5% Trust and Safety fee is added to the porter total rate. If you cancel your task within 24 hours of the scheduled start time, you will be charged a $50 cancellation fee.<br />
                                I agree to the terms of service &amp; privacy policy.
                                </p>
                            </div>
                        </fieldset>
                    </div>
                </flex-1>
    		</div>
        </form>
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
    });

    $('#discount_code').css('background-image', 'url(./images/tag-with-text.php?t=5.99)')
});
  </script>

<?php include '../snippets/footer.php'; ?>
