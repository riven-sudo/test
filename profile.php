<?php
include('config/constants.php');


// ✅ Check login first (before any output)
if (!isset($_SESSION['customer_id'])) {
    header('Location: customer-login.php');
    exit;
}

$customer_id = $_SESSION['customer_id'];

// ✅ Fetch user info
$sql = "SELECT * FROM tbl_customer WHERE id = $customer_id";
$res = mysqli_query($conn, $sql);
$customer = mysqli_fetch_assoc($res);

// ✅ Handle profile update
if (isset($_POST['update_profile'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);

    // Password handling: if both password fields are empty, keep existing password
    if ($password === '' && $confirm_password === '') {
        $password_to_save = $customer['password'];
    } else {
        // Validate match
        if ($password !== $confirm_password) {
            $_SESSION['update_message'] = "❌ Passwords do not match.";
            header('Location: profile.php');
            exit;
        }

        // Basic length check
        if (strlen($password) < 6) {
            $_SESSION['update_message'] = "❌ Password must be at least 6 characters.";
            header('Location: profile.php');
            exit;
        }

        // Hash the new password
        $password_to_save = password_hash($password, PASSWORD_DEFAULT);
    }

    // ✅ Handle profile picture upload
    $profile_pic = $customer['profile_pic']; // default old pic
    if (isset($_FILES['profile_pic']['name']) && $_FILES['profile_pic']['name'] != "") {
        $image_name = time() . '_' . basename($_FILES['profile_pic']['name']);
        $target_path = "images/profile/" . $image_name;

        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_path)) {
            $profile_pic = $image_name;
        }
    }

    // Escape the final password value for DB (it's already hashed when new)
    $password_db = mysqli_real_escape_string($conn, $password_to_save);

    $update_sql = "UPDATE tbl_customer SET 
        username='$username',
        email='$email',
        password='$password_db',
        address='$address',
        contact='$contact',
        profile_pic='$profile_pic'
        WHERE id=$customer_id";

    if (mysqli_query($conn, $update_sql)) {
        $_SESSION['update_message'] = "✅ Profile updated successfully!";
        header('Location: profile.php');
        exit;
    } else {
        $_SESSION['update_message'] = "❌ Failed to update profile.";
    }
}
?>

<?php include('partials-front/menu.php'); ?>

<div class="profile-container">
    <h2>My Profile</h2>

    <?php
    if (isset($_SESSION['update_message'])) {
        echo "<p class='alert'>" . $_SESSION['update_message'] . "</p>";
        unset($_SESSION['update_message']);
    }
    ?>

    <form action="" method="POST" enctype="multipart/form-data" class="profile-form">
        <div class="profile-pic-section">
            <img src="images/profile/<?php echo $customer['profile_pic'] ? $customer['profile_pic'] : 'default.png'; ?>" 
                 alt="Profile Picture" class="profile-pic">
            <input type="file" name="profile_pic" accept="image/*">
        </div>

        <label>Username</label>
        <input type="text" name="username" value="<?php echo $customer['username']; ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo $customer['email']; ?>" required>

        <label>New Password (leave blank to keep current)</label>
        <input type="password" name="password" value="" placeholder="Enter new password">

        <label>Confirm New Password</label>
        <input type="password" name="confirm_password" value="" placeholder="Repeat new password">

        <label>Address</label>
        <input type="text" name="address" value="<?php echo isset($customer['address']) ? $customer['address'] : ''; ?>">

        <label>Contact</label>
        <input type="text" name="contact" value="<?php echo isset($customer['contact']) ? $customer['contact'] : ''; ?>">

        <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
    </form>
</div>



<style>
.profile-container {
    max-width: 500px;
    margin: 40px auto;
    background: #fff;
    padding: 25px 30px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.profile-container h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

.profile-form {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.profile-form label {
    font-weight: bold;
    color: #555;
}

.profile-form input {
    padding: 8px 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 1rem;
}

.profile-pic-section {
    text-align: center;
    margin-bottom: 15px;
}

.profile-pic {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #f39c12;
    margin-bottom: 10px;
}

.btn-primary {
    background-color: #f39c12;
    color: white;
    padding: 10px;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.2s;
}

.btn-primary:hover {
    background-color: #e67e22;
}

.alert {
    text-align: center;
    padding: 10px;
    background: #d4edda;
    color: #155724;
    border-radius: 6px;
    margin-bottom: 10px;
}
</style>


<?php include('partials-front/footer.php'); ?>
