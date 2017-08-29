<?php
$pageClass = 'order';
$pageName = 'order1';

include '../snippets/header.php';

$page_data = null;

foreach ($service_data as $type) {
    if ($type['id'] == $_GET['page']) {
        $page_data = $type;
    }
}

if ($page_data === null) {
    header("HTTP/1.1 404 Not Found");
    $page_data = [
        'name' => '404 Not Found'
    ];
}


?>
<input id="service-id" value="<?=$page_data['id']?>" type="hidden" />
<style type="text/css">
#banner-bg {
    background-image:url('<?=$page_data['webHeader']?>');
    background-size: cover;
}
@media (max-width:48rem) {
    #banner-bg {
        background-image:url('<?=$page_data['webMobileHeader']?>');
    }
}
</style>
<style type="text/css" id="cssConsole">
</style>
<section class="banner order-banner" id="banner-bg" style="">

    <div class="inner">
        <h3 class="nob">Task Summary</h3>

        <div class="whitebg-rounded" style="margin-top: 0.6rem">

            <div class="order description ">
                <h2 class="blue"><?=$page_data['name']?> </h2>

                <p><?=$page_data['description']?></p>

            </div>

            <i class="clear"></i>
        </div>
    </div>
</section>



<section class="order1 graybg" id="summon-details">
	<div class="inner">
		<div class="">
			<div class="center ">
    			<h1 class="blue">Success!</h1>


          <img src="/images/success.svg" style="width: 25%; margin: 2rem auto; display: block;" alt="Success!" />

          <p class="manatee">A Porter has been summoned. An email has been sent with follow up instructions.</p>
      </div>
		</div>
	</div>
</section>

<?php

$mapCallback = 'initAutocomplete';
include '../snippets/map-api.php';

include '../snippets/footer.php'; ?>
