<?php
include_once(__DIR__ . '/config/constants.php');
header('Content-Type: application/json');

if (isset($_POST['index'], $_POST['action'])) {
    $index = (int) $_POST['index'];
    $action = $_POST['action'];

    if (isset($_SESSION['cart'][$index])) {
        // capture old
        $old = $_SESSION['cart'][$index];

        if ($action === "increase") {
            $_SESSION['cart'][$index]['qty'] += 1;
        } elseif ($action === "decrease") {
            $_SESSION['cart'][$index]['qty'] -= 1;
            // 🔥 If qty drops to 0 → remove
            if ($_SESSION['cart'][$index]['qty'] <= 0) {
                // Audit removal due to qty 0
                @Audit::log('update_cart_removed', 'cart', null, $old, null, array('index' => $index, 'action' => 'decrease_to_zero'));

                unset($_SESSION['cart'][$index]);
                $_SESSION['cart'] = array_values($_SESSION['cart']); // reindex
                echo json_encode([
                    "status" => "removed",
                    "index" => $index,
                    "grand_total" => array_sum(array_map(fn($i) => $i['qty'] * $i['price'], $_SESSION['cart']))
                ]);
                exit;
            }
        }

        // capture new
        $new = $_SESSION['cart'][$index];
        @Audit::log('update_cart', 'cart', null, $old, $new, array('index' => $index, 'action' => $action));

        // Compute totals
        $line_total = $_SESSION['cart'][$index]['qty'] * $_SESSION['cart'][$index]['price'];
        $grand_total = array_sum(array_map(fn($i) => $i['qty'] * $i['price'], $_SESSION['cart']));

        echo json_encode([
            "status" => "success",
            "qty" => $_SESSION['cart'][$index]['qty'],
            "line_total" => $line_total,
            "grand_total" => $grand_total
        ]);
        exit;
    }
}

echo json_encode(["status" => "error", "message" => "Invalid request"]);
