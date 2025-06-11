<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Order #<?php echo $order['order_id']; ?></h1>
            <div>
                <div class="btn-group me-2">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        Update Status
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <form action="<?= $this->basePath ?>/admin/orders/<?php echo $order['order_id']; ?>/status" method="POST">
                                <input type="hidden" name="status" value="pending">
                                <button type="submit" class="dropdown-item <?php echo $order['status'] === 'pending' ? 'active' : ''; ?>">
                                    <i class="bi bi-clock"></i> Pending
                                </button>
                            </form>
                        </li>
                        <li>
                            <form action="<?= $this->basePath ?>/admin/orders/<?php echo $order['order_id']; ?>/status" method="POST">
                                <input type="hidden" name="status" value="processing">
                                <button type="submit" class="dropdown-item <?php echo $order['status'] === 'processing' ? 'active' : ''; ?>">
                                    <i class="bi bi-gear"></i> Processing
                                </button>
                            </form>
                        </li>
                        <li>
                            <form action="<?= $this->basePath ?>/admin/orders/<?php echo $order['order_id']; ?>/status" method="POST">
                                <input type="hidden" name="status" value="shipped">
                                <button type="submit" class="dropdown-item <?php echo $order['status'] === 'shipped' ? 'active' : ''; ?>">
                                    <i class="bi bi-truck"></i> Shipped
                                </button>
                            </form>
                        </li>
                        <li>
                            <form action="<?= $this->basePath ?>/admin/orders/<?php echo $order['order_id']; ?>/status" method="POST">
                                <input type="hidden" name="status" value="delivered">
                                <button type="submit" class="dropdown-item <?php echo $order['status'] === 'delivered' ? 'active' : ''; ?>">
                                    <i class="bi bi-check-circle"></i> Delivered
                                </button>
                            </form>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="<?= $this->basePath ?>/admin/orders/<?php echo $order['order_id']; ?>/status" method="POST">
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="dropdown-item text-danger <?php echo $order['status'] === 'cancelled' ? 'active' : ''; ?>"
                                        onclick="return confirm('Are you sure you want to cancel this order?')">
                                    <i class="bi bi-x-circle"></i> Cancel Order
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
                <a href="<?= $this->basePath ?>/admin/orders" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Orders
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title mb-0">Order Items</h5>
                    <span class="badge bg-<?php echo $this->getStatusBadgeClass($order['status']); ?> fs-6">
                        <?php echo ucfirst($order['status']); ?>
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                 class="img-thumbnail me-3"
                                                 style="width: 60px;">
                                            <div>
                                                <?php echo htmlspecialchars($item['name']); ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo formatCurrency($item['price']); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td><?php echo formatCurrency($item['price'] * $item['quantity']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td><strong><?php echo formatCurrency($order['total_amount']); ?></strong></td>
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
                <h5 class="card-title mb-4">Order Information</h5>
                
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Order Date</h6>
                    <p class="mb-0">
                        <i class="bi bi-calendar3"></i>
                        <?php echo date('F d, Y', strtotime($order['order_date'])); ?>
                    </p>
                    <p class="mb-0">
                        <i class="bi bi-clock"></i>
                        <?php echo date('H:i', strtotime($order['order_date'])); ?>
                    </p>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted mb-2">Customer Information</h6>
                    <p class="mb-1">
                        <i class="bi bi-person"></i>
                        <?php echo htmlspecialchars($order['customer_name']); ?>
                    </p>
                    <?php if (isset($order['customer_email'])): ?>
                    <p class="mb-0">
                        <i class="bi bi-envelope"></i>
                        <a href="mailto:<?php echo htmlspecialchars($order['customer_email']); ?>">
                            <?php echo htmlspecialchars($order['customer_email']); ?>
                        </a>
                    </p>
                    <?php endif; ?>
                </div>

                <div>
                    <h6 class="text-muted mb-2">Shipping Address</h6>
                    <address class="mb-0">
                        <i class="bi bi-geo-alt"></i>
                        <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?>
                    </address>
                </div>
            </div>
        </div>
    </div>
</div> 