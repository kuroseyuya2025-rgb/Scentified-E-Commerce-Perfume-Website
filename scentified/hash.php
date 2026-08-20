<?php
// IMPORTANT: Use the output of this page to update the password_hash for the admin user 
// (or any user) in your 'users' table in the 'scentified_db' database.

$admin_password = "admin123";
$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

echo "<h2>Admin Password Hash Generator</h2>";
echo "<p>Use this hash to update the 'password_hash' column for the admin user in your `users` table.</p>";
echo "<p><strong>Password:</strong> $admin_password</p>";
echo "<p><strong>Generated Hash:</strong></p>";
echo "<code>" . $hashed_password . "</code>";

// To avoid displaying this on every access, you can delete this file after use.
?>