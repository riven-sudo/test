<?php
// Include constants (database connection + session start)
include('../config/constants.php');

// Check if food_id is passed
if (isset($_GET['food_id'])) {
    $food_id = (int) $_GET['food_id'];

    // Delete ratings for one food
    $sql = "DELETE FROM tbl_ratings WHERE food_id = $food_id";
    $res = mysqli_query($conn, $sql);

    if ($res) {
        $_SESSION['message'] = "✅ Ratings reset for Food ID: $food_id";
    } else {
        $_SESSION['message'] = "❌ Failed to reset ratings!";
    }

} else {
    // Reset all ratings
    $sql = "TRUNCATE TABLE tbl_ratings";
    $res = mysqli_query($conn, $sql);

    if ($res) {
        $_SESSION['message'] = "✅ All ratings have been reset!";
    } else {
        $_SESSION['message'] = "❌ Failed to reset all ratings!";
    }
}

// Redirect back to admin manage-food page
header("Location: manage-food.php");
exit;
?>
