<div class="row">
    <div class="col-12">
        <h1 class="mb-4"><?= __('shopping_cart') ?></h1>
    </div>
</div>

<?php if (empty($cartItems)): ?>
    <div class="alert alert-info">
        <?= __('empty_cart') ?> <a href="/vietnghe-keychain/products"><?= __('continue_shopping') ?></a>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th><?= __('product') ?></th>
                    <th><?= __('price') ?></th>
                    <th><?= __('quantity') ?></th>
                    <th><?= __('subtotal') ?></th>
                    <th><?= __('actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     class="img-thumbnail me-3"
                                     style="width: 80px;">
                                <div>
                                    <h5 class="mb-0"><?php echo htmlspecialchars($item['name']); ?></h5>
                                    <small class="text-muted"><?= __('available') ?>: <?php echo htmlspecialchars($item['stock_quantity']); ?></small>
                                </div>
                            </div>
                        </td>
                        <td><?php echo formatCurrency($item['price']); ?></td>
                        <td>
                            <div class="input-group" style="width: 120px;">
                                <button class="btn btn-outline-secondary btn-sm" 
                                        type="button"
                                        data-id="<?php echo $item['cart_item_id']; ?>">&minus;</button>
                                <input type="number" 
                                       class="form-control form-control-sm text-center" 
                                       value="<?php echo $item['quantity']; ?>"
                                       min="1"
                                       max="<?php echo $item['stock_quantity']; ?>"
                                       data-stock="<?php echo $item['stock_quantity']; ?>">
                                <button class="btn btn-outline-secondary btn-sm" 
                                        type="button"
                                        data-id="<?php echo $item['cart_item_id']; ?>">&plus;</button>
                            </div>
                        </td>
                        <td><?php echo formatCurrency($item['price'] * $item['quantity']); ?></td>
                        <td>
                            <button type="button" 
                                    class="btn btn-danger btn-sm"
                                    data-id="<?php echo $item['cart_item_id']; ?>">
                                <?= __('remove') ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end"><strong><?= __('total') ?>:</strong></td>
                    <td><strong><?php echo formatCurrency($total); ?></strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="/vietnghe-keychain/products" class="btn btn-outline-secondary"><?= __('continue_shopping') ?></a>
        <div>
            <form action="/vietnghe-keychain/cart/clear" method="POST" class="d-inline">
                <button type="submit" class="btn btn-danger"><?= __('clear_cart') ?></button>
            </form>
            <a href="/vietnghe-keychain/checkout" class="btn btn-primary ms-2"><?= __('proceed_checkout') ?></a>
        </div>
    </div>

    <!-- Load jQuery first -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- Then load our cart script -->
    <script src="/vietnghe-keychain/public/js/cart.js"></script>
<?php endif; ?> 