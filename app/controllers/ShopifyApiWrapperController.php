<?php

namespace App\Controllers;

use App\Models\FulfillmentService;

class ShopifyApiWrapperController extends ApplicationController
{
  protected static $before_action = [
    'only_accept_json',
    'authenticate_request',
  ];

  public function fulfillment_orders()
  {

    $headers = array_change_key_case(getallheaders(), CASE_LOWER);

    $api_key = $headers['api-key'] ?? '';
    $status = (string) $_GET['status'] ?? null; // assignmentStatus: FULFILLMENT_REQUESTED or CANCELLATION_REQUESTED only
    $orderID = (string) $_GET['orderID'] ?? null;

    if (empty($status)) $this->throw_error("Missing request params: status");

    $fulfillment_service = FulfillmentService::find_by(['api_key' => $api_key]);
    $location_id = $fulfillment_service->location_id;

    $query = [
      'query' => '
        query {
          assignedFulfillmentOrders(
            first: 250
            locationIds: "' . $location_id . '"
            assignmentStatus: ' . $status . '
          ) {
            edges {
              node {
                id
                orderId
                order {
                  customer {
                    firstName
                    lastName
                    email
                    phone
                    createdAt
                  }
                  totalPriceSet {
                    shopMoney {
                      amount
                    }
                  }
                  shippingAddress {
                    address1
                    address2
                    city
                    company
                    zip
                    province
                    countryCodeV2
                  }
                }
                lineItems(first: 250) {
                  edges {
                    node {
                      id
                      lineItem {
                        refundableQuantity
                        name
                        sku
                      }
                    }
                  }
                }
              }
            }
          }
        }',
    ];

    // do graphql query here
    $query_response = $this->request_graphql($query, $_ENV['SHOP_NAME'], $_ENV['ACCESS_TOKEN']);

    $response_code = 200;
    $data = [];

    if (!empty($query_response['errors'])) {
      $response_code = 400;
    }

    // return json here
    switch ($response_code) {
      case 200:
        $fulfillmentOrders = [];
        $responseFulfillmentOrders = $query_response['data']['assignedFulfillmentOrders']['edges'] ?? [];
        foreach ($responseFulfillmentOrders as $edge) {
          $node = $edge['node'];
          $order = $node['order'];

          $lineItems = [];
          foreach ($node['lineItems']['edges'] as $lineItemEdge) {
            $lineItemNode = $lineItemEdge['node'];
            $lineItem = $lineItemNode['lineItem'];
            $lineItems[] = [
              'lineItemID' => (string) $lineItemNode['id'],
              'productName' => (string) $lineItem['name'],
              'productSku' => (string) $lineItem['sku'],
              'productQuantity' => (int) $lineItem['refundableQuantity'],
            ];
          }

          if ((!empty($orderID) && $node['orderId'] === $orderID) || empty($orderID)) {
            $fulfillmentOrders[] = [
              'orderID' => (string) $node['orderId'],
              'fulfillmentOrderID' => (string) $node['id'],
              'customerFirstName' => (string) $order['customer']['firstName'],
              'customerLastName' => (string) $order['customer']['lastName'],
              'customerEmail' => (string) $order['customer']['email'],
              'customerPhone' => (string) $order['customer']['phone'],
              'orderDate' => (string) $order['customer']['createdAt'],
              'orderShippingTotal' => (float) $order['totalPriceSet']['shopMoney']['amount'],
              'lineItems' => $lineItems,
              'shippingAddress' => [
                'address1' => (string) $order['shippingAddress']['address1'],
                'address2' => (string) $order['shippingAddress']['address2'],
                'city' => (string) $order['shippingAddress']['city'],
                'company' => (string) $order['shippingAddress']['company'],
                'zip' => (string) $order['shippingAddress']['zip'],
                'province' => (string) $order['shippingAddress']['province'],
                'countryCode' => (string) $order['shippingAddress']['countryCodeV2'],
              ],
            ];
          }
        }

        $data = [
          "status" => (string) "success",
          "fulfillmentOrders" => $fulfillmentOrders,
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

    $this->return_json($data, $response_code);
  }

  public function fulfillment_order_acceptance()
  {
    $input = file_get_contents('php://input');
    $json = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) $this->throw_error("Invalid JSON format: " . json_last_error_msg());

    $fulfillmentOrderId = $json['fulfillmentOrderID'] ?? null;
    if (empty($fulfillmentOrderId)) $this->throw_error("Missing argument: fulfillmentOrderID");

    // do graphql query here
    $query = [
      'query' => '
        mutation {
          fulfillmentOrderAcceptFulfillmentRequest(
            id: "' . $fulfillmentOrderId . '"
          ) {
            userErrors {
              field
              message
            }
          }
        }
      ',
    ];

    $query_response = $this->request_graphql($query, $_ENV['SHOP_NAME'], $_ENV['ACCESS_TOKEN']);

    $data = [];
    $response_code = 200;
    if (!empty($query_response['data']['fulfillmentOrderAcceptFulfillmentRequest']['userErrors']) || !empty($query_response['errors'])) {
      $response_code = 400;
    }

    // return json here
    switch ($response_code) {
      case 200:
        $data = [
          "status" => (string) "success",
          "message" => "Fulfillment order in progress.",
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

    $this->return_json($data, $response_code);
  }

  public function fulfillment_order_cancellation()
  {
    $input = file_get_contents('php://input');
    $json = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) $this->throw_error("Invalid JSON format: " . json_last_error_msg());

    $fulfillmentOrderId = $json['fulfillmentOrderID'] ?? null;
    if (empty($fulfillmentOrderId)) $this->throw_error("Missing argument: fulfillmentOrderID");

    // do graphql query here
    $query = [
      'query' => '
        mutation {
          fulfillmentOrderRejectFulfillmentRequest(
            id: "' . $fulfillmentOrderId . '"
          ) {
            userErrors {
              field
              message
            }
          }
        }
      ',
    ];

    $query_response = $this->request_graphql($query, $_ENV['SHOP_NAME'], $_ENV['ACCESS_TOKEN']);

    $data = [];
    $response_code = 200;
    if (!empty($query_response['data']['fulfillmentOrderRejectFulfillmentRequest']['userErrors']) || !empty($query_response['errors'])) {
      $response_code = 400;
    }

    // return json here
    switch ($response_code) {
      case 200:
        $data = [
          "status" => (string) "success",
          "message" => "Fulfillment order rejected.",
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

    $this->return_json($data, $response_code);
  }

  public function fulfillments()
  {
    $input = file_get_contents('php://input');
    $json = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) $this->throw_error("Invalid JSON format: " . json_last_error_msg());

    $lineItems = [];
    $trackingNumbers = [];
    $trackingURLs = [];
    $fulfillmentOrderIds = [];

    foreach ($json['orders'] as $order) {
      foreach ($order['fulfillmentOrders'] as $fulfillmentOrder) {
        $currentLineItems = [];
        foreach ($fulfillmentOrder['lineItems'] as $lineItem) {
          $currentLineItems[] = [
            'id' => $lineItem['lineItemID'],
            'quantity' => $lineItem['productQuantity'],
          ];
        }

        $lineItems[] = [
          'fulfillmentOrderId' => $fulfillmentOrder['fulfillmentOrderId'],
          'fulfillmentOrderLineItems' => $currentLineItems,
        ];

        $fulfillmentOrderIds[] = $fulfillmentOrder['fulfillmentOrderId'];

        if (isset($fulfillmentOrder['trackingNumber'])) {
          $trackingNumbers[] = $fulfillmentOrder['trackingNumber'];
        };

        if (isset($fulfillmentOrder['trackingURL'])) {
          $trackingURLs[] = $fulfillmentOrder['trackingURL'];
        };
      }
    }



    // do graphql query here
    $query = [
      'query' => '
        mutation {
          fulfillmentCreate(
              fulfillment: {
                  lineItemsByFulfillmentOrder: ' . $this->array_to_graphql($lineItems) . ',
                  trackingInfo: {
                      company: "test company",
                      numbers: ' . $this->array_to_graphql($trackingNumbers) . ',
                      urls: ' . $this->array_to_graphql($trackingURLs) . '
                  }
              }
          ) {
              userErrors {
                  field
                  message
              }
          }
        }
      ',
    ];

    $query_response = $this->request_graphql($query, $_ENV['SHOP_NAME'], $_ENV['ACCESS_TOKEN']);


    $data = [];
    $response_code = 200;
    if (!empty($query_response['data']['fulfillmentCreate']['userErrors']) || !empty($query_response['errors'])) {
      $response_code = 400;
    }

    // return json here
    switch ($response_code) {
      case 200:
        $data = [
          "status" => (string) "success",
          "fulfillmentID" => $fulfillmentOrderIds[0],
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

    $this->return_json($data, $response_code);
  }

  public function products()
  {
    $sku = (string) $_GET['sku'] ?? null;

    // do graphql query here
    $query = [
      "query" => '{
        products(first: 1, query: "sku:' . $sku . '") {
          edges {
            node {
              id
              title
              variants(first: 250) {
                edges {
                  node {
                    id
                    title
                    inventoryQuantity
                  }
                }
              }
            }
          }
        }
      }
      ',
    ];

    $response = $this->request_graphql($query, $_ENV['SHOP_NAME'], $_ENV['ACCESS_TOKEN']);
    $product = $response['data']['products']['edges'][0]['node'] ?? [];
    $variants = $product['variants']['edges'] ?? [];

    $product_id = $product['id'] ?? '';
    $product_title = $product['title'] ?? '';
    $product_variants = [];

    foreach ($variants as $variant) {
      $variant_node = $variant['node'] ?? [];

      $product_variants[] = [
        "variantID" => $variant_node["id"] ?? '',
        "title" => $variant_node['title'] ?? '',
        "quantity" => $variant_node['inventoryQuantity'] ?? 0,
      ];
    }

    $response_code = 200;
    $data = [];

    if (empty($sku) || empty($product_id)) {
      $response_code = 400;
    }


    // return json here
    switch ($response_code) {
      case 200:
        $data = [
          "status" => (string) "success",
          "productID" => (string) $product_id,
          "title" => (string) $product_title,
          "variants" => (array) $product_variants,
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

    $this->return_json($data, $response_code);
  }

  public function variants()
  {
    $headers = array_change_key_case(getallheaders(), CASE_LOWER);

    $api_key = $headers['api-key'] ?? '';
    $fulfillment_service = FulfillmentService::find_by(['api_key' => $api_key]);
    $location_id = $fulfillment_service->location_id;

    $input = file_get_contents('php://input');
    $json = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) $this->throw_error("Invalid JSON format: " . json_last_error_msg());

    // do graphql query here
    $variants = $json['variants'] ?? [];
    $product_id = $variants[0]['productId'] ?? '';

    // check if all product id is same
    foreach ($variants as $variant) {
      $variant_product_id = $variant['productId'] ?? '';
      if ($product_id !== $variant_product_id) $this->throw_error("Invalid request: err:ProdID");
    }

    $variant_items = [];
    foreach ($variants as $variant) {
      if (!isset($variant['price']) && !isset($variant['quantity'])) continue;

      $variant_changes = [
        'id' => $variant['variantID'] ?? '',
      ];

      if (isset($variant['price'])) {
        $variant_changes['price'] =  (float) $variant['price'];
      }

      $variant_items[] = $variant_changes;
    }

    $product_mutation = [
      'query' => '
        mutation {
          productVariantsBulkUpdate(
            productId: "' . $product_id . '"
            variants: ' . $this->array_to_graphql($variant_items) . '
          ) {
            productVariants {
              id
              inventoryItem {
                id
              }
            }
            userErrors {
              field
              message
              code
            }
          }
        }
      ',
    ];

    $product_mutation_response = $this->request_graphql($product_mutation, $_ENV['SHOP_NAME'], $_ENV['ACCESS_TOKEN']);

    if (!empty($product_mutation_response['data']['productVariantsBulkUpdate']['userErrors']) || !empty($product_mutation_response['errors'])) {
      $this->throw_error("Invalid request: err:PrcMut");
    }

    $response_variants = $product_mutation_response['data']['productVariantsBulkUpdate']['productVariants'] ?? [];
    $variant_inventory_id_map = [];
    foreach ($response_variants as $response_variant) {
      $variant_inventory_id_map[$response_variant['id']] = $response_variant['inventoryItem']['id'];
    }

    $variants_inventory_changes = [];

    foreach ($variants as $variant) {
      if (!empty($variant_inventory_id_map[$variant['variantID']]) && isset($variant['quantity'])) {
        $variants_inventory_changes[] = [
          'delta' => $variant['quantity'],
          'inventoryItemId' => $variant_inventory_id_map[$variant['variantID']],
          'locationId' => $location_id,
        ];
      }
    }

    $inventory_mutation = [
      'query' => '
        mutation {
          inventoryAdjustQuantities(
            input: {
              name: "available", 
              changes: ' . $this->array_to_graphql($variants_inventory_changes) . ', 
              reason: "correction"
            }
          ) {
            userErrors {
              code
              field
              message
            }
          }
        }
      ',
    ];

    $inventory_mutation_response = $this->request_graphql($inventory_mutation, $_ENV['SHOP_NAME'], $_ENV['ACCESS_TOKEN']);

    $data = [];
    $response_code = 200;
    if (!empty($inventory_mutation_response['data']['inventoryAdjustQuantities']['userErrors']) || !empty($inventory_mutation_response['errors'])) {
      // $this->throw_error("Invalid request: err:invAdjQty");
      $response_code = 400;
    }

    // return json here
    switch ($response_code) {
      case 200:
        $data = [
          "status" => (string) "success",
          "message" => "Variant(s) updated",
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

    $this->return_json($data, $response_code);
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
    $headers = array_change_key_case(getallheaders(), CASE_LOWER);
    if (!empty($headers['content-type']) && $headers['content-type'] === 'application/json') return;
    $this->throw_error("Invalid request. Only accepts 'application/json'.");
  }

  protected function authenticate_request()
  {

    $headers = array_change_key_case(getallheaders(), CASE_LOWER);
    if (!isset($headers['api-key'])) $this->throw_error("API-KEY required");

    $fulfillment_service = FulfillmentService::find_by(['api_key' => $headers['api-key']]);
    if (empty($fulfillment_service)) $this->throw_error("Access denied", 403);
  }

  protected function return_json(array $data, int $status = 200)
  {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
  }

  private function array_to_graphql(array $array): string
  {
    $graphql = '[';
    if (!empty($array)) {
      foreach ($array as $item) {
        if (is_array($item)) {
          $graphql .= '{';
          foreach ($item as $key => $value) {
            if (is_array($value)) {
              $value = $this->array_to_graphql($value);
            } else if (is_string($value)) {
              $value = '"' . $value . '",';
            } else if (is_int($value)) {
              $value = (int) $value . ',';
            } else if (is_float($value)) {
              $value = (float) $value . ',';
            }
            $graphql .= "{$key}: {$value},";
          }
          $graphql = rtrim($graphql, ",") . '},';
        } else if (is_string($item)) {
          $graphql .= '"' . $item . '",';
        } else if (is_int($item)) {
          $graphql .= (int) $item . ',';
        } else if (is_float($item)) {
          $graphql .= (float) $item . ',';
        }
      }
    }
    $graphql = rtrim($graphql, ",") . ']';
    return $graphql;
  }
}
