<?php

namespace App\Controllers;

class ShopifyApiWrapperController extends ApplicationController
{
  protected static $before_action = [
    'only_accept_json',
  ];

  public function fulfillment_orders()
  {
    $status = (string) $_GET['status'] ?? null;
    $orderID = (string) $_GET['orderID'] ?? null;

    // do graphql query here
    $response_code = 200;
    $data = [];

    // return json here
    switch ($response_code) {
      case 200:
        $data = [
          "status" => (string) "success",
          "fulfillmentOrders" => (array) [
            [
              "orderID" => (string) "string",
              "customerFirstName" => (string) "string",
              "customerLastName" => (string) "string",
              "customerEmail" => (string) "string",
              "customerPhone" => (string) "string",
              "orderDate" => (string) "string",
              "orderShippingTotal" => (float) 0.1,
              "lineItems" => (array) [
                [
                  "lineItemID" => (string) "string",
                  "productName" => (string) "string",
                  "productSku" => (string) "string",
                  "productQuantity" => (int) 1,
                ],
              ],
              "shippingAddress" => (array) [
                "address1" => (string) "string",
                "address2" => (string) "string",
                "city" => (string) "string",
                "company" => (string) "string",
                "zip" => (string) "string",
                "province" => (string)"string",
                "countryCode" => (string)"string",
              ],
            ],
          ],
        ];
        break;
      case 400:
        $data = [
          "status" => "error",
          "message" => "Invalid request",
        ];
        break;
      case 403:
        $data = [
          "status" => "error",
          "message" => "Access denied",
        ];
        break;
      case 429:
        $data = [
          "status" => "error",
          "message" => "Too many requests",
        ];
        break;
    }

    $this->return_json($data);
  }

  public function fulfillment_order_acceptance()
  {
    $input = file_get_contents('php://input');
    $json = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) $this->throw_error("Invalid JSON format: " . json_last_error_msg());

    // do graphql query here
    $data = [];


    // return json here
    $data = $json;

    $this->return_json($data);
  }

  public function fulfillment_order_cancellation()
  {
    $input = file_get_contents('php://input');
    $json = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) $this->throw_error("Invalid JSON format: " . json_last_error_msg());

    // do graphql query here
    $data = [];


    // return json here
    $data = $json;

    $this->return_json($data);
  }

  public function fulfillments()
  {
    $input = file_get_contents('php://input');
    $json = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) $this->throw_error("Invalid JSON format: " . json_last_error_msg());

    // do graphql query here
    $data = [];


    // return json here
    $data = $json;

    $this->return_json($data);
  }

  public function products()
  {
    $status = (string) $_GET['sku'] ?? null;

    // do graphql query here
    $response_code = 200;
    $data = [];

    // return json here
    switch ($response_code) {
      case 200:
        $data = [
          "status" => (string) "success",
          "productID" => (string) "string",
          "variants" => (array) [
            [
              "variantID" => (string) "string",
              "title" => (string) "string",
              "quantity" => (int) 1,
            ],
          ],
        ];
        break;
      case 400:
        $data = [
          "status" => "error",
          "message" => "Invalid request",
        ];
        break;
      case 403:
        $data = [
          "status" => "error",
          "message" => "Access denied",
        ];
        break;
      case 429:
        $data = [
          "status" => "error",
          "message" => "Too many requests",
        ];
        break;
    }

    $this->return_json($data);
  }

  public function variants()
  {
    $input = file_get_contents('php://input');
    $json = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) $this->throw_error("Invalid JSON format: " . json_last_error_msg());

    // do graphql query here
    $data = [];


    // return json here
    $data = $json;

    $this->return_json($data);
  }


  /************************/
  /************************/
  /************************/
  // helpers function below
  /************************/
  /************************/
  /************************/


  protected function only_accept_json()
  {
    if ($_SERVER['CONTENT_TYPE'] === 'application/json') return;
    $this->throw_error("Invalid request");
  }

  private function throw_error(string $message)
  {
    header('Content-Type: application/json');
    echo json_encode([
      "status" => "error",
      "message" => $message
    ]);
    exit;
  }

  private function return_json(array $data)
  {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
  }
}
