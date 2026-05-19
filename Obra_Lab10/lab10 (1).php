<?php
    // =========================================================
    // TODO 1: SECURE DATABASE CONNECTION (XAMPP / MySQL)
    // =========================================================
    // 1. Connect to MySQL using mysqli_connect($host, $user, $password, $dbname)
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $dbname = 'pizzaMaster';

    $conn = new mysqli($host, $user, $password, $dbname);//create connection

    //check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // =========================================================
    // TODO 2: HANDLE POST REQUESTS (ALL CRUD OPERATIONS)
    // =========================================================
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // You can refresh the page by using header("Location: " . $_SERVER['PHP_SELF']); exit; after each operation to see changes immediately.
        
        // ---  PIZZA ADMIN ---
        if (isset($_POST['add_pizza'])) {
            // TODO: Write INSERT query for Pizzas
            
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $price = floatval($_POST['price']);
            $stmt = mysqli_prepare($conn, "INSERT INTO pizzas (name, price) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "sd", $name, $price);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;            
        }
        if (isset($_POST['update_pizza'])) {
            // TODO: Write UPDATE query to change pizza price

        $id = intval($_POST['item_id']);
            $new_price = floatval($_POST['new_price']);
            $stmt = mysqli_prepare($conn, "UPDATE pizzas SET price = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "di", $new_price, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;            
        }
        if (isset($_POST['delete_pizza'])) {
            // TODO: Write DELETE query to remove a pizza

        $id = intval($_POST['item_id']);
            $stmt = mysqli_prepare($conn, "DELETE FROM pizzas WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;            
        }

        // ---  TOPPINGS ADMIN ---
        if (isset($_POST['add_topping'])) {
            // TODO: Write INSERT query for Toppings

        $name = mysqli_real_escape_string($conn, $_POST['name']);
            $price = floatval($_POST['price']);
            $stmt = mysqli_prepare($conn, "INSERT INTO toppings (name, price) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "sd", $name, $price);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;            
        }
        if (isset($_POST['update_topping'])) {
            // TODO: Write UPDATE query to change topping price

        $id = intval($_POST['item_id']);
            $new_price = floatval($_POST['new_price']);
            $stmt = mysqli_prepare($conn, "UPDATE toppings SET price = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "di", $new_price, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;            
        }
        if (isset($_POST['delete_topping'])) {
            // TODO: Write DELETE query to remove a topping

        $id = intval($_POST['item_id']);
            $stmt = mysqli_prepare($conn, "DELETE FROM toppings WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;            
        }

        // --- 🛒 ORDERING SYSTEM ---
        if (isset($_POST['create_order'])) {
            // TODO: 
            // 1. Fetch the selected Pizza's price from the database using mysqli_query
            // 2. Loop through selected Toppings, fetch their prices, and calculate total topping cost
            // 3. Calculate Grand Total: (Pizza Price + Toppings Total) * Quantity
            // 4. INSERT the final order into the 'orders' table

            $customer = mysqli_real_escape_string($conn, $_POST['customer']);
            $pizza_id = intval($_POST['pizza']);
            $quantity = intval($_POST['qty']);
            if (isset($_POST['toppings'])) {
                $selected_topping_ids = $_POST['toppings'];
            } else {
                $selected_topping_ids = [];
            }

            //1. Fetch the selected Pizza's price
            $pizza_result = mysqli_query($conn, "SELECT name, price FROM pizzas WHERE id = $pizza_id");
            $pizza_row = mysqli_fetch_assoc($pizza_result);
            $pizza_name = $pizza_row['name'];
            $pizza_price = $pizza_row['price'];

            //2. Loop through selected Toppings, fetch their prices, and calculate total topping cost
            $toppings_total = 0;
            $topping_names = [];
            if (!empty($selected_topping_ids)) {
                $ids = implode(',', array_map('intval', $selected_topping_ids));
                $topping_result = mysqli_query($conn, "SELECT name, price FROM toppings WHERE id IN ($ids)");
                while ($row = mysqli_fetch_assoc($topping_result)) {
                    $toppings_total += $row['price'];
                    $topping_names[] = $row['name'];
                }
            }

            //3. Calculate Grand Total
            $grand_total = ($pizza_price + $toppings_total) * $quantity;
            if (empty($topping_names)) {
                $toppings_text = '';
            } else {
                $toppings_text = implode(', ', $topping_names);
            }

            //4. INSERT the final order
            $stmt = mysqli_prepare($conn, "INSERT INTO orders (customer, pizza, toppings, qty, total, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
            mysqli_stmt_bind_param($stmt, "sssid", $customer, $pizza_name, $toppings_text, $quantity, $grand_total);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            header("Location: " . $_SERVER['PHP_SELF']);
            exit;            
        }

        // --- 📋 MANAGE ORDERS ---
        if (isset($_POST['update_status'])) {
            // TODO: Write UPDATE query to change order status to 'Completed'

            $order_id = intval($_POST['order_id']);
            mysqli_query($conn, "UPDATE orders SET status = 'Completed' WHERE id = $order_id");
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;            
        }
        if (isset($_POST['delete_order'])) {
            // TODO: Write DELETE query to remove an order
            
            $order_id = intval($_POST['order_id']);
            mysqli_query($conn, "DELETE FROM orders WHERE id = $order_id");
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>🍕 Pizza Master Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #FF6B6B 0%, #FFA500 100%); min-height: 100vh; padding: 40px 20px; color: #333;}
        .container { max-width: 1200px; margin: 0 auto; }
        header { text-align: center; color: white; margin-bottom: 40px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        h1 { font-size: 3em; margin-bottom: 10px; }
        
        .grid-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;}
        .full-width { grid-column: 1 / -1; }
        @media(max-width: 800px) { .grid-layout { grid-template-columns: 1fr; } }
        
        .card { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 10px 20px rgba(0,0,0,0.2); }
        .card h2 { color: #FF6B6B; border-bottom: 3px solid #FFA500; padding-bottom: 10px; margin-bottom: 20px; }
        
        .form-group { display: flex; gap: 10px; margin-bottom: 20px; align-items: flex-end; }
        .form-stack { display: flex; flex-direction: column; gap: 8px; margin-bottom: 15px; }
        input[type="text"], input[type="number"] { padding: 10px; border: 2px solid #FF6B6B; border-radius: 8px; width: 100%; }
        
        .radio-group, .checkbox-group { display: flex; flex-direction: column; gap: 10px; }
        .selection-item { display: flex; align-items: center; padding: 10px; border-radius: 8px; cursor: pointer; background: #fff5f5;}
        .selection-item:hover { background-color: #ffe8e8; }
        .selection-item input { margin-right: 10px; width: 18px; height: 18px; accent-color: #FF6B6B; }
        .price { color: #FFA500; font-weight: bold; }
        
        button { padding: 10px 15px; background: #FF6B6B; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; }
        button:hover { background: #FFA500; }
        .btn-large { width: 100%; padding: 15px; font-size: 1.1em; }
        .btn-update { background: #4CAF50; padding: 6px 12px; font-size: 0.9em; }
        .btn-delete { background: #f44336; padding: 6px 12px; font-size: 0.9em; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ecf0f1; }
        th { background-color: #FFF5E6; color: #FF6B6B; }
        .price-input { width: 90px !important; padding: 6px !important; margin-right: 5px; border: 1px solid #ccc !important;}
        
        .badge { padding: 5px 10px; border-radius: 20px; font-size: 0.8em; font-weight: bold; color: white; }
        .bg-pending { background-color: #FFA500; }
        .bg-completed { background-color: #4CAF50; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🍕 Pizza Master Dashboard</h1>
            <p>Admin Menu Management & Live Ordering System</p>
        </header>

        <div class="grid-layout">
            
            <div class="card">
                <h2>⚙️ Manage Pizzas</h2>
                <form method="post" class="form-group">
                    <div style="flex: 2;"><input type="text" name="name" placeholder="New Pizza Name" required></div>
                    <div style="flex: 1;"><input type="number" name="price" step="0.01" min="0" placeholder="Price" required></div>
                    <button type="submit" name="add_pizza">Add</button>
                </form>
                <table>
                    <tbody>
                        <?php
                            // TODO 3: Read from 'pizzas' table using mysqli_query and mysqli_fetch_assoc
                            // Remember to use htmlspecialchars() for security!

                            $pizzas_result = mysqli_query($conn, "SELECT id, name, price FROM pizzas ORDER BY name");
                            if (mysqli_num_rows($pizzas_result) > 0) {
                                while ($row = mysqli_fetch_assoc($pizzas_result)) {
                                    echo "<tr>";
                                    echo "<td><strong>" . htmlspecialchars($row['name']) . "</strong></td>";
                                    echo "<td style='display:flex; justify-content:flex-end; align-items:center; gap:6px;'>";
                                    echo "<form method='post' style='display:flex; align-items:center; gap:6px; margin:0;'>";
                                    echo "<input type='hidden' name='item_id' value='" . $row['id'] . "'>";
                                    echo "<input type='number' name='new_price' value='" . $row['price'] . "' step='0.01' class='price-input' required>";
                                    echo "<button type='submit' name='update_pizza' class='btn-update'>Save</button>";
                                    echo "</form>";
                                    echo "<form method='post' style='margin:0;'>";
                                    echo "<input type='hidden' name='item_id' value='" . $row['id'] . "'>";
                                    echo "<button type='submit' name='delete_pizza' class='btn-delete'>✖</button>";
                                    echo "</form>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3'>No pizzas found. Add a pizza.</td></tr>";
                            }

                            /* Example of how the generated HTML should look:
                            <tr>
                                <td><strong>Safe Pizza Name</strong></td>
                                <td>
                                    <form method='post' style='display:flex;'>
                                        <input type='hidden' name='item_id' value='1'>
                                        <input type='number' name='new_price' value='150.00' step='0.01' class='price-input' required>
                                        <button type='submit' name='update_pizza' class='btn-update'>Save</button>
                                    </form>
                                </td>
                                <td>
                                    <form method='post'>
                                        <input type='hidden' name='item_id' value='1'>
                                        <button type='submit' name='delete_pizza' class='btn-delete'>✖</button>
                                    </form>
                                </td>
                            </tr>
                            */
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h2>⚙️ Manage Toppings</h2>
                <form method="post" class="form-group">
                    <div style="flex: 2;"><input type="text" name="name" placeholder="New Topping Name" required></div>
                    <div style="flex: 1;"><input type="number" name="price" step="0.01" min="0" placeholder="Price" required></div>
                    <button type="submit" name="add_topping">Add</button>
                </form>
                <table>
                    <tbody>
                        <?php
                            // TODO 4: Read from 'toppings' table and generate rows dynamically
                            //should also contain the a form with input to update price and a delete button similar to pizzas
                            $toppings_result = mysqli_query($conn, "SELECT id, name, price FROM toppings ORDER BY name");
                            if (mysqli_num_rows($toppings_result) > 0) {
                                while ($row = mysqli_fetch_assoc($toppings_result)) {
                                    echo "<tr>";
                                    echo "<td><strong>" . htmlspecialchars($row['name']) . "</strong></td>";
                                    echo "<td style='display:flex; justify-content:flex-end; align-items:center; gap:6px;'>";
                                    echo "<form method='post' style='display:flex; align-items:center; gap:6px; margin:0;>";
                                    echo "<input type='hidden' name='item_id' value='" . $row['id'] . "'>";
                                    echo "<input type='number' name='new_price' value='" . $row['price'] . "' step='0.01' class='price-input' required>";
                                    echo "<button type='submit' name='update_topping' class='btn-update'>Save</button>";
                                    echo "</form>";
                                    echo "<form method='post' style='margin:0;'>";
                                    echo "<input type='hidden' name='item_id' value='" . $row['id'] . "'>";
                                    echo "<button type='submit' name='delete_topping' class='btn-delete'>✖</button>";
                                    echo "</form>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3'>No toppings found. Add a topping.</td></tr>";
                            }

                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card" style="max-width: 800px; margin: 0 auto 30px auto;">
            <h2>🛒 Place New Order</h2>
            <form method="post">
                <div class="form-stack">
                    <label><strong>Customer Name</strong></label>
                    <input type="text" name="customer" required>
                </div>

                <div class="grid-layout" style="gap: 20px; margin-bottom: 0;">
                    
                    <div class="form-stack">
                        <label><strong>Select Pizza</strong></label>
                        <div class="radio-group">
                            <?php 
                                // TODO 5: Fetch Pizzas from DB to generate radio buttons
                                $pizzas_radio = mysqli_query($conn, "SELECT id, name, price FROM pizzas ORDER BY name");
                                if (mysqli_num_rows($pizzas_radio) > 0) {
                                    $first = true;
                                    while ($p = mysqli_fetch_assoc($pizzas_radio)) {
                                        if ($first) {
                                            $checked = 'checked';
                                        } else {
                                            $checked = '';
                                        }
                                        $first = false;
                                        echo '<label class="selection-item" style="justify-content: space-between;">';
                                        echo '<input type="radio" name="pizza" value="' . $p['id'] . '" ' . $checked . ' required>';
                                        echo '<span style="flex:1; margin-left:10px;">' . htmlspecialchars($p['name']) . '</span>';
                                        echo '<span class="price">₱' . number_format($p['price'], 2) . '</span>';
                                        echo '</label>';
                                    }
                                } else {
                                    echo "<p>No pizzas available. Please add pizzas first.</p>";
                                }

                            ?>
                        </div>
                    </div>

                    <div class="form-stack">
                        <label><strong>Select Toppings</strong></label>
                        <div class="checkbox-group">
                            <?php 
                                // TODO 6: Fetch Toppings from DB to generate checkboxes
                                $toppings_check = mysqli_query($conn, "SELECT id, name, price FROM toppings ORDER BY name");
                                if (mysqli_num_rows($toppings_check) > 0) {
                                    while ($t = mysqli_fetch_assoc($toppings_check)) {
                                        echo '<label class="selection-item" style="justify-content: space-between;">';
                                        echo '<input type="checkbox" name="toppings[]" value="' . $t['id'] . '">';
                                        echo '<span style="flex:1; margin-left:10px;">' . htmlspecialchars($t['name']) . '</span>';
                                        echo '<span class="price">+₱' . number_format($t['price'], 2) . '</span>';
                                        echo '</label>';
                                    }
                                } else {
                                    echo "<p>No toppings available.</p>";
                                }

                            ?>
                        </div>
                    </div>
                </div>

                <div class="form-stack" style="margin-top: 15px;">
                    <label><strong>Quantity</strong></label>
                    <input type="number" name="qty" min="1" value="1" required>
                </div>

                <button type="submit" name="create_order" class="btn-large">🚀 Submit Order</button>
            </form>
        </div>

        <div class="card full-width">
            <h2>📋 Live Kitchen Orders</h2>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th><th>Customer</th><th>Order Details</th><th>Total</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            // TODO 7: Read from 'orders' table and display live kitchen orders
                            // If status is Pending, show the Checkmark (✔) button. Otherwise, hide it.
                            $orders_result = mysqli_query($conn, "SELECT id, customer, pizza, toppings, qty, total, status FROM orders ORDER BY id DESC");
                            if (mysqli_num_rows($orders_result) > 0) {
                                while ($order = mysqli_fetch_assoc($orders_result)) {
                                    $details = $order['qty'] . " " . htmlspecialchars($order['pizza']);
                                    if (!empty($order['toppings'])) {
                                        $details .= " w/ " . htmlspecialchars($order['toppings']);
                                    }
                                    $status_class = ($order['status'] == 'Pending') ? 'bg-pending' : 'bg-completed';
                                    

                                    echo "<tr>";
                                    echo "<td>" . $order['id'] . "</td>";
                                    echo "<td>" . htmlspecialchars($order['customer']) . "</td>";
                                    echo "<td>" . $details . "</td>";
                                    echo "<td>₱" . number_format($order['total'], 2) . "</td>";
                                    echo "<td><span class='badge $status_class'>" . $order['status'] . "</span></td>";
                                    echo "<td style='display:flex; gap:5px;'>";
                                    
                                    //complete button only for Pending orders
                                    if ($order['status'] == 'Pending') {
                                        echo "<form method='post'>";
                                        echo "<input type='hidden' name='order_id' value='" . $order['id'] . "'>";
                                        echo "<button type='submit' name='update_status' class='btn-update'>✔ Complete</button>";
                                        echo "</form>";
                                    }    

                                    echo "<form method='post' onsubmit=\"return confirm('Delete this order?');\">";
                                    echo "<input type='hidden' name='order_id' value='" . $order['id'] . "'>";
                                    echo "<button type='submit' name='delete_order' class='btn-delete'>✖ Delete</button>";
                                    echo "</form>";
                                    
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' style='text-align:center;'>No orders yet.</td></tr>";
                            }

                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
</body>
</html>