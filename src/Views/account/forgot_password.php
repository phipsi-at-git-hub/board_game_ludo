<?php use App\Core\Csrf; ?>

<h1>Forgot Password</h1>
<form method="POST" action="/forgot-password">
    <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">
    <input type="email" name="email" placeholder="Your email" required><br>
    <button type="submit">Send reset link</button>
</form>
<p><a href="/login">Back to login</a></p>
