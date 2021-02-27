<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Airtable Key
    |--------------------------------------------------------------------------
    |
    | This value can be found in your Airtable account page:
    | https://airtable.com/account
    |
     */
    'key' => env('AIRTABLE_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Airtable Base
    |--------------------------------------------------------------------------
    |
    | This value can be found once you click on your Base on the API page:
    | https://airtable.com/api
    | https://airtable.com/[BASE_ID]/api/docs#curl/introduction
    |
     */
    'base' => env('AIRTABLE_BASE'),

    /*
    |--------------------------------------------------------------------------
    | Default Airtable Table
    |--------------------------------------------------------------------------
    |
    | This value can be found on the API docs page:
    | https://airtable.com/[BASE_ID]/api/docs#curl/table:tasks
    | The value will be hilighted at the beginning of each table section.
    | Example:
    | Each record in the `Tasks` contains the following fields
    |
     */
    'default' => 'default',

    'tables' => [

        'default' => [
            'name' => env('AIRTABLE_TABLE'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Default Airtable Client Settings
    |--------------------------------------------------------------------------
    |
    | This value can be found on the API docs page:
    | https://airtable.com/[BASE_ID]/api/docs#curl/table:tasks
    | The value will be hilighted at the beginning of each table section.
    | Example:
    | Each record in the `Tasks` contains the following fields
    |
     */
    
    'log_http' => env('AIRTABLE_LOG_HTTP', false),
    'log_http_format' => env('AIRTABLE_LOG_HTTP_FORMAT', '{request} >>> {res_body}'),

    'typecast' => env('AIRTABLE_TYPECAST', false),

    // The API is limited to 5 requests per second per base. 
    // If you exceed this rate, you will receive a 429 status code and will need to wait 30 seconds before a successful request.
    // This value is the delay in microseconds between subsequent requests.
    'delay_between_requests' => env('AIRTABLE_DELAY_BETWEEN_REQUESTS', 200000),
];
