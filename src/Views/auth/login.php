<h1>Login</h1>
<?php if (!empty($error)) echo "<p style='color:red'>$error</p>"; ?>
<form method="POST" action="/login">
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Login</button>
</form>
<p>No account? <a href="/register">Register</a></p>
