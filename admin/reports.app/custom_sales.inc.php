<?php

document::$snippets['title'][] = language::translate('title_monthly_sales', 'Monthly Sales');

breadcrumbs::add(language::translate('title_reports', 'Reports'));
breadcrumbs::add(language::translate('title_monthly_sales', 'Monthly Sales'));

$_GET['date_from'] = !empty($_GET['date_from']) ? date('Y-m-d', strtotime($_GET['date_from'])) : date('Y-01-01 00:00:00');
$_GET['date_to'] = !empty($_GET['date_to']) ? date('Y-m-d', strtotime($_GET['date_to'])) : date('Y-m-d');

if ($_GET['date_from'] > $_GET['date_to']) list($_GET['date_from'], $_GET['date_to']) = [$_GET['date_to'], $_GET['date_from']];

if ($_GET['date_from'] > date('Y-m-d')) $_GET['date_from'] = date('Y-m-d');
if ($_GET['date_to'] > date('Y-m-d')) $_GET['date_to'] = date('Y-m-d');

// Table Rows
$rows = [];

$orders_query = database::query(
    " SELECT * FROM " . DB_TABLE_PREFIX . "orders o
        INNER JOIN " . DB_TABLE_PREFIX . "order_statuses os ON os.id = o.order_status_id
        WHERE os.state = 'completed' or os.state = 'delivered'
    order by o.date_updated desc"
);

$total = 0;
while($row = database::fetch($orders_query)) { 
    if(!$row) continue;
    array_push($rows, $row);
    $total += $row['payment_due'];
}

if (isset($_GET['download'])) {

    // Execute the SQL query
    $sql = "
    SELECT lc_orders.*, lc_order_statuses.state 
    FROM lc_orders 
    INNER JOIN lc_order_statuses ON lc_order_statuses.id = lc_orders.order_status_id 
    WHERE lc_order_statuses.state = 'completed' OR lc_order_statuses.state = 'delivered'
  ";
    $result = database::query($sql);

    // Set headers to force download
    header('Content-Type: application/csv; charset=' . language::$selected['charset']);
    header('Content-Disposition: filename="custom_sales_' . date('Ymd', strtotime($_GET['date_from'])) . '-' . date('Ymd', strtotime($_GET['date_to'])) . '.csv"');

    // Open output stream
    $output = fopen('php://output', 'w');

    // Add CSV column headers
    $header = [];
    while ($field_info = $result->fetch_field()) {
        $header[] = $field_info->name; // Get column names dynamically
    }
    fputcsv($output, $header);

    // Initialize total amount
    $total_amount = 0;

    // Fetch data rows and calculate total amount
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
        // Assuming the amount column is named 'amount'
        $total_amount += (float)$row['payment_due'];
    }

    // Add a last row for the total amount
    fputcsv($output, ['Total Amount', '', '', '', $total_amount]); // Adjust number of empty fields as necessary

    // Close the output stream
    fclose($output);

    # ------------------------------------------------------ Custom

    exit;
}
?>
<style>
    form[name="filter_form"] li {
        vertical-align: middle;
    }
</style>

<div class="card card-app">
    <div class="card-header">
        <div class="card-title">
            <?php echo $app_icon; ?> <?php echo language::translate('title_custom_sales', 'Custom Sales'); ?>
        </div>
    </div>

    <div class="card-action">
        <?php echo functions::form_draw_form_begin('filter_form', 'get'); ?>
        <?php echo functions::form_draw_hidden_field('app'); ?>
        <?php echo functions::form_draw_hidden_field('doc'); ?>
        <ul class="list-inline">
            <li><?php echo language::translate('title_date_period', 'Date Period'); ?>:</li>
            <li>
                <div class="input-group" style="max-width: 380px;">
                    <?php echo functions::form_draw_date_field('date_from'); ?>
                    <span class="input-group-text"> - </span>
                    <?php echo functions::form_draw_date_field('date_to'); ?>
                </div>
            </li>
            <li><?php echo functions::form_draw_button('filter', language::translate('title_filter_now', 'Filter')); ?></li>
            <li><?php echo functions::form_draw_button('download', language::translate('title_download', 'Download')); ?></li>
        </ul>
        <?php echo functions::form_draw_form_end(); ?>
    </div>

    <table class="table table-striped table-hover data-table">
        <thead>
            <tr>
                <th width="100%"><?php echo language::translate('title_customers', 'Customer'); ?></th>
                <th class="border-left text-center"><?php echo language::translate('title_order_id', 'Order ID'); ?></th>
                <th class="border-left text-center"><?php echo language::translate('title_total_price', 'Total Price'); ?></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($rows as $row) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['customer_firstname']).' '. htmlspecialchars($row['customer_lastname']) ?></td>
                    <td class="border-left text-end"><?= htmlspecialchars($row['id']) ?></td>
                    <td class="border-left text-end"><?= htmlspecialchars($row['currency_code']) . htmlspecialchars($row['payment_due']) ?></td>
                </tr>
            <?php } ?>
        </tbody>

        <tfoot>
            <tr>
                <td class="text-start"><?= strtoupper(language::translate('title_total', 'Total')); ?></td>
                <td class="border-left text-end"></td>
                <td class="border-left text-end"><?= settings::get('store_currency_code') . $total ?></td>
            </tr>
        </tfoot>
    </table>
</div>