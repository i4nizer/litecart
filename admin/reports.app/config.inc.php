<?php

return $app_config = [
  'name' => language::translate('title_reports', 'Reports'),
  'default' => 'custom_sales',
  'priority' => 0,
  'theme' => [
    'color' => '#a17b54',
    'icon' => 'fa-pie-chart',
  ],
  'menu' => [
    [
      'title' => language::translate('title_custom_sales', 'Custom Sales'),
      'doc' => 'custom_sales',
      'params' => [],
    ],
    [
      'title' => language::translate('title_monthly_sales', 'Monthly Sales'),
      'doc' => 'monthly_sales',
      'params' => [],
    ],
    [
      'title' => language::translate('title_most_sold_products', 'Most Sold Products'),
      'doc' => 'most_sold_products',
      'params' => [],
    ],
    [
      'title' => language::translate('title_most_shopping_customers', 'Most Shopping Customers'),
      'doc' => 'most_shopping_customers',
      'params' => [],
    ],
  ],
  'docs' => [
    'custom_sales' => 'custom_sales.inc.php',
    'monthly_sales' => 'monthly_sales.inc.php',
    'most_sold_products' => 'most_sold_products.inc.php',
    'most_shopping_customers' => 'most_shopping_customers.inc.php',
  ],
];
