<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Add New Product</h1>
            <a href="<?= $basePath ?>/admin/products" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Products
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="<?= $basePath ?>/admin/products/create" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <div class="input-group">
                            <span class="input-group-text">₫</span>
                            <input type="number" class="form-control" id="price" name="price" step="1" min="0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="color" class="form-label">Color</label>
                        <input type="text" class="form-control" id="color" name="color" maxlength="30">
                    </div>

                    <div class="mb-3">
                        <label for="material" class="form-label">Material</label>
                        <input type="text" class="form-control" id="material" name="material" maxlength="30">
                    </div>

                    <div class="mb-3">
                        <label for="stock_quantity" class="form-label">Stock Quantity</label>
                        <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Product Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    </div>

                    <button type="submit" class="btn btn-primary">Create Product</button>
                </form>
            </div>
        </div>
    </div>
</div> 