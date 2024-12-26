<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FORMS</title>
</head>

<body>
  <main style="min-height: 100vh; display: flex; flex-flow: row wrap; align-items: center; justify-content: center; align-content: center;">
    <form data-method="GET" data-endpoint="<?= $_ENV['HOME_URL']; ?>/fulfillment_orders" style="display: flex; flex-flow: column nowrap; max-width: 500px;">
      <p>GET /fulfillment_orders</p>
      <input type="text" name="status" placeholder="status" />
      <input type="text" name="orderID" placeholder="orderID" />
      <button type="submit">send</button>
    </form>
    <form data-method="POST" data-endpoint="<?= $_ENV['HOME_URL']; ?>/fulfillment_order_acceptance" style="display: flex; flex-flow: column nowrap; max-width: 500px;">
      <p>POST /fulfillment_order_acceptance</p>
      <input type="text" name="fulfillmentOrderID" placeholder="fulfillmentOrderID" />
      <button type="submit">send</button>
    </form>
    <form data-method="POST" data-endpoint="<?= $_ENV['HOME_URL']; ?>/fulfillment_order_cancellation" style="display: flex; flex-flow: column nowrap; max-width: 500px;">
      <p>POST /fulfillment_order_cancellation</p>
      <input type="text" name="fulfillmentOrderID" placeholder="fulfillmentOrderID" />
      <button type="submit">send</button>
    </form>
    <form data-method="POST" data-endpoint="<?= $_ENV['HOME_URL']; ?>/fulfillments" style="display: flex; flex-flow: column nowrap; max-width: 500px;">
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
    <form data-method="GET" data-endpoint="<?= $_ENV['HOME_URL']; ?>/products" style="display: flex; flex-flow: column nowrap; max-width: 500px;">
      <p>GET /products</p>
      <input type="text" name="sku" placeholder="sku" value="TESTSKU1" />
      <button type="submit">send</button>
    </form>
    <form data-method="PATCH" data-endpoint="<?= $_ENV['HOME_URL']; ?>/variants" style="display: flex; flex-flow: column nowrap; max-width: 500px;">
      <p>PATCH /variants</p>
      <!-- Product ID -->
      <input type="text" name="productId" placeholder="productId" value="gid://shopify/Product/9657989890333" />
      <hr>
      <!-- Variant 1 -->
      <input type="text" name="variantID[]" placeholder="variantID" value="gid://shopify/ProductVariant/49442088714525" />
      <input type="text" name="quantity[]" placeholder="quantity" value="12" />
      <input type="text" name="price[]" placeholder="price" value="66" />

      <!-- Variant 2 -->
      <input type="text" name="variantID[]" placeholder="variantID" value="gid://shopify/ProductVariant/49442088747293" />
      <input type="text" name="quantity[]" placeholder="quantity" value="12" />
      <input type="text" name="price[]" placeholder="price" value="66" />

      <!-- Variant 3 -->
      <input type="text" name="variantID[]" placeholder="variantID" value="gid://shopify/ProductVariant/49442088780061" />
      <input type="text" name="quantity[]" placeholder="quantity" value="12" />
      <input type="text" name="price[]" placeholder="price" value="66" />
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

            // If the method is PATCH, handle multiple variants
            if (method === "PATCH") {
              let productId = formData.get('productId');
              let variants = [];
              let variantIDs = formData.getAll('variantID[]');
              let quantities = formData.getAll('quantity[]');
              let prices = formData.getAll('price[]');

              // Loop through the arrays and create an array of variant objects
              for (let i = 0; i < variantIDs.length; i++) {
                variants.push({
                  variantID: variantIDs[i],
                  quantity: quantities[i],
                  price: prices[i]
                });
              }

              data['productId'] = productId;
              data['variants'] = variants;
            } else {
              // For other methods, convert formData to a regular object
              formData.forEach((value, key) => {
                data[key] = value;
              });
            }

            // Create request options
            let options = {
              method: method,
              headers: {
                'Content-Type': 'application/json',
                // 'API-KEY': 't5cL6y7vbV5b9XSrH3h8FEsM8VPgeoLj',
                'API-KEY': '34YlE5kpTLWk3Bj6xJdVnKcyvhN5cy0x',
              },
            };

            if (method !== "GET") {
              options.body = JSON.stringify(data);
            } else {
              // For GET method, handle query parameters
              let queryString = new URLSearchParams(data).toString();

              // Check if the endpoint already contains a query string
              if (endpoint.includes('?')) {
                endpoint += '&' + queryString;
              } else {
                endpoint += '?' + queryString;
              }
            }

            console.log(options);
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