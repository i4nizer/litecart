<?php

# no token provided
if (empty($_GET['token']) || empty($_GET['id'])) { ?>


    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Provide Token</title>
    </head>

    <body>

        <div class="confirm-box">
            <h1>Please check your email for an email confirmation link.</h1>
            <a href="mailto:">Open Email App</a>
        </div>

        <style>
            body {
                width: 100%;
                height: 100dvh;
                margin: 0;
                padding: 0;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .confirm-box {
                width: auto;
                height: auto;
                padding: 2dvw 5dvw;
                border: 1px solid lightgray;
                border-radius: 8px;
                background-color: white;
            }
        </style>

    </body>

    </html>


<?php exit;
}



# token is provided

$token = $_GET['token'];
$customerId = $_GET['id'];

$customer_query = database::query(
    "select * from " . DB_TABLE_PREFIX . "customers
    where email_confirm_token = '" . database::input($token) . "'
    limit 1;"
);

if (!$customer = database::fetch($customer_query)) {
    http_response_code(400);
    die('Invalid or expired token');
}

# Mark the email as confirmed
database::query(
    "update " . DB_TABLE_PREFIX . "customers
        set is_email_confirmed = 1, email_confirm_token = null
        where id = " . (int)$customerId . "
        limit 1;"
);

# login user
customer::load($customerId);

# Redirect to the home page with a success message
notices::add('success', language::translate('success_your_customer_account_has_been_created', 'Your customer account has been created and confirmed.'));
header('Location: ' . document::ilink(''));
