<div class="row">
    <div class="col-12">
        <h1 class="mb-4"><?= __('products') ?></h1>
        <?php if (isset($keyword) && !empty($keyword)): ?>
            <p><?= __('search_results_for') ?>: "<?php echo htmlspecialchars($keyword); ?>"</p>
        <?php endif; ?>
    </div>
</div>

<div class="row row-cols-1 row-cols-md-4 g-4">
    <?php foreach ($products as $product): ?>
        <div class="col">
            <div class="card h-100">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                     class="card-img-top" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                    <p class="card-text">
                        <strong><?= __('price') ?>: <?php echo formatCurrency($product['price']); ?></strong>
                    </p>
                    <div class="d-grid gap-2">
                        <a href="/vietnghe-keychain/products/<?php echo $product['product_id']; ?>" class="btn btn-primary"><?= __('view_details') ?></a>
                        <form action="/vietnghe-keychain/cart/add" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                            <button type="submit" class="btn btn-success w-100"><?= __('add_to_cart') ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if (empty($products)): ?>
    <div class="alert alert-info mt-4">
        <?= __('no_products_found') ?>
    </div>
<?php endif; ?>