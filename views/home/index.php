<!-- Hero Section -->
<div class="bg-dark text-white py-5 mb-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 hero-content">
                <h1 class="display-4"><?= __('welcome') ?></h1>
                <p class="lead"><?= __('trusted_source') ?></p>
                <a href="<?= $this->basePath ?>/products" class="btn btn-primary btn-lg"><?= __('view_all') ?></a>
            </div>
            <div class="col-md-6">
                <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
                    </div>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="<?= $this->basePath ?>/public/images/products/keychain1.JPG" class="d-block w-100 rounded" alt="Keychain 1">
                        </div>
                        <div class="carousel-item">
                            <img src="<?= $this->basePath ?>/public/images/products/keychain2.jpg" class="d-block w-100 rounded" alt="Keychain 2">
                        </div>
                        <div class="carousel-item">
                            <img src="<?= $this->basePath ?>/public/images/products/keychain3.JPG" class="d-block w-100 rounded" alt="Keychain 3">
                        </div>
                        <div class="carousel-item">
                            <img src="<?= $this->basePath ?>/public/images/products/keychain4.JPG" class="d-block w-100 rounded" alt="Keychain 4">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden"><?= __('previous') ?></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden"><?= __('next') ?></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Featured Products Section -->
<div class="container">
    <h2 class="mb-4"><?= __('latest_products') ?></h2>
    <div class="row row-cols-1 row-cols-md-4 g-4">
        <?php foreach ($latestProducts as $product): ?>
            <div class="col">
                <div class="card h-100">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                         class="card-img-top" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p class="card-text">
                            <strong><?= __('price') ?>: <?php echo formatCurrency($product['price']); ?></strong>
                        </p>
                        <a href="<?= $this->basePath ?>/products/<?php echo $product['product_id']; ?>" class="btn btn-primary"><?= __('view_details') ?></a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($latestProducts)): ?>
        <div class="alert alert-info mt-4">
            <?= __('no_results') ?>
        </div>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="<?= $this->basePath ?>/products" class="btn btn-outline-primary"><?= __('view_all') ?></a>
    </div>
</div>

<!-- Features Section -->
<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <div class="feature-item">
                <i class="fas fa-truck fa-3x mb-3"></i>
                <h3><?= __('free_shipping') ?></h3>
                <p><?= __('free_shipping_desc') ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-item">
                <i class="fas fa-undo fa-3x mb-3"></i>
                <h3><?= __('easy_returns') ?></h3>
                <p><?= __('easy_returns_desc') ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-item">
                <i class="fas fa-lock fa-3x mb-3"></i>
                <h3><?= __('secure_payment') ?></h3>
                <p><?= __('secure_payment_desc') ?></p>
            </div>
        </div>
    </div>
</div> 