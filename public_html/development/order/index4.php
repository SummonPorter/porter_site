<?php
$pageClass = 'order';
$pageName = 'order1';
include '../snippets/header.php';
?>


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
			<h1 class="blue">Your Details</h1>
			<div class="apply__form">
				<form method="post" action="order/index5.php">

                    <fieldset  class="inputGroup inputGroup--form " style="margin-top: 1rem;">
                        <div class="form2col">
                            <label for="firstname">Firstname</label>
                            <input type="text" name="firstname" id="firstname" placeholder="First name" />
                            <label for="lastname">Lastname</label>
                            <input type="text" name="lastname" id="lastname" placeholder="Last name" />
                            <label for="mobile">Mobile</label>
                            <input type="text" name="mobile" class="mobile-bg" id="mobile" placeholder="Mobile Number" />

                            <label for="email">Email</label>
                            <input type="text" name="email" class="email-bg" id="email" placeholder="Email Address" />
                            <i class="fa fa-email-after"></i>
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

<script type="text/javascript">
$(function(){
    $('#streetaddress').focus();
    setInterval(function(){
        $('textarea').each(function(){
            $(this).css('height', $(this)[0].scrollHeight+'px');
        });
    }, 500);

	$('input[type="text"], input[type="tel"]').keyup(function(){
		$(this).prev().css('opacity', this.value.length == 0 ? 0 : 1);
	})
})
</script>

<?php include '../snippets/footer.php'; ?>
