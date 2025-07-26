<?php
require 'db.php';
require 'includes/header.php';

// Optional: Admin session check
// if (!isset($_SESSION['admin_id'])) {
//     header("Location: login.php");
//     exit();
// }

$sql = "SELECT 
            o.order_id, 
            u.username, 
            o.total, 
            o.status, 
            o.order_date 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.order_date DESC";
$result = $conn->query($sql);
?>


<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">
    <!-- Top Navbar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Products</h2>
        <button class="btn btn-dark"><i class="bi bi-box-arrow-right"></i> Logout</button>
    </div>



    <!-- Product Table -->
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Latest Orders</h5>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total (₱)</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="orders-table-body">
                    <!-- Orders will be loaded here via AJAX -->
                </tbody>
            </table>
            <!-- View Order Modal -->
            <div class="modal fade" id="viewOrderModal" tabindex="-1" aria-labelledby="viewOrderLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Order Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="order-details-content">
                            <!-- Order content loaded here via AJAX -->
                            <div class="text-center">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>

</main>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
    function fetchOrders() {
        $.ajax({
            url: 'fetch_orders.php',
            method: 'GET',
            success: function(response) {
                $('#orders-table-body').html(response);
            }
        });
    }

    // Load orders on page load
    fetchOrders();

    // Poll every 3 seconds
    setInterval(fetchOrders, 3000);

    // View order details
    $(document).on('click', '.view-order-btn', function() {
        const orderId = $(this).data('id');
        $('#viewOrderModal').modal('show');

        $.get('get_order_details.php', {
            order_id: orderId
        }, function(data) {
            $('#order-details-content').html(data);
        });
    });

    // Update order status
    $(document).on('submit', '#updateOrderForm', function(e) {
        e.preventDefault();
        $.post('update_order_status.php', $(this).serialize(), function(response) {
            alert(response.message);
            $('#viewOrderModal').modal('hide');
            fetchOrders();
        }, 'json');
    });
</script>
<script>
    // Accept order
    $(document).on('click', '.accept-order-btn', function() {
        const orderId = $(this).data('id');

        if (confirm("Are you sure you want to accept this order?")) {
            $.ajax({
                url: 'accept_order.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    order_id: orderId
                },
                success: function(response) {
                    if (response.success) {
                        alert('Order accepted successfully.');
                        fetchOrders(); // Refresh table to show Print button
                    } else {
                        alert(response.message || 'Failed to accept order.');
                    }
                },
                error: function() {
                    alert('Server error while accepting order.');
                }
            });
        }
    });

    // Print receipt
    $(document).on('click', '.print-receipt-btn', function() {
        const orderId = $(this).data('id');

        $.ajax({
            url: 'print_receipt.php',
            type: 'GET',
            data: {
                order_id: orderId
            },
            success: function(receiptHtml) {
                const printWindow = window.open('', '', 'width=800,height=600');
                printWindow.document.open();
                printWindow.document.write(receiptHtml);
                printWindow.document.close();

                printWindow.onload = function() {
                    printWindow.focus();
                    printWindow.print();
                };
            },
            error: function() {
                alert('Failed to load receipt.');
            }
        });
    });
</script>




<?php include 'includes/footer.php'; ?>