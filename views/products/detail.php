<div class="row">
        <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" 
               value="<?php echo htmlspecialchars($value); ?>">
    <div class="col-md-6">
        <div class="product-image-container" style="width: 100%; height: 400px; overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa; border-radius: 8px;">
            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                 class="img-fluid rounded" 
                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                 style="max-width: 100%; max-height: 100%; object-fit: contain;">
        </div>
    </div>
    <div class="col-md-6">
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <p class="lead"><?php echo htmlspecialchars($product['description']); ?></p>
        
        <div class="mb-4">
            <h3 class="text-primary"><?php echo formatCurrency($product['price']); ?></h3>
        </div>

        <div class="mb-4">
            <h4><?= __('product_details') ?></h4>
            <ul class="list-unstyled">
                <li><strong><?= __('color') ?>:</strong> <?php echo htmlspecialchars($product['color']); ?></li>
                <li><strong><?= __('material') ?>:</strong> <?php echo htmlspecialchars($product['material']); ?></li>
                <li><strong><?= __('stock') ?>:</strong> <?php echo htmlspecialchars($product['stock_quantity']); ?></li>
            </ul>
        </div>

        <?php if ($product['stock_quantity'] > 0): ?>
            <form action="/vietnghe-keychain/cart/add" method="POST" class="mb-4">
                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                <div class="mb-3">
                    <label for="quantity" class="form-label"><?= __('quantity') ?></label>
                    <input type="number" 
                           class="form-control" 
                           id="quantity" 
                           name="quantity" 
                           value="1" 
                           min="1" 
                           max="<?php echo $product['stock_quantity']; ?>" 
                           style="width: 100px;">
                </div>
                <button type="submit" class="btn btn-primary btn-lg"><?= __('add_to_cart') ?></button>
            </form>
        <?php else: ?>
            <div class="alert alert-warning">
                <?= __('out_of_stock') ?>
            </div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="/vietnghe-keychain/products" class="btn btn-outline-secondary"><?= __('back_to_products') ?></a>
        </div>
    </div>
</div>