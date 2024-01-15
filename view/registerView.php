<?php ob_start(); ?>

<div class="container-fluid d-flex h-100 characterBackground">
    <div class="row align-self-center w-100">
        <div class="col-4 mx-auto auth-container">
            <h3>Welcome !
            </h3>
            <p class="text-muted">We're happy to see you join the community!</p>
			<?php if (isset($error_msg)) { ?>
                <div class="alert alert-danger my-4">
                    <?= $error_msg; ?>
                </div>
            <?php } ?>
			<?php if (isset($success_msg)) { ?>
                <div class="alert alert-success my-4">
                    <?= $success_msg; ?>
                </div>
            <?php } ?>
            <form action="/index.php?action=register" method="post">
                <div class="mb-3">
                    <label for="email" class="form-label text-muted small text-uppercase">Email</label>
                    <input type="email" class="form-control" id="email" name="email"/>
                </div>

				<div class="mb-3">
                    <label for="username" class="form-label text-muted small text-uppercase">Username</label>
                    <input type="username" class="form-control" id="username" name="username"/>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label text-muted small text-uppercase">Password</label>
                    <input type="password" class="form-control" id="password" name="password"/>
                </div>

				<div class="mb-3">
                    <label for="cpassword" class="form-label text-muted small text-uppercase">Confirm Password</label>
                    <input type="password" class="form-control" id="cpassword" name="cpassword"/>
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary btn-lg btn-block w-100">Register now!</button>
                </div>
            </form>
			<a class="btn btn-warning btn-lg btn-block w-100" href="/?page=login">I already have an account</a>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require( __DIR__ . '/base.php'); ?>
