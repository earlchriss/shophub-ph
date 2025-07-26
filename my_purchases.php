    <?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>My Purchases</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            .order-image {
                width: 70px;
                height: 70px;
                object-fit: cover;
                border-radius: 6px;
                border: 1px solid #dee2e6;
            }

            .card-header h6 {
                font-size: 1rem;
                margin: 0;
            }

            .card-body small {
                color: #555;
            }
        </style>
    </head>

    <body>
        <div class="container py-5">
            <h2 class="mb-4">My Purchases</h2>
            <div id="orders-container">
                <div class="text-center text-muted">Loading your orders...</div>
            </div>
        </div>


        <!-- Review Modal -->
        <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow">
                    <div class="modal-header">
                        <h5 class="modal-title" id="reviewModalLabel">Submit Your Review</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="submit_review.php" method="POST">
                        <div class="modal-body">
                            <input type="hidden" name="order_id" id="modalOrderId">
                            <input type="hidden" name="product_id" id="modalProductId">

                            <!-- Star Rating -->
                            <div class="mb-4 text-center">
                                <label class="form-label d-block"><strong>Rating:</strong></label>
                                <div class="d-flex justify-content-center flex-row-reverse star-rating gap-1">
                                    <input type="radio" id="star5" name="rating" value="5" required>
                                    <label for="star5" class="text-warning"><i class="bi bi-star"></i></label>

                                    <input type="radio" id="star4" name="rating" value="4">
                                    <label for="star4" class="text-warning"><i class="bi bi-star"></i></label>

                                    <input type="radio" id="star3" name="rating" value="3">
                                    <label for="star3" class="text-warning"><i class="bi bi-star"></i></label>

                                    <input type="radio" id="star2" name="rating" value="2">
                                    <label for="star2" class="text-warning"><i class="bi bi-star"></i></label>

                                    <input type="radio" id="star1" name="rating" value="1">
                                    <label for="star1" class="text-warning"><i class="bi bi-star"></i></label>
                                </div>
                            </div>

                            <!-- Review Text -->
                            <div class="mb-3">
                                <label for="reviewText" class="form-label"><strong>Your Review:</strong></label>
                                <textarea class="form-control" id="reviewText" name="review" rows="4" required></textarea>
                            </div>
                        </div>

                        <div class="modal-footer justify-content-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <style>
            .star-rating input[type="radio"] {
                display: none;
            }

            .star-rating label i {
                transition: color 0.2s;
            }

            .star-rating input[type="radio"]:checked~label i,
            .star-rating input[type="radio"]:checked+label i {
                color: #ffc107;
                /* Yellow */
            }

            .star-rating label i::before {
                content: "\f586";
                /* Bootstrap icon: bi-star */
                font-family: "Bootstrap-icons";
            }

            .star-rating input[type="radio"]:checked~label i::before,
            .star-rating input[type="radio"]:checked+label i::before {
                content: "\f586";
                /* Default bi-star */
            }

            .star-rating input[type="radio"]:checked~label:nth-child(-n+10) i::before,
            .star-rating input[type="radio"]:checked+label i::before {
                content: "\f586";
                /* filled star fallback, will override via JS */
            }

            /* Optional: improve spacing and cursor */
            .star-rating label {
                cursor: pointer;
                font-size: 2rem;
            }
        </style>



        <script>
            document.addEventListener('DOMContentLoaded', () => {
                fetchOrders();
                setInterval(fetchOrders, 3000); // Auto-refresh every 3 seconds
            });

            function getStatusColor(status) {
                switch (status.toLowerCase()) {
                    case 'pending':
                        return 'secondary';
                    case 'processing':
                        return 'warning';
                    case 'shipped':
                        return 'info';
                    case 'delivered':
                        return 'success';
                    case 'cancelled':
                        return 'danger';
                    default:
                        return 'dark';
                }
            }

            function fetchOrders() {
                fetch('fetch_orders.php')
                    .then(response => {
                        if (!response.ok) throw new Error("Failed to fetch orders.");
                        return response.json();
                    })
                    .then(orders => {
                        const container = document.getElementById('orders-container');
                        container.innerHTML = '';

                        if (orders.length === 0) {
                            container.innerHTML = `<div class="alert alert-info">You have not placed any orders yet.</div>`;
                            return;
                        }

                        orders.forEach(order => {
                            const card = document.createElement('div');
                            card.className = 'card shadow-sm border-0 mb-4';
                            card.innerHTML = `
  <div class="card-header bg-light d-flex justify-content-between align-items-center">
    <div>
      <h6><strong>Order ID:</strong> ${order.order_id}</h6>
      <small class="text-muted">Placed on: ${new Date(order.order_date).toLocaleDateString()}</small>
    </div>
    <div class="text-end">
      <span class="badge bg-${getStatusColor(order.status)} mb-2">${order.status}</span><br>
      <div class="d-flex justify-content-end gap-2">
        <a href="track_order.php?order_id=${order.order_id}" class="btn btn-sm btn-outline-dark">Track Order</a>
        ${order.status === "Delivered" ? `
          <button class="btn btn-sm btn-outline-primary review-btn"
            data-order-id="${order.order_id}"
            data-product-id="${order.items[0].product_id}" 
            data-bs-toggle="modal"
            data-bs-target="#reviewModal">
            Review
          </button>
        ` : ''}
      </div>
    </div>
  </div>

  ${order.items.map(item => `
    <div class="d-flex align-items-start border-bottom py-3 px-3">
      <img src="admin/${item.product_image}" alt="${item.product_name}" class="me-3 order-image">
      <div>
        <h6 class="mb-1">${item.product_name}</h6>
        <small>Quantity: ${item.quantity} × ₱${parseFloat(item.price).toFixed(2)}</small><br>
        <small><strong>Subtotal:</strong> ₱${parseFloat(item.subtotal).toFixed(2)}</small>
      </div>
    </div>
  `).join('')}

  <div class="text-end px-3 py-2">
    <h6><strong>Total Paid: ₱${parseFloat(order.total).toFixed(2)}</strong></h6>
  </div>
`;

                            container.appendChild(card);
                        });
                    })
                    .catch(error => {
                        document.getElementById('orders-container').innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
                    });
            }


            document.querySelectorAll('input[name="star"]').forEach((starInput) => {
                starInput.addEventListener('change', function() {
                    document.getElementById('ratingInput').value = this.value;
                });
            });


            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('track-order-btn')) {
                    const orderId = e.target.dataset.id;
                    fetch(`get_order_tracking.php?order_id=${orderId}`)
                        .then(res => res.json())
                        .then(data => {
                            const timeline = document.getElementById('trackingTimeline');
                            timeline.innerHTML = '';

                            if (data.length === 0) {
                                timeline.innerHTML = `<li>No tracking information yet.</li>`;
                            } else {
                                data.forEach(step => {
                                    timeline.innerHTML += `
                                <li>
                                    <strong>${step.status}</strong><br>
                                    <small>${step.remarks}</small><br>
                                    <small class="text-muted">${new Date(step.timestamp).toLocaleString()}</small>
                                </li>
                            `;
                                });
                            }

                            new bootstrap.Modal(document.getElementById('trackingModal')).show();
                        })
                        .catch(() => {
                            alert("Failed to fetch tracking data.");
                        });
                }
            });

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('review-btn')) {
                    const orderId = e.target.dataset.orderId;
                    const productId = e.target.dataset.productId;

                    document.getElementById('reviewOrderId').value = orderId;
                    document.getElementById('reviewProductId').value = productId;

                    // Reset stars and text
                    document.querySelectorAll('input[name="rating"]').forEach(el => el.checked = false);
                    document.getElementById('review').value = '';
                }
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const reviewModal = document.getElementById('reviewModal');

                reviewModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const orderId = button.getAttribute('data-order-id');
                    const productId = button.getAttribute('data-product-id');

                    document.getElementById('review_order_id').value = orderId;
                    document.getElementById('review_product_id').value = productId;
                });

                // Optional: attach rating selection to hidden input
                document.querySelectorAll('input[name="star"]').forEach((input) => {
                    input.addEventListener('change', function() {
                        document.getElementById('ratingInput').value = this.value;
                    });
                });
            });
        </script>
        <script>
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('review-btn')) {
                    document.getElementById('modalOrderId').value = e.target.dataset.orderId;
                    document.getElementById('modalProductId').value = e.target.dataset.productId;
                }
            });
        </script>

        <script>
            const stars = document.querySelectorAll('.star-rating input');
            stars.forEach(star => {
                star.addEventListener('change', () => {
                    const value = parseInt(star.value);
                    const labels = document.querySelectorAll('.star-rating label i');
                    labels.forEach((icon, index) => {
                        icon.classList.remove('bi-star-fill');
                        icon.classList.add('bi-star');
                        if (4 - index < value) { // index 0 is star5
                            icon.classList.remove('bi-star');
                            icon.classList.add('bi-star-fill');
                        }
                    });
                });
            });
        </script>


        <!-- Bootstrap JS (required for modal) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    </body>

    </html>