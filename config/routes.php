<?php

use Core\Components\Route;


// shopify api wrapper
Route::get('/fulfillment_orders', 'ShopifyApiWrapper@fulfillment_orders');
Route::post('/fulfillment_order_acceptance', 'ShopifyApiWrapper@fulfillment_order_acceptance');
Route::post('/fulfillment_order_cancellation', 'ShopifyApiWrapper@fulfillment_order_cancellation');
Route::post('/fulfillments', 'ShopifyApiWrapper@fulfillments');
Route::get('/products', 'ShopifyApiWrapper@products');
Route::patch('/variants', 'ShopifyApiWrapper@variants');




Route::catchall('Application@not_found');

// test route
Route::get('/test', 'Application@test');
