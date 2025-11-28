<?php 
include('partials/menu.php');

// Get all customers
$sql = "SELECT * FROM tbl_customer ORDER BY created_at DESC";
$res = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers</title>
    <link rel="stylesheet" href="../css/customer.css"> <!-- corrected path and extension -->
</head>
<body>

<div class="wrapper">
    <h2 class="text-center">Manage Customers</h2>

    <table class="tbl-full">
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Email</th>
         <!-- New Column -->
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($res)) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['username']; ?></td>
            <td><?php echo $row['email']; ?></td>
             <!-- Show password -->
            <td>
                <?php 
                    if ($row['status'] == 'Pending') {
                        echo "<span class='error'>Pending</span>";
                    } elseif ($row['status'] == 'Approved') {
                        echo "<span class='success'>Approved</span>";
                    } else {
                        echo "<span class='error'>Rejected</span>";
                    }
                ?>
            </td>
            <td>
                <?php if ($row['status'] == 'Pending') { ?>
                    <a href="approve/reject.php?id=<?php echo $row['id']; ?>&status=Approved" class="btn-primary">✅</a>
                    <a href="approve/reject.php?id=<?php echo $row['id']; ?>&status=Rejected" class="btn-danger">✖️</a>
                    <a href="delete-customer.php?id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this account?');">🗑️</a>
                <?php } else { ?>
                    <span class="btn-secondary"></span>
                    <a href="delete-customer.php?id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this account?');">🗑️</a>
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
</table>

</div>

</body>
</html>
