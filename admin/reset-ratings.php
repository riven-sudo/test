<?php
// Include constants (database connection + session start)
include('../config/constants.php');

// Check if food_id is passed
if (isset($_GET['food_id'])) {
    $food_id = (int) $_GET['food_id'];

    // Count existing ratings for audit
    $cnt = 0;
    $sel = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM tbl_ratings WHERE food_id = $food_id");
    if ($sel && ($r = mysqli_fetch_assoc($sel))) $cnt = (int)$r['cnt'];

    // Delete ratings for one food
    $sql = "DELETE FROM tbl_ratings WHERE food_id = $food_id";
    $res = mysqli_query($conn, $sql);

    if ($res) {
        $_SESSION['message'] = "✅ Ratings reset for Food ID: $food_id";
        @Audit::log('reset_ratings', 'tbl_ratings', $food_id, array('count'=>$cnt), null);
    } else {
        $_SESSION['message'] = "❌ Failed to reset ratings!";
        @Audit::log('reset_ratings_failed', 'tbl_ratings', $food_id, array('count'=>$cnt), null);
    }

} else {
    // Count all ratings before purge
    $cnt = 0;
    $sel = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM tbl_ratings");
    if ($sel && ($r = mysqli_fetch_assoc($sel))) $cnt = (int)$r['cnt'];

    // Reset all ratings
    $sql = "TRUNCATE TABLE tbl_ratings";
    $res = mysqli_query($conn, $sql);

    if ($res) {
        $_SESSION['message'] = "✅ All ratings have been reset!";
        @Audit::log('reset_all_ratings', 'tbl_ratings', null, array('count'=>$cnt), null);
    } else {
        $_SESSION['message'] = "❌ Failed to reset all ratings!";
        @Audit::log('reset_all_ratings_failed', 'tbl_ratings', null, array('count'=>$cnt), null);
    }
}

// Redirect back to admin manage-food page
header("Location: manage-food.php");
exit;
?>
