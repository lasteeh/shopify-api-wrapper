<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FORMS</title>
</head>

<body>
  <main style="min-height: 100vh; display: flex; flex-flow: row wrap; align-items: center; justify-content: center; align-content: center;">
    <form data-method="GET" data-endpoint="http://localhost/projects/shopify-api-wrapper/fulfillment_orders" style="display: flex; flex-flow: column nowrap; max-width: 500px;">
      <p>GET /fulfillment_orders</p>
      <input type="text" name="status" placeholder="status" />
      <input type="text" name="orderID" placeholder="orderID" />
      <button type="submit">send</button>
    </form>
    <form data-method="POST" data-endpoint="http://localhost/projects/shopify-api-wrapper/fulfillment_order_acceptance" style="display: flex; flex-flow: column nowrap; max-width: 500px;">
      <p>POST /fulfillment_order_acceptance</p>
      <input type="text" name="fulfillmentOrderID" placeholder="fulfillmentOrderID" />
      <button type="submit">send</button>
    </form>
    <form data-method="POST" data-endpoint="http://localhost/projects/shopify-api-wrapper/fulfillment_order_cancellation" style="display: flex; flex-flow: column nowrap; max-width: 500px;">
      <p>POST /fulfillment_order_cancellation</p>
      <input type="text" name="fulfillmentOrderID" placeholder="fulfillmentOrderID" />
      <button type="submit">send</button>
    </form>
    <form data-method="POST" data-endpoint="http://localhost/projects/shopify-api-wrapper/fulfillments" style="display: flex; flex-flow: column nowrap; max-width: 500px;">
      <p>POST /fulfillments</p>
      <input type="text" name="orderID" placeholder="orderID" />
      <input type="text" name="fulfillmentOrderID" placeholder="fulfillmentOrderID" />
      <input type="text" name="trackingCompany" placeholder="trackingCompany" />
      <input type="text" name="trackingNumber" placeholder="trackingNumber" />
      <input type="text" name="trackingURL" placeholder="trackingURL" />
      <input type="text" name="lineItemID" placeholder="lineItemID" />
      <input type="text" name="productQuantity" placeholder="productQuantity" />
      <button type="submit">send</button>
    </form>
    <form data-method="GET" data-endpoint="http://localhost/projects/shopify-api-wrapper/products" style="display: flex; flex-flow: column nowrap; max-width: 500px;">
      <p>GET /products</p>
      <input type="text" name="sku" placeholder="sku" />
      <button type="submit">send</button>
    </form>
    <form data-method="PATCH" data-endpoint="http://localhost/projects/shopify-api-wrapper/variants" style="display: flex; flex-flow: column nowrap; max-width: 500px;">
      <p>PATCH /variants</p>
      <input type="text" name="variantID" placeholder="variantID" />
      <input type="text" name="quantity" placeholder="quantity" />
      <input type="text" name="price" placeholder="price" />
      <button type="submit">send</button>
    </form>
  </main>
</body>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const app = {
      test: function() {
        let allForms = document.querySelectorAll("form");

        allForms.forEach(form => {
          let method = form.getAttribute('data-method');
          let endpoint = form.getAttribute('data-endpoint');

          form.addEventListener("submit", (event) => {
            event.preventDefault();

            // Gather form data
            let formData = new FormData(form);
            let data = {};

            // Convert FormData to JSON object
            formData.forEach((value, key) => {
              data[key] = value;
            });

            // Create request options
            let options = {
              method: method,
              headers: {
                'Content-Type': 'application/json',
                'API-KEY': 'testapi',
              },
            };

            if (method !== "GET") {
              options.body = JSON.stringify(data);
            } else {
              // Append query params for GET request
              let queryString = new URLSearchParams(data).toString();
              endpoint += `?${queryString}`;
            }

            console.log(data);
            // Send the request
            fetch(endpoint, options)
              .then(response => response.json())
              .then(result => {
                console.log("Response:", result);
              })
              .catch(error => {
                console.error("Error:", error);
              });
          });
        });
      },
    };

    window.addEventListener("load", function() {
      app.test();
    });
  });
</script>

</html>