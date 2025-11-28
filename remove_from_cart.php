<?php
include_once(__DIR__ . '/config/constants.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove'])) {
    $index = intval($_POST['remove']); 

    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // reindex
        echo "success";
    } else {
        echo "not_found";
    }
} else {
    echo "error";
}
