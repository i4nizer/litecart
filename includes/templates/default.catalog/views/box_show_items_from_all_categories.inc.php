<section id="box-items-from-all-categories" class="card">

    <div class="card-header">
        <h2 class="card-title"><?php echo $category_title; ?></h2>
    </div>

    <div class="card-body">
        <div class="listing products columns">
            <?php foreach ($products as $product) echo functions::draw_listing_product($product); ?>
        </div>
    </div>

</section>