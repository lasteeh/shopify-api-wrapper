<?php

namespace App\Controllers;

class ShopifyApiWrapperController extends ApplicationController
{
  protected static $before_action = [
    'only_accept_json' => [
      'except' => ['test'],
    ],
    'authenticate_request' => [
      'except' => ['test'],
    ],
  ];

  public function fulfillment_orders()
  {
    $status = (string) $_GET['status'] ?? null;
    $orderID = (string) $_GET['orderID'] ?? null;
    $shop_url = (string) $_ENV['SHOP_URL'] ?? null;
    $access_token = (string) $_ENV['ACCESS_TOKEN'] ?? null;

    $query = "
    {
      orders(query: \"fulfillment_status:$status\"){
        edges {
          node {
            id
            createdAt
            totalShippingPriceSet {
              presentmentMoney {
                amount
              }
            }
          }
        }
      }
    }
    ";

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


  public function test()
  {
    $query = [
      "query" => '{
      shop { 
        name 
      }  
    }
    ',
      'variables' => [
        "id" => "asdasdas"
      ],
    ];

    var_dump($this->request_graphql($query, $_ENV['SHOP_NAME'], $_ENV['ACCESS_TOKEN']));
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

  protected function authenticate_request()
  {
    $headers = getallheaders();

    if (!isset($headers['API-KEY'])) $this->throw_error("Missing API-KEY header");
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

  private function request_graphql(array $query, string $shop, string $token, string $version = '2024-10')
  {
    $url = "https://{$shop}.myshopify.com/admin/api/{$version}/graphql.json";

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);

    $headers = [
      'Content-Type: application/json',
      'X-Shopify-Access-Token: ' . $token
    ];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($query));
    $response = curl_exec($ch);

    if (curl_errno($ch)) $this->throw_error("Request failed: " . curl_error($ch));

    curl_close($ch);
    return json_decode($response, true);
  }
}
