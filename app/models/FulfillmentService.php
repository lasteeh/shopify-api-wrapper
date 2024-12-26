<?php

namespace App\Models;

class FulfillmentService extends ApplicationRecord
{
  public static $TABLE = 'fulfillment_services';

  public $id;
  public $api_key;
  public $name;
  public $fulfillment_service_id;
  public $location_id;
  public $created_at;
  public $updated_at;

  protected $validations = [
    'api_key' => [
      'presence' => true,
      'uniqueness' => true,
    ],
    'name' => [
      'presence' => true,
      'uniqueness' => true,
    ],
    'fulfillment_service_id' => [
      'presence' => true,
      'uniqueness' => true,
    ],
    'location_id' => [
      'presence' => true,
      'uniqueness' => true,
    ],
  ];

  public function register(array $params)
  {
    $fulfillment_service = new static($params);
    $fulfillment_service->save();

    return [$fulfillment_service, $fulfillment_service->errors()];
  }
}
