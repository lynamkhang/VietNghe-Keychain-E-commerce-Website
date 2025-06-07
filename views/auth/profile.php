<div class="container py-4" style="margin-bottom: 150px;">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h1 class="card-title h3 mb-4"><?= __('profile') ?></h1>

                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success">
                            <?php
                            switch ($_GET['success']) {
                                case 'updated':
                                    echo __('profile_updated_success');
                                    break;
                                case 'password_updated':
                                    echo __('password_updated_success');
                                    break;
                            }
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <?php
                            switch ($_GET['error']) {
                                case 'missing_fields':
                                    echo __('fill_required_fields');
                                    break;
                                case 'update_failed':
                                    echo __('update_failed');
                                    break;
                                default:
                                    echo __('general_error');
                            }
                            ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= $this->basePath ?>/profile/update" method="POST">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label"><?= __('first_name') ?></label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label"><?= __('last_name') ?></label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label"><?= __('email') ?></label>
                            <input type="email" class="form-control" id="email" 
                                   value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly>
                            <small class="text-muted"><?= __('email_cannot_change') ?></small>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label"><?= __('phone') ?></label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label"><?= __('address') ?></label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary"><?= __('update_profile') ?></button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h2 class="card-title h3 mb-4"><?= __('change_password') ?></h2>
                    <form action="<?= $this->basePath ?>/profile/password" method="POST">
                        <div class="mb-3">
                            <label for="current_password" class="form-label"><?= __('current_password') ?></label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label"><?= __('new_password') ?></label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label"><?= __('confirm_password') ?></label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-primary"><?= __('change_password') ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> 