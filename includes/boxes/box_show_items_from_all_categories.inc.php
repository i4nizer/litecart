<?php

$num_product = database::query(
    "SELECT `value` FROM " . DB_TABLE_PREFIX . "settings WHERE `key` = 'vmodcfg_scoip_num_items_per_category' LIMIT 1;"
);

// if this is not found, it means the vmod is not properly installed / broken
// so best to do nothing
if (database::num_rows($num_product) === 0) {
    trigger_error('box_show_items_from_all_categories are executed without proper precondition');
    return;
}

// otherwise, even if the value is wrong, safe to assume that we can use default value
// for the config
$num = database::fetch($num_product)['value'] ?? 10;

$categories_query = database::query(
    "SELECT  ci.name, c.id FROM " . DB_TABLE_PREFIX . "categories c " .
        "INNER JOIN " . DB_TABLE_PREFIX . "categories_info ci " .
        "ON c.id = ci.category_id"
);

$categories = [];
while ($category = database::fetch($categories_query)) {
    $products_in_category_query = functions::catalog_products_query([
        'categories' => [$category['id']],
        'limit' => $num,
    ]);

    if (!database::num_rows($products_in_category_query)) {
        continue;
    }

    functions::draw_lightbox();

    $box_items_per_category = new ent_view();
    $box_items_per_category->snippets['category_title'] = $category['name'];
    $box_items_per_category->snippets['products'] = [];
    while ($listing_products = database::fetch($products_in_category_query)) {
        $box_items_per_category->snippets['products'][] = $listing_products;
    }

    echo $box_items_per_category->stitch('views/box_show_items_from_all_categories');
}
