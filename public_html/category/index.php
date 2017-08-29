<?php
$pageClass = 'category';
$pageName = 'categories';

unset($bg_url);
if ($category_display_type == 'show_category') {
    $title = htmlentities($parent_categories[$category_id]['name']);
    $meta_description = htmlentities($parent_categories[$category_id]['description']);
    $bg_url = $parent_categories[$category_id]['webImage'];
}

include incl('snippets/header');
?>


<?php if ($category_display_type == 'show_category'): ?>
    <section class="banner category-banner nosearch" style="<?php if(isset($bg_url)) printf("background-image:url('%s');",httpStrip($bg_url)); ?>">
        <div class="inner">
            <div class="home__heroIntroInner">
               <h1><?=$parent_categories[$category_id]['name']?></h1>
           </div>
       </div>
   </section>
<?php else: ?>
    <section class="banner category-banner" style="<?php if(isset($bg_url)) printf("background-image:url('%s');",httpStrip($bg_url)); ?>">
        <div class="inner">
            <div class="home__heroIntroInner">
                <h1>What do you need help with?</h1>
                <form class="inputGroup inputGroup--search">
                    <input type="text" name="searchText" placeholder="What do you need help with?" id="tags" autocomplete="off">
                    <div class="submit" id="searchSubmit"><i class="icon-search"></i></div>
                </form>
            </div>
        </div>
    </section>
<?php endif; ?>


<?php include incl('snippets/banner'); ?>


<!-- TASKS -->
<section class="category-list verticalSpacing" id="tasks">
	<div class="inner">
		<h2 class="heading--center blue"><?=($category_display_type=='show_category'?'Choose a Summon below':'Choose a Summon')?></h2>
        <br />
		<div class="home__taskItems">
			<?php
			$tpl = '
			<div class="home__taskItem">
				<div style="background-image:url(%s)">
					<a href="%s" class="home__taskItemInner">
						<div class="home__taskItemCopy">
							<h4>%s</h4>
						</div>
					</a>
				</div>
			</div>';
				if ($category_display_type == 'show_category') {
					$relevant_services = array_filter($service_types, function($service) use ($category_id){
						return $service['group']['id'] == $category_id;
					});
					foreach ($relevant_services as $service_type) {
						printf (
							$tpl,
							httpStrip($service_type['webIcon']),
							makeUrl('/order/'.nameToSlug($service_type['group']['name']).'/'.nameToSlug($service_type['name']).'/'),
							$service_type['name']
						);
					}

				} else {

					foreach ($parent_categories as $category) {
						printf (
							$tpl,
							httpStrip($category['webImage']),
							makeUrl('/category/'.nameToSlug($category['name'])),
							$category['name']
						);
					}

    				foreach ($wildcard_services as $service) {
    					printf (
    						$tpl,
    						httpStrip($service['webIcon']),
    						makeUrl('/order/'.nameToSlug($service['name'])),
    						$service['name']
    					);
    				}
				}


			?>

		</div>
	</div>
</section>

<?php include incl('snippets/value-props') ?>
<?php include incl('snippets/autocomplete'); ?>
<?php include incl('snippets/footer'); ?>
