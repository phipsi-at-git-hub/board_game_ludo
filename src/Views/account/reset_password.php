<?php use App\Core\Csrf; ?>

<h1>Reset Password</h1>
<form method="POST" action="">
    <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">
    <input type="password" name="new_password" placeholder="New password" required><br>
    <input type="password" name="confirm_password" placeholder="Confirm password" required><br>
    <button type="submit">Reset Password</button>
</form>
