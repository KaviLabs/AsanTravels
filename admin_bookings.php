<?php
// ADMIN VIEW - View all bookings with day-by-day details
// Save this as: admin_bookings.php

$conn = new mysqli("localhost", "root", "", "asantravels_og");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Get all bookings
$query = "SELECT * FROM booking ORDER BY id DESC";
$result = $conn->query($query);

if (!$result) {
    die("Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Bookings Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f5f5;
            padding: 20px;
        }
        
        .table-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: 600;
        }
        
        .status-confirmed {
            background: #d4edda;
            color: #155724;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: 600;
        }
        
        .status-completed {
            background: #cce5ff;
            color: #004085;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: 600;
        }
        
        h1 {
            color: #2d5a3d;
            margin-bottom: 30px;
            font-weight: 800;
        }
        
        .booking-detail-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .booking-detail-modal.show {
            display: flex;
        }
        
        .modal-content {
            background: white;
            border-radius: 10px;
            padding: 30px;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
            width: 90%;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-clipboard-list me-2"></i> Bookings Management</h1>
        
        <div class="table-container">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Customer Name</th>
                        <th>Package</th>
                        <th>Dates</th>
                        <th>Passengers</th>
                        <th>Total Cost</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><strong>#<?php echo $row['id']; ?></strong></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo substr(htmlspecialchars($row['Package']), 0, 30); ?>...</td>
                            <td>
                                <?php echo date('M d', strtotime($row['start_date'])); ?> - 
                                <?php echo date('M d, Y', strtotime($row['end_date'])); ?>
                            </td>
                            <td>
                                <span class="badge bg-primary"><?php echo $row['passengers']; ?> people</span>
                            </td>
                            <td><strong>$<?php echo number_format($row['total'], 2); ?></strong></td>
                            <td>
                                <span class="status-<?php echo strtolower($row['status']); ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="viewBooking(<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES); ?>)">
                                    View
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="booking-detail-modal" id="detailModal">
        <div class="modal-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3>Booking Details</h3>
                <button onclick="closeModal()" style="background: none; border: none; font-size: 24px; cursor: pointer;">×</button>
            </div>
            
            <div id="modalBody"></div>
            
            <div style="margin-top: 20px; display: flex; gap: 10px;">
                <button onclick="closeModal()" class="btn btn-secondary">Close</button>
            </div>
        </div>
    </div>

    <script>
        function viewBooking(booking) {
            let html = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Booking ID:</strong> #${booking.id}<br>
                        <strong>Customer:</strong> ${booking.name}<br>
                        <strong>Email:</strong> ${booking.email}<br>
                        <strong>Status:</strong> ${booking.status}
                    </div>
                    <div class="col-md-6">
                        <strong>Check-in:</strong> ${new Date(booking.start_date).toLocaleDateString()}<br>
                        <strong>Check-out:</strong> ${new Date(booking.end_date).toLocaleDateString()}<br>
                        <strong>Passengers:</strong> ${booking.passengers}<br>
                        <strong>Total Cost:</strong> $${parseFloat(booking.total).toFixed(2)}
                    </div>
                </div>
                
                <hr>
                
                <div class="mb-3">
                    <strong>Package:</strong><br>
                    ${booking.Package}
                </div>
                
                <div class="mb-3">
                    <strong>Special Request:</strong><br>
                    ${booking.special_request || 'None'}
                </div>
                
                <div class="mb-3">
                    <strong>Cost Breakdown:</strong><br>
                    Base Price: $${parseFloat(booking.base_price).toFixed(2)}<br>
                    Extras: $${parseFloat(booking.extras).toFixed(2)}<br>
                    Total: $${parseFloat(booking.total).toFixed(2)}<br>
                    <span style="color: #4caf50; font-weight: bold;">Pay on Arrival: $${parseFloat(booking.pay_on_arrival).toFixed(2)}</span>
                </div>
            `;
            
            // Try to parse and display itinerary
            try {
                const itinerary = JSON.parse(booking.optional_tours);
                html += '<div class="mb-3"><strong>Itinerary:</strong><br>';
                html += '<table class="table table-sm"><tbody>';
                
                for (let day in itinerary) {
                    html += `<tr><td><strong>Day ${day}</strong></td><td>`;
                    if (itinerary[day].activities && itinerary[day].activities.length > 0) {
                        itinerary[day].activities.forEach(activity => {
                            html += `${activity.name} (${activity.location})<br>`;
                        });
                    } else {
                        html += 'No activities';
                    }
                    html += '</td></tr>';
                }
                
                html += '</tbody></table></div>';
            } catch(e) {
                // Optional tours not in expected format
            }
            
            document.getElementById('modalBody').innerHTML = html;
            document.getElementById('detailModal').classList.add('show');
        }
        
        function closeModal() {
            document.getElementById('detailModal').classList.remove('show');
        }
        
        // Close modal when clicking outside
        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>
