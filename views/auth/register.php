<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><?= __('register') ?></h4>
            </div>
            <div class="card-body">
                <?php if (isset($errors) && !empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= __($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <?php
                        switch ($_GET['error']) {
                            case 'password_mismatch':
                                echo __('passwords_do_not_match');
                                break;
                            case 'missing_fields':
                                echo __('fill_required_fields');
                                break;
                            case 'email_exists':
                                echo __('email_exists');
                                break;
                            case 'username_exists':
                                echo __('username_exists');
                                break;
                            default:
                                echo __('general_error');
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <form action="/vietnghe-keychain/register" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label"><?= __('username') ?></label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label"><?= __('email') ?></label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="first_name" class="form-label"><?= __('first_name') ?></label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo isset($first_name) ? htmlspecialchars($first_name) : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label"><?= __('last_name') ?></label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo isset($last_name) ? htmlspecialchars($last_name) : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label"><?= __('phone_number') ?></label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label"><?= __('address') ?></label>
                        <textarea class="form-control" id="address" name="address" rows="2"><?php echo isset($address) ? htmlspecialchars($address) : ''; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label"><?= __('new_password') ?></label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="form-text"><?= __('password_requirement') ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label"><?= __('confirm_password') ?></label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary"><?= __('register') ?></button>
                    </div>
                </form>

                <div class="mt-3 text-center">
                    <p class="mb-0"><?= __('already_account') ?> <a href="/vietnghe-keychain/login"><?= __('login') ?></a></p>
                </div>
            </div>
        </div>
    </div>
</div> 