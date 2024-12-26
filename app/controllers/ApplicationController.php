<?php

namespace App\Controllers;

use Core\Components\ActionController;

class ApplicationController extends ActionController
{
  public function not_found()
  {
    $this->render(status: 404);
  }

  public function forms()
  {
    $this->render();
  }

  protected function throw_error(string $message, int $status = 400)
  {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode([
      "status" => "error",
      "message" => $message
    ]);
    exit;
  }

  protected function request_graphql(array $query, string $shop, string $token, string $version = '2024-10')
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
