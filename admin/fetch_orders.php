<?php
require '../includes/db.php';

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

$output = '';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $output .= '<tr>
            <td>' . htmlspecialchars($row['order_id']) . '</td>
            <td>' . htmlspecialchars($row['username']) . '</td>
            <td>₱' . number_format($row['total'], 2) . '</td>
            <td><span class="badge ' . ($row['status'] == 'Pending' ? 'bg-warning' : 'bg-success') . '">' . htmlspecialchars($row['status']) . '</span></td>
            <td>' . date('F j, Y', strtotime($row['order_date'])) . '</td>
            <td>';

        if ($row['status'] === 'Pending') {
            $output .= '<button class="btn btn-success btn-sm accept-order-btn" data-id="' . $row['order_id'] . '"><i class="bi bi-check-circle"></i> Accept</button>';
        } else {
            $output .= '<button class="btn btn-primary btn-sm view-order-btn" data-id="' . $row['order_id'] . '"><i class="bi bi-eye"></i> View</button> ';
            $output .= '<button class="btn btn-dark btn-sm print-receipt-btn" data-id="' . $row['order_id'] . '"><i class="bi bi-printer"></i> Print</button>';
        }

        $output .= '</td></tr>';
    }
} else {
    $output = '<tr><td colspan="6" class="text-center">No orders found.</td></tr>';
}

echo $output;
