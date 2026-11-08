<div class="form-section" style="margin-top: 3rem;">
    <h2>Admin Login</h2>
    <form method="post" action="/admin?page=login" class="admin-form">
        <div class="form-group">
            <label for="username">Username</label>
            <input id="username" name="username" type="text" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>
