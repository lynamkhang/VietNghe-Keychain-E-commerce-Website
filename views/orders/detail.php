<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><?= __('order_number') ?> #<?php echo $order['order_id']; ?></h1>
            <a href="<?= $basePath ?>/orders" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> <?= __('back_to_orders') ?>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4"><?= __('order_items') ?></h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th><?= __('product') ?></th>
                                <th><?= __('price') ?></th>
                                <th><?= __('quantity') ?></th>
                                <th><?= __('subtotal') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($item['image'])): ?>
                                                <img src="<?= $basePath ?>/uploads/products/<?= htmlspecialchars($item['image']) ?>" 
                                                     alt="<?= htmlspecialchars($item['name']) ?>"
                                                     class="img-thumbnail me-3"
                                                     style="width: 60px;">
                                            <?php endif; ?>
                                            <div>
                                                <?= htmlspecialchars($item['name']) ?>
                                                <?php if ($item['deleted']): ?>
                                                    <span class="badge bg-secondary">Product no longer available</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= formatCurrency($item['price']) ?></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td><?= formatCurrency($item['price'] * $item['quantity']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong><?= __('total') ?>:</strong></td>
                                <td><strong><?= formatCurrency($order['total_amount']) ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4"><?= __('order_information') ?></h5>
                <p><strong><?= __('order_date') ?>:</strong><br>
                <?= date('F d, Y', strtotime($order['order_date'])) ?></p>
                
                <p><strong><?= __('order_status') ?>:</strong><br>
                <span class="badge bg-<?= $this->getStatusBadgeClass($order['status']) ?>">
                    <?= __('status_' . $order['status']) ?>
                </span></p>

                <h5 class="card-title mb-3 mt-4"><?= __('shipping_address') ?></h5>
                <address>
                    <?= nl2br(htmlspecialchars($order['shipping_address'])) ?>
                    <?php if (isset($order['shipping_city']) && isset($order['shipping_country'])): ?>
                        <br><?= htmlspecialchars($order['shipping_city']) ?>, 
                        <?= htmlspecialchars($order['shipping_country']) ?>
                    <?php endif; ?>
                </address>
            </div>
        </div>
    </div>
</div> 