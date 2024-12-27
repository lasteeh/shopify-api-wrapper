<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FORMS</title>

  <style>
    .limit-width {
      display: flex;
      flex-flow: column nowrap;
      max-width: 500px;
    }
  </style>
</head>

<body>
  <main style="min-height: 100vh; display: flex; flex-flow: row wrap; align-items: center; justify-content: center; align-content: center;">
    <form class="limit-width" id="get-fulfillment-orders" data-method="GET" data-endpoint="<?= $_ENV['HOME_URL']; ?>/fulfillment_orders">
      <p>GET /fulfillment_orders</p>
      <input type="text" name="status" placeholder="status" />
      <input type="text" name="orderID" placeholder="orderID" />
      <button type="submit">send</button>
    </form>
    <form class="limit-width" id="post-fulfillment-order-acceptance" data-method="POST" data-endpoint="<?= $_ENV['HOME_URL']; ?>/fulfillment_order_acceptance">
      <p>POST /fulfillment_order_acceptance</p>
      <input type="text" name="fulfillmentOrderID" placeholder="fulfillmentOrderID" />
      <button type="submit">send</button>
    </form>
    <form class="limit-width" id="post-fulfillment-orders-cancellation" data-method="POST" data-endpoint="<?= $_ENV['HOME_URL']; ?>/fulfillment_order_cancellation">
      <p>POST /fulfillment_order_cancellation</p>
      <input type="text" name="fulfillmentOrderID" placeholder="fulfillmentOrderID" />
      <button type="submit">send</button>
    </form>

    <form class="limit-width" id="post-fulfillments" data-method="POST" data-endpoint="<?= $_ENV['HOME_URL']; ?>/fulfillments">
      <p>POST /fulfillments</p>

      <div id="orders" style="margin-block-end: 1em;">
        <div class="order limit-width">
          <input type="text" name="orderId[]" placeholder="Order ID" required />

          <div class="fulfillmentOrders limit-width">
            <div class="fulfillmentOrder limit-width">
              <input type="text" name="fulfillmentOrderId[]" placeholder="Fulfillment Order ID" required />
              <input type="text" name="trackingCompany[]" placeholder="Tracking Company" required />
              <input type="text" name="trackingNumber[]" placeholder="Tracking Number" required />
              <input type="text" name="trackingURL[]" placeholder="Tracking URL" required />

              <div class="lineItems">
                <div class="lineItem">
                  <input type="text" name="lineItemID[]" placeholder="Line Item ID" />
                  <input type="number" name="productQuantity[]" placeholder="Product Quantity" />
                </div>
              </div>

              <button type="button" class="addLineItem">Add Another Line Item</button>
            </div>
          </div>

          <button type="button" class="addFulfillmentOrder">Add Another Fulfillment Order</button>
        </div>
      </div>

      <button type="button" class="addOrder">Add Another Order</button>
      <button type="submit">Send</button>
    </form>

    <form class="limit-width" id="get-products" data-method="GET" data-endpoint="<?= $_ENV['HOME_URL']; ?>/products">
      <p>GET /products</p>
      <input type="text" name="sku" placeholder="sku" />
      <button type="submit">send</button>
    </form>


    <form class="limit-width" id="patch-variants" data-method="PATCH" data-endpoint="<?= $_ENV['HOME_URL']; ?>/variants">
      <p>PATCH /variants</p>

      <!-- Product ID -->
      <input type="text" name="productId" placeholder="Product ID" required />
      <hr>

      <!-- Variants Container -->
      <div id="variants-container" class="limit-width">
        <!-- Variant 1 (Default) -->
        <div class="variant limit-width" style="margin-bottom: 1em;">
          <input type="text" name="variantID[]" placeholder="Variant ID" required />
          <input type="number" name="quantity[]" placeholder="Quantity" />
          <input type="number" name="price[]" placeholder="Price (in USD)" step="0.01" />
        </div>
      </div>

      <!-- Add New Variant Button -->
      <button type="button" id="addVariant">Add Another Variant</button>
      <br><br>

      <!-- Submit Button -->
      <button type="submit">Send</button>
    </form>
  </main>
</body>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const app = {
      api_key: "t5cL6y7vbV5b9XSrH3h8FEsM8VPgeoLj",

      get_fulfillment_orders: function() { // Get the form element and attach the submit event listener
        const form = document.getElementById("get-fulfillment-orders");

        form.addEventListener('submit', function(event) {
          event.preventDefault(); // Prevent the default form submission

          // Get the input values for status and orderID
          const status = form.querySelector('input[name="status"]').value;
          const orderID = form.querySelector('input[name="orderID"]').value;

          // Construct the query string with parameters
          const queryParams = new URLSearchParams({
            status: status,
            orderID: orderID
          });

          // Construct the full URL with query parameters
          const endpoint = form.dataset.endpoint + "?" + queryParams.toString();

          // Set the API key in the headers
          const headers = {
            'Content-Type': 'application/json',
            "API-KEY": app.api_key,
          };

          let options = {
            method: form.dataset.method,
            headers: headers
          }

          console.log(options);
          // Perform the GET request using fetch API
          fetch(endpoint, options)
            .then(response => response.json())
            .then(data => {
              // Handle the response data (e.g., log or display it)
              console.log("Fulfillment Orders:", data);
              // Optionally, display the data to the user or handle it further here
            })
            .catch(error => {
              // Handle any errors (e.g., network issues)
              console.error("Error fetching fulfillment orders:", error);
              // Optionally show an error message to the user
            });
        });
      },

      fulfillment_order_acceptance: function() {
        const form = document.getElementById("post-fulfillment-order-acceptance");

        form.addEventListener('submit', function(event) {
          event.preventDefault();

          // Get the fulfillmentOrderID value from the form input
          const fulfillmentOrderID = form.querySelector('input[name="fulfillmentOrderID"]').value;

          // Construct the body of the POST request
          const body = JSON.stringify({
            fulfillmentOrderID: fulfillmentOrderID
          });

          // Define headers and method
          const headers = {
            'Content-Type': 'application/json',
            "API-KEY": app.api_key
          };

          const options = {
            method: form.dataset.method,
            headers: headers,
            body: body // Attach the request body
          };

          // Perform the POST request
          fetch(form.dataset.endpoint, options)
            .then(response => response.json())
            .then(data => {
              // Handle the response data (e.g., log it or display it)
              console.log("Fulfillment Order Acceptance Response:", data);
            })
            .catch(error => {
              console.error("Error posting fulfillment order acceptance:", error);
            });
        });
      },

      fulfillment_order_cancellation: function() {
        const form = document.getElementById("post-fulfillment-orders-cancellation");

        form.addEventListener('submit', function(event) {
          event.preventDefault();

          // Get the fulfillmentOrderID value from the form input
          const fulfillmentOrderID = form.querySelector('input[name="fulfillmentOrderID"]').value;

          // Construct the body of the POST request
          const body = JSON.stringify({
            fulfillmentOrderID: fulfillmentOrderID
          });

          // Define headers and method
          const headers = {
            'Content-Type': 'application/json',
            "API-KEY": app.api_key
          };

          const options = {
            method: form.dataset.method,
            headers: headers,
            body: body // Attach the request body
          };

          // Perform the POST request
          fetch(form.dataset.endpoint, options)
            .then(response => response.json())
            .then(data => {
              // Handle the response data (e.g., log it or display it)
              console.log("Fulfillment Order Cancellation Response:", data);
            })
            .catch(error => {
              console.error("Error posting fulfillment order cancellation:", error);
            });
        });
      },

      get_products: function() {
        const form = document.getElementById("get-products");

        form.addEventListener('submit', function(event) {
          event.preventDefault();

          // Get the SKU value from the form input
          const sku = form.querySelector('input[name="sku"]').value;

          // Construct the query string
          const queryParams = new URLSearchParams({
            sku
          });

          // Construct the full URL with the query parameters
          const endpoint = form.dataset.endpoint + "?" + queryParams.toString();

          // Set the API key in the headers
          const headers = {
            'Content-Type': 'application/json',
            "API-KEY": app.api_key
          };

          // Define the GET request options
          const options = {
            method: form.dataset.method,
            headers: headers
          };

          // Perform the GET request using fetch API
          fetch(endpoint, options)
            .then(response => response.json())
            .then(data => {
              console.log("Products:", data);
              // Optionally, display the data to the user or handle it further
            })
            .catch(error => {
              console.error("Error fetching products:", error);
            });
        });
      },

      fulfillments: function() {
        const form = document.getElementById("post-fulfillments");

        // Add new order
        form.querySelector(".addOrder").addEventListener("click", function() {
          const ordersContainer = document.getElementById("orders");
          const newOrder = document.createElement("div");
          newOrder.classList.add("order");
          newOrder.classList.add("limit-width");
          newOrder.innerHTML = `
            <input type="text" name="orderId[]" placeholder="Order ID" required />
            <div class="fulfillmentOrders">
                <div class="fulfillmentOrder">
                    <input type="text" name="fulfillmentOrderId[]" placeholder="Fulfillment Order ID" required />
                    <input type="text" name="trackingCompany[]" placeholder="Tracking Company" required />
                    <input type="text" name="trackingNumber[]" placeholder="Tracking Number" required />
                    <input type="text" name="trackingURL[]" placeholder="Tracking URL" required />

                    <div class="lineItems">
                        <div class="lineItem">
                            <input type="text" name="lineItemID[]" placeholder="Line Item ID" required />
                            <input type="number" name="productQuantity[]" placeholder="Product Quantity" required />
                        </div>
                    </div>
                    
                    <button type="button" class="addLineItem">Add Another Line Item</button>
                </div>
            </div>
            <button type="button" class="addFulfillmentOrder">Add Another Fulfillment Order</button>
        `;
          ordersContainer.appendChild(newOrder);
        });

        // Add new fulfillment order
        form.addEventListener("click", function(event) {
          if (event.target.classList.contains("addFulfillmentOrder")) {
            const orderDiv = event.target.closest(".order");
            const fulfillmentOrdersContainer = orderDiv.querySelector(".fulfillmentOrders");
            const newFulfillmentOrder = document.createElement("div");
            newFulfillmentOrder.classList.add("fulfillmentOrder");
            newFulfillmentOrder.classList.add("limit-width");
            newFulfillmentOrder.innerHTML = `
                <input type="text" name="fulfillmentOrderId[]" placeholder="Fulfillment Order ID" required />
                <input type="text" name="trackingCompany[]" placeholder="Tracking Company" required />
                <input type="text" name="trackingNumber[]" placeholder="Tracking Number" required />
                <input type="text" name="trackingURL[]" placeholder="Tracking URL" required />
                <div class="lineItems">
                    <div class="lineItem">
                        <input type="text" name="lineItemID[]" placeholder="Line Item ID" required />
                        <input type="number" name="productQuantity[]" placeholder="Product Quantity" required />
                    </div>
                </div>
                <button type="button" class="addLineItem">Add Another Line Item</button>
            `;
            fulfillmentOrdersContainer.appendChild(newFulfillmentOrder);
          }

          // Add new line item
          if (event.target.classList.contains("addLineItem")) {
            const fulfillmentOrderDiv = event.target.closest(".fulfillmentOrder");
            const lineItemsContainer = fulfillmentOrderDiv.querySelector(".lineItems");
            const newLineItem = document.createElement("div");
            newLineItem.classList.add("lineItem");
            newLineItem.innerHTML = `
                <input type="text" name="lineItemID[]" placeholder="Line Item ID" required />
                <input type="number" name="productQuantity[]" placeholder="Product Quantity" required />
            `;
            lineItemsContainer.appendChild(newLineItem);
          }
        });

        // Handle form submission
        form.addEventListener('submit', function(event) {
          event.preventDefault();

          // Gather data from the form
          const orders = [];
          const orderIds = form.querySelectorAll('input[name="orderId[]"]');
          const fulfillmentOrderIds = form.querySelectorAll('input[name="fulfillmentOrderId[]"]');
          const trackingCompanies = form.querySelectorAll('input[name="trackingCompany[]"]');
          const trackingNumbers = form.querySelectorAll('input[name="trackingNumber[]"]');
          const trackingURLs = form.querySelectorAll('input[name="trackingURL[]"]');
          const lineItemIDs = form.querySelectorAll('input[name="lineItemID[]"]');
          const productQuantities = form.querySelectorAll('input[name="productQuantity[]"]');

          let currentOrder = null;
          let currentFulfillmentOrder = null;
          let lineItemIndex = 0;
          let fulfillmentOrderIndex = 0;

          // Loop through the orders and build the JSON structure
          for (let i = 0; i < orderIds.length; i++) {
            // Create a new order
            currentOrder = {
              orderId: orderIds[i].value,
              fulfillmentOrders: []
            };

            // Get fulfillment orders for the current order
            const currentFulfillmentOrders = currentOrder.fulfillmentOrders;
            while (fulfillmentOrderIndex < fulfillmentOrderIds.length && fulfillmentOrderIndex < trackingCompanies.length) {
              const fulfillmentOrderId = fulfillmentOrderIds[fulfillmentOrderIndex].value;
              const trackingCompany = trackingCompanies[fulfillmentOrderIndex].value;
              const trackingNumber = trackingNumbers[fulfillmentOrderIndex].value;
              const trackingURL = trackingURLs[fulfillmentOrderIndex].value;

              if (fulfillmentOrderId) {
                currentFulfillmentOrder = {
                  fulfillmentOrderId: fulfillmentOrderId,
                  trackingCompany: trackingCompany,
                  trackingNumber: trackingNumber,
                  trackingURL: trackingURL,
                  lineItems: []
                };

                // Add line items for this fulfillment order
                const currentLineItems = currentFulfillmentOrder.lineItems;
                while (lineItemIndex < lineItemIDs.length && lineItemIndex < productQuantities.length) {
                  const lineItemID = lineItemIDs[lineItemIndex].value;
                  const productQuantity = productQuantities[lineItemIndex].value;

                  if (!lineItemID || !productQuantity) {
                    break;
                  }

                  currentLineItems.push({
                    lineItemID: lineItemID,
                    productQuantity: parseInt(productQuantity)
                  });

                  lineItemIndex++;
                }

                // Add the fulfillment order to the list of this order
                currentFulfillmentOrders.push(currentFulfillmentOrder);
              }

              fulfillmentOrderIndex++;
              // Stop when we've reached the end of fulfillment orders for this order.
              if (fulfillmentOrderIndex >= orderIds.length || fulfillmentOrderIndex >= trackingCompanies.length) {
                break;
              }
            }

            // Add the current order to the orders array
            orders.push(currentOrder);
          }

          // Construct the final JSON body
          const requestBody = {
            orders: orders
          };

          // Send the request
          const headers = {
            'Content-Type': 'application/json',
            "API-KEY": app.api_key
          };

          const options = {
            method: form.dataset.method,
            headers: headers,
            body: JSON.stringify(requestBody) // Attach the JSON request body
          };

          console.log(options);
          fetch(form.dataset.endpoint, options)
            .then(response => response.json())
            .then(data => {
              console.log("Fulfillment Response:", data);
            })
            .catch(error => {
              console.error("Error posting fulfillment:", error);
            });
        });
      },

      patch_variants: function() {
        const form = document.getElementById("patch-variants");

        // Add new variant
        document.getElementById("addVariant").addEventListener("click", function() {
          const variantsContainer = document.getElementById("variants-container");
          const newVariant = document.createElement("div");
          newVariant.classList.add("variant");
          newVariant.classList.add("limit-width");
          newVariant.style.marginBottom = "1em"; // Style to match the original variant
          newVariant.innerHTML = `
            <input type="text" name="variantID[]" placeholder="Variant ID" required />
            <input type="number" name="quantity[]" placeholder="Quantity" />
            <input type="number" name="price[]" placeholder="Price (in USD)" step="0.01" />
          `;
          variantsContainer.appendChild(newVariant);
        });

        // Handle form submission
        form.addEventListener('submit', function(event) {
          event.preventDefault();

          // Gather data from the form
          const productId = form.querySelector('input[name="productId"]').value;
          const variantIDs = form.querySelectorAll('input[name="variantID[]"]');
          const quantities = form.querySelectorAll('input[name="quantity[]"]');
          const prices = form.querySelectorAll('input[name="price[]"]');

          // Construct variants array
          const variants = [];
          for (let i = 0; i < variantIDs.length; i++) {
            const variantID = variantIDs[i].value;
            const quantity = quantities[i] ? quantities[i].value : null; // Optional field
            const price = prices[i] ? prices[i].value : null; // Optional field

            if (!variantID) {
              continue; // Skip if no variant ID is provided
            }

            variants.push({
              productId: productId, // Required
              variantID: variantID, // Required
              quantity: quantity ? parseInt(quantity) : undefined, // Optional
              price: price ? parseFloat(price) : undefined, // Optional
            });
          }

          // Construct the final JSON request body
          const requestBody = {
            variants: variants
          };

          // Send the request
          const headers = {
            'Content-Type': 'application/json',
            "API-KEY": app.api_key
          };

          const options = {
            method: form.dataset.method,
            headers: headers,
            body: JSON.stringify(requestBody) // Attach the JSON request body
          };

          console.log(options);
          fetch(form.dataset.endpoint, options)
            .then(response => response.json())
            .then(data => {
              console.log("Variants Patch Response:", data);
            })
            .catch(error => {
              console.error("Error patching variants:", error);
            });
        });
      }
    };

    window.addEventListener("load", function() {
      app.get_fulfillment_orders();
      app.fulfillment_order_acceptance();
      app.fulfillment_order_cancellation();
      app.get_products();

      app.fulfillments();
      app.patch_variants();
    });
  });
</script>

</html>