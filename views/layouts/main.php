<!DOCTYPE html>
<html lang="<?= getCurrentLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Việt Nghệ Keychain</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $this->basePath ?>/public/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <div class="nav-left">
                <a class="navbar-brand" href="<?= $this->basePath ?>/">
                    <img src="<?= $this->basePath ?>/public/images/logo/logo-transparent.png" alt="Việt Nghệ Keychain" height="180">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <div class="nav-left">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="<?= $this->basePath ?>/products"><?= __('products') ?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= $this->basePath ?>/orders"><?= __('orders') ?></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="search-container">
                <form class="d-flex" action="<?= $this->basePath ?>/products/search" method="GET">
                    <input class="form-control me-2" type="search" name="keyword" placeholder="<?= __('search') ?>">
                    <button class="btn btn-outline-light" type="submit"><?= __('search_button') ?></button>
                </form>
            </div>

            <div class="nav-right">
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle text-light" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://flagcdn.com/w20/<?= getLanguageOptions()[getCurrentLanguage()]['flag'] ?>.png" 
                             srcset="https://flagcdn.com/w40/<?= getLanguageOptions()[getCurrentLanguage()]['flag'] ?>.png 2x"
                             width="20" 
                             height="15"
                             alt="<?= getLanguageOptions()[getCurrentLanguage()]['name'] ?>">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                        <?php foreach (getLanguageOptions() as $code => $lang): ?>
                            <li>
                                <form action="<?= $this->basePath ?>/language/switch" method="POST" class="dropdown-item-form">
                                    <input type="hidden" name="language" value="<?= $code ?>">
                                    <button type="submit" class="dropdown-item <?= getCurrentLanguage() === $code ? 'active' : '' ?>">
                                        <img src="https://flagcdn.com/w20/<?= $lang['flag'] ?>.png" 
                                             srcset="https://flagcdn.com/w40/<?= $lang['flag'] ?>.png 2x"
                                             width="20" 
                                             height="15"
                                             alt="<?= $lang['name'] ?>"
                                             class="me-2">
                                        <?= $lang['name'] ?>
                                    </button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <?php if (isset($_SESSION['user'])): ?>
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle text-light" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo htmlspecialchars($_SESSION['user']['first_name'] . ' ' . $_SESSION['user']['last_name']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin'): ?>
                                <li><a class="dropdown-item" href="<?= $this->basePath ?>/admin">Admin Dashboard</a></li>
                                <li><a class="dropdown-item" href="<?= $this->basePath ?>/admin/users">Manage Users</a></li>
                                <li><a class="dropdown-item" href="<?= $this->basePath ?>/admin/products">Manage Products</a></li>
                                <li><a class="dropdown-item" href="<?= $this->basePath ?>/admin/orders">Manage Orders</a></li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="<?= $this->basePath ?>/orders"><?= __('my_orders') ?></a></li>
                            <li><a class="dropdown-item" href="<?= $this->basePath ?>/profile"><?= __('profile') ?></a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= $this->basePath ?>/logout"><?= __('logout') ?></a></li>
                        </ul>
                    </div>
                    <?php
                        $cartCount = 0;
                        if (isset($_SESSION['user'])) {
                            $cartModel = new Cart();
                            $cartCount = $cartModel->getCartItemCount($_SESSION['user']['user_id']);
                        }
                    ?>
                    <a class="nav-link position-relative text-light" href="<?= $this->basePath ?>/cart">
                        <i class="fas fa-shopping-cart"></i>
                        <?php if ($cartCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?= $cartCount ?>
                                <span class="visually-hidden"><?= __('cart_items') ?></span>
                            </span>
                        <?php endif; ?>
                    </a>
                <?php else: ?>
                    <a class="nav-link text-light" href="<?= $this->basePath ?>/login"><?= __('login') ?></a>
                    <a class="btn btn-outline-light" href="<?= $this->basePath ?>/register"><?= __('register') ?></a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="container my-4">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                echo __($_SESSION['success']);
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
        <?php echo $content; ?>
    </main>

    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Việt Nghệ Keychain</h5>
                    <p><?= __('trusted_source') ?></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <h5><?= __('contact_us') ?></h5>
                    <p><?= __('email') ?>: info@vietnghe.com<br><?= __('phone') ?>: (123) 456-7890</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 