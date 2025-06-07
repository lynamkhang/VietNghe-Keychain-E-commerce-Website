<div class="row">
    <div class="col-12">
        <h1 class="mb-4"><?= __('checkout') ?></h1>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                    echo __($_SESSION['error']); 
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo __($_SESSION['success']); 
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4"><?= __('shipping_info') ?></h5>
                <form action="<?= $this->basePath ?>/checkout/process" method="POST" id="checkoutForm">
                    <div class="mb-3">
                        <label for="address" class="form-label"><?= __('address') ?></label>
                        <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label"><?= __('city') ?></label>
                            <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="country" class="form-label"><?= __('country') ?></label>
                            <input type="text" class="form-control" id="country" name="country" value="<?php echo htmlspecialchars($user['country'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" id="placeOrderBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <?= __('place_order') ?>
                    </button>
                </form>

                <script>
                document.getElementById('checkoutForm').addEventListener('submit', function(e) {
                    var btn = document.getElementById('placeOrderBtn');
                    var spinner = btn.querySelector('.spinner-border');
                    btn.disabled = true;
                    spinner.classList.remove('d-none');
                });
                </script>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4"><?= __('order_summary') ?></h5>
                <?php foreach ($cartItems as $item): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            <?php echo htmlspecialchars($item['name']); ?>
                            <small class="text-muted d-block"><?= __('quantity') ?>: <?php echo $item['quantity']; ?></small>
                        </div>
                        <div>
                            <?php echo formatCurrency($item['price'] * $item['quantity']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <hr>
                <div class="d-flex justify-content-between">
                    <strong><?= __('total') ?>:</strong>
                    <strong><?php echo formatCurrency($total); ?></strong>
                </div>
            </div>
        </div>
    </div>
</div> 