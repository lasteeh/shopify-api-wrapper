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
    $headers = getallheaders();

    $api_key = $headers['Api-Key'] ?? '';
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
            $lineItem = $lineItemEdge['node']['lineItem'];
            $lineItems[] = [
              'lineItemID' => (string) $lineItem['sku'],
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
    if (!empty($query_response['fulfillmentOrderAcceptFulfillmentRequest']['userErrors'])) {
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

    $data['test'] = $query_response;

    $this->return_json($data);
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
    if (!empty($query_response['fulfillmentOrderAcceptFulfillmentRequest']['userErrors'])) {
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

    $this->return_json($data);
  }

  public function variants()
  {
    $input = file_get_contents('php://input');
    $json = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) $this->throw_error("Invalid JSON format: " . json_last_error_msg());

    // do graphql query here
    $product_id = $json['productId'] ?? null;
    $variants = $json['variants'] ?? [];
    $variant_inputs_for_price_update = [];

    foreach ($variants as $variant) {
      $variant_inputs_for_price_update[] =
        '{
          id: "' . $variant['variantID'] . '"
          ' . (isset($variant['price']) ? 'price: ' . $variant['price'] : '') . '
        }';
    }

    $price_mutation = [
      'query' => '
        mutation {
          productVariantsBulkUpdate(
            productId: "' . $product_id . '",
            variants: [' . implode(",", $variant_inputs_for_price_update)  . ']
          ) {
            product { id }
            productVariants {
              id
              price
              inventoryItem {
                id
                inventoryLevels(first: 250) {
                  nodes {
                    location {
                      id
                    }
                  }
                }
              }
              inventoryQuantity
            }
            userErrors {
              field
              message
            }
          }
        }
      ',
    ];

    $price_mutation_response = $this->request_graphql($price_mutation, $_ENV['SHOP_NAME'], $_ENV['ACCESS_TOKEN']);
    $price_response_errors = $price_mutation_response['data']['productVariantsBulkUpdate']['userErrors'] ?? [];

    if (!empty($price_response_errors)) {
      $data = [
        "status" => "error",
        "message" => "Invalid request",
      ];
      $this->return_json($data);
    }

    $variant_location_map = [];

    foreach ($price_mutation_response['data']['productVariantsBulkUpdate']['productVariants'] as $variant) {
      $variant_id = $variant['id'] ?? '';
      $variant_inventory_id = $variant['inventoryItem']['id'] ?? '';
      $variant_locations = $variant['inventoryItem']['inventoryLevels']['nodes'] ?? [];

      $variant_location_map[$variant_id] = [
        'locations' => $variant_locations,
        'inventoryItemId' => $variant_inventory_id,
      ];
    }


    $inventory_inputs_for_quantity_update = [];

    foreach ($variants as $variant) {
      $variant_inventory_id = $variant_location_map[$variant['variantID']]['inventoryItemId'] ?? '';
      $variant_quantity = (isset($variant['quantity']) ? 'delta: ' . $variant['quantity'] : '');

      $inventory_inputs_for_quantity_update[] =
        "\n" . '{
          inventoryItemId: "' . $variant_inventory_id . '" 
          ' . $variant_quantity . ' 
        }';
    }

    $inventory_mutation = [
      'query' => '
        mutation {
          inventoryAdjustQuantities(
            input: {
              changes: [' . implode(",", $inventory_inputs_for_quantity_update) . ']
              reason: "correction"
              name: "available"
            }
          ) {
            inventoryAdjustmentGroup {
              changes {
                name
                quantityAfterChange
              }
            }
          }
        }
      ',
    ];

    $inventory_mutation_response = $this->request_graphql($inventory_mutation, $_ENV['SHOP_NAME'], $_ENV['ACCESS_TOKEN']);

    $data = [];

    // return json here
    $this->return_json(['price_mutation_response' => $price_mutation_response, 'inventory_mutation_response' => $inventory_mutation_response, 'inventory_mutation_query' => $inventory_mutation, 'location_map' => $variant_location_map]);
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
    $this->throw_error("Invalid request. Only accepts 'application/json'.");
  }

  protected function authenticate_request()
  {
    $headers = getallheaders();
    if (!isset($headers['Api-Key'])) $this->throw_error("Api-Key required");

    $fulfillment_service = FulfillmentService::find_by(['api_key' => $headers['Api-Key']]);
    if (empty($fulfillment_service)) $this->throw_error("Invalid Api-Key");
  }

  private function return_json(array $data, int $status = 200)
  {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
  }
}
