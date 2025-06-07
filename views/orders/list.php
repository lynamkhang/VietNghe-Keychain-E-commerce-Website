<div class="row">
    <div class="col-12">
        <h1 class="mb-4"><?= __('my_orders') ?></h1>
    </div>
</div>

<?php if (empty($orders)): ?>
    <div class="alert alert-info">
        <?= __('no_orders_yet') ?> <a href="<?= $basePath ?>/products"><?= __('start_shopping') ?></a>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th><?= __('order_id') ?></th>
                    <th><?= __('order_date') ?></th>
                    <th><?= __('total') ?></th>
                    <th><?= __('order_status') ?></th>
                    <th><?= __('actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?php echo $order['order_id']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                        <td><?php echo formatCurrency($order['total_amount']); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $this->getStatusBadgeClass($order['status']); ?>">
                                <?php echo __('status_' . $order['status']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= $basePath ?>/orders/show/<?php echo $order['order_id']; ?>" class="btn btn-sm btn-primary"><?= __('view_details') ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?> 