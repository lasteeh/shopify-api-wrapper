<?php

namespace App\Controllers;

use App\Models\FulfillmentService;

class FulfillmentServicesController extends ApplicationController

{
  public function index()
  {
    $services = FulfillmentService::all();

    $this->variables(['services' => $services]);
    $this->render();
  }

  public function create()
  {
    $errors = [];
    $notices = [];
    $fulfillment_service_graphql_params = $this->fulfillment_service_graphql_params();


    $query = [
      'query' => '
        mutation {
          fulfillmentServiceCreate(
            name: "' . $fulfillment_service_graphql_params['name'] . '",
            callbackUrl: "' . $_ENV['HOME_URL'] . '/fulfillment-services-callback",
          ) {
            fulfillmentService {
              id
              serviceName
              callbackUrl
              location {
                id
              }
            }
            userErrors {
              field
              message
            }
          }
        }',
    ];

    $query_response = $this->request_graphql($query, $_ENV['SHOP_NAME'], $_ENV['ACCESS_TOKEN']);
    $query_response_errors = $query_response['data']['fulfillmentServiceCreate']['userErrors'] ?? [];

    if (!empty($query_response_errors)) {
      foreach ($query_response_errors as $error) {
        $errors[] = $error['message'];
      }
    }

    if (empty($query_response_errors)) {
      $fulfillment_service_params = [
        'api_key' => $fulfillment_service_graphql_params['api'],
        'name' => $fulfillment_service_graphql_params['name'],
        'fulfillment_service_id' => $query_response['data']['fulfillmentServiceCreate']['fulfillmentService']['id'],
        'location_id' => $query_response['data']['fulfillmentServiceCreate']['fulfillmentService']['location']['id'],
      ];

      $fulfillment_service = new FulfillmentService;
      [$fulfillment_service, $fs_errors] = $fulfillment_service->register($fulfillment_service_params);

      $errors = array_merge($errors, $fs_errors);

      $location_edit_mutation = [
        'query' => '
          mutation {
            locationEdit(
              id: "' . $query_response['data']['fulfillmentServiceCreate']['fulfillmentService']['location']['id'] . '",
              input: {
                name: "' . $fulfillment_service_graphql_params['name'] . '",
                address: {
                  address1: "' . $fulfillment_service_graphql_params['address1'] ?? '' . '", 
                  address2: "' . $fulfillment_service_graphql_params['address2'] ?? '' . '", 
                  city: "' . $fulfillment_service_graphql_params['city'] ?? '' . '", 
                  countryCode: ' . $fulfillment_service_graphql_params['countryCode'] ?? '' . ', 
                  phone: "' . $fulfillment_service_graphql_params['phone'] ?? '' . '", 
                  zip: "' . $fulfillment_service_graphql_params['zip'] ?? '' . '"
                }
              }
            ) {
              userErrors {
                code
                field
                message
              }
              location {
                id
                name
                address {
                  address1
                  address2
                  city
                  country
                  countryCode
                  phone
                  zip
                }
              }
            }
          }
        ',
      ];

      $location_edit_mutation_response = $this->request_graphql($location_edit_mutation, $_ENV['SHOP_NAME'], $_ENV['ACCESS_TOKEN']);
      $location_edit_mutation_response_errors = $location_edit_mutation_response['data']['locationEdit']['userErrors'] ?? [];

      if (!empty($location_edit_mutation_response_errors)) {
        foreach ($location_edit_mutation_response_errors as $error) {
          $errors[] = $error['message'];
        }
      }
    }

    if (empty($errors)) $notices = ['Fulfillment service successfully created.'];
    $this->redirect("/fulfillment-services", flash: ['notices' => $notices, 'errors' => $errors]);
  }

  public function callback()
  {
    $data = json_decode(file_get_contents('php://input'), true);
    var_dump($data);
    exit;
  }

  private function fulfillment_service_graphql_params()
  {
    return $this->params_permit([
      'api',
      'name',
      'address1',
      'address2',
      'city',
      'zip',
      'countryCode',
      'phone'
    ], $_POST);
  }
}
