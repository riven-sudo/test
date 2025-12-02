<?php 
include('partials/menu.php');
include('partials/admin-check.php');

// Get all customers
$sql = "SELECT * FROM tbl_customer ORDER BY created_at DESC";
$res = mysqli_query($conn, $sql);
?>

        <!--Main Content Section Starts-->
        <div class="main-content">
            <div class="wrapper">
                <h1>Manage Customers</h1>

                <br />

                <div class="table-responsive">
                <table class="tbl-full">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>

                <?php while ($row = mysqli_fetch_assoc($res)) { ?>
                    <tr>
                        <td data-label="ID"><?php echo $row['id']; ?></td>
                        <td data-label="Username"><?php echo $row['username']; ?></td>
                        <td data-label="Email"><?php echo $row['email']; ?></td>
                        <td data-label="Status">
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
                        <td data-label="Action">
                            <?php if ($row['status'] == 'Pending') { ?>
                                <a title="Approve" href="approve/reject.php?id=<?php echo $row['id']; ?>&status=Approved" class="action-btn btn-approve"><i class="fa-solid fa-check"></i></a>
                                <a title="Reject" href="approve/reject.php?id=<?php echo $row['id']; ?>&status=Rejected" class="action-btn btn-danger"><i class="fa-solid fa-xmark"></i></a>
                                <a title="Delete" href="delete-customer.php?id=<?php echo $row['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Are you sure you want to delete this account?');"><i class="fa-solid fa-trash"></i></a>
                            <?php } else { ?>
                                <a title="Delete" href="delete-customer.php?id=<?php echo $row['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Are you sure you want to delete this account?');"><i class="fa-solid fa-trash"></i></a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                </table>
                </div>

            </div>
        </div>
        <!--Main Content Section Ends-->

<?php include('partials/footer.php') ?>
