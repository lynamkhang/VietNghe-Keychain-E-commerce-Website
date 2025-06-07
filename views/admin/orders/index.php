<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Orders</h1>
            <div class="btn-group">
                <a href="<?= $this->basePath ?>/admin/orders" class="btn <?php echo !isset($_GET['status']) ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    <i class="bi bi-list"></i> All Orders
                </a>
                <a href="<?= $this->basePath ?>/admin/orders?status=pending" class="btn <?php echo isset($_GET['status']) && $_GET['status'] === 'pending' ? 'btn-warning' : 'btn-outline-warning'; ?>">
                    <i class="bi bi-clock"></i> Pending (<?php echo $totalPending; ?>)
                </a>
                <a href="<?= $this->basePath ?>/admin/orders?status=processing" class="btn <?php echo isset($_GET['status']) && $_GET['status'] === 'processing' ? 'btn-info' : 'btn-outline-info'; ?>">
                    <i class="bi bi-gear"></i> Processing (<?php echo $totalProcessing; ?>)
                </a>
                <a href="<?= $this->basePath ?>/admin/orders?status=shipped" class="btn <?php echo isset($_GET['status']) && $_GET['status'] === 'shipped' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    <i class="bi bi-truck"></i> Shipped (<?php echo $totalShipped; ?>)
                </a>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-2">
                <div class="card border-warning h-100">
                    <div class="card-body p-2 text-center">
                        <h6 class="card-title text-warning mb-1">Pending</h6>
                        <h3 class="mb-0"><?php echo $totalPending; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-info h-100">
                    <div class="card-body p-2 text-center">
                        <h6 class="card-title text-info mb-1">Processing</h6>
                        <h3 class="mb-0"><?php echo $totalProcessing; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-primary h-100">
                    <div class="card-body p-2 text-center">
                        <h6 class="card-title text-primary mb-1">Shipped</h6>
                        <h3 class="mb-0"><?php echo $totalShipped; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-success h-100">
                    <div class="card-body p-2 text-center">
                        <h6 class="card-title text-success mb-1">Delivered</h6>
                        <h3 class="mb-0"><?php echo $totalDelivered; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-danger h-100">
                    <div class="card-body p-2 text-center">
                        <h6 class="card-title text-danger mb-1">Cancelled</h6>
                        <h3 class="mb-0"><?php echo $totalCancelled; ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php
                switch ($_GET['success']) {
                    case 'updated':
                        echo 'Order status updated successfully.';
                        break;
                    case 'deleted':
                        echo 'Order deleted successfully.';
                        break;
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php
                switch ($_GET['error']) {
                    case 'update_failed':
                        echo 'Failed to update order status.';
                        break;
                    case 'delete_failed':
                        echo 'Failed to delete order.';
                        break;
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive" style="height: calc(100vh - 800px); min-height: 200px; max-height: 800px; overflow-y: auto;">
                    <table class="table table-striped table-hover mb-0">
                        <thead style="position: sticky; top: 0; background: white; z-index: 1; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No orders found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['order_id']; ?></td>
                                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                        <td><?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></td>
                                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $this->getStatusBadgeClass($order['status']); ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?= $this->basePath ?>/admin/orders/<?php echo $order['order_id']; ?>" 
                                                   class="btn btn-sm btn-primary">View Details</a>
                                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <span class="visually-hidden">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <form action="<?= $this->basePath ?>/admin/orders/<?php echo $order['order_id']; ?>/status" method="POST" style="display: inline;">
                                                            <input type="hidden" name="status" value="processing">
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="bi bi-gear"></i> Mark as Processing
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="<?= $this->basePath ?>/admin/orders/<?php echo $order['order_id']; ?>/status" method="POST" style="display: inline;">
                                                            <input type="hidden" name="status" value="shipped">
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="bi bi-truck"></i> Mark as Shipped
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="<?= $this->basePath ?>/admin/orders/<?php echo $order['order_id']; ?>/status" method="POST" style="display: inline;">
                                                            <input type="hidden" name="status" value="delivered">
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="bi bi-check-circle"></i> Mark as Delivered
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="<?= $this->basePath ?>/admin/orders/<?php echo $order['order_id']; ?>/status" method="POST" style="display: inline;">
                                                            <input type="hidden" name="status" value="cancelled">
                                                            <button type="submit" class="dropdown-item text-danger" 
                                                                    onclick="return confirm('Are you sure you want to cancel this order?')">
                                                                <i class="bi bi-x-circle"></i> Cancel Order
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> 