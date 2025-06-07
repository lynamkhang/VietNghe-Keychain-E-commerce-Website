<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><?= __('login') ?></h4>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?php 
                        echo __($_SESSION['success']);
                        unset($_SESSION['success']);
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo __($error); ?></div>
                <?php endif; ?>

                <form action="/vietnghe-keychain/login" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label"><?= __('email') ?></label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label"><?= __('current_password') ?></label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary"><?= __('login') ?></button>
                    </div>
                </form>

                <div class="mt-3 text-center">
                    <p class="mb-0"><?= __('dont_account') ?> <a href="/vietnghe-keychain/register"><?= __('register') ?></a></p>
                </div>
            </div>
        </div>
    </div>
</div> 