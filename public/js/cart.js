function updateQuantity(element, change) {
    const $element = $(element);
    const cartItemId = $element.prop('tagName') === 'INPUT' ? 
        $element.closest('.input-group').find('button').first().data('id') : 
        $element.data('id');
    
    const $input = $element.prop('tagName') === 'INPUT' ? 
        $element : 
        $element.closest('.input-group').find('input');
    
    let newQuantity;
    const stockQuantity = parseInt($input.data('stock'));
    
    if (typeof change === 'number') {
        newQuantity = parseInt($input.val()) + change;
    } else {
        newQuantity = parseInt(change);
    }
    
    if (newQuantity < 1) {
        alert('Quantity cannot be less than 1');
        return;
    }

    if (newQuantity > stockQuantity) {
        alert('Cannot exceed available stock quantity (' + stockQuantity + ')');
        $input.val(Math.min($input.val(), stockQuantity));
        return;
    }

    console.log('Updating quantity:', { cartItemId, newQuantity, stockQuantity });
    
    $.ajax({
        url: '/vietnghe-keychain/cart/update',
        method: 'POST',
        data: {
            cart_item_id: cartItemId,
            quantity: newQuantity
        },
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        dataType: 'json'
    })
    .done(function(data) {
        console.log('Update response:', data);
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Failed to update quantity. Please try again.');
            // Reset the input to its previous value
            $input.val($input.prop('defaultValue'));
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('Error:', jqXHR.responseJSON || jqXHR.responseText);
        if (jqXHR.status === 401) {
            alert('Please log in to continue.');
            location.href = '/vietnghe-keychain/login';
        } else {
            alert('An error occurred while updating the quantity.');
            // Reset the input to its previous value
            $input.val($input.prop('defaultValue'));
        }
    });
}

function removeItem(cartItemId) {
    if (!confirm('Are you sure you want to remove this item?')) return;
    
    console.log('Removing item:', cartItemId);

    $.ajax({
        url: '/vietnghe-keychain/cart/remove',
        method: 'POST',
        data: {
            cart_item_id: cartItemId
        },
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        dataType: 'json'
    })
    .done(function(data) {
        console.log('Remove response:', data);
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Failed to remove item. Please try again.');
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('Error:', jqXHR.responseJSON || jqXHR.responseText);
        if (jqXHR.status === 401) {
            alert('Please log in to continue.');
            location.href = '/vietnghe-keychain/login';
        } else {
            alert('An error occurred while removing the item.');
        }
    });
}

// Document ready handler
$(function() {
    console.log('Cart JS initialized');

    // Bind quantity input change event
    $('.input-group input[type="number"]').on('change', function() {
        console.log('Input changed');
        const $input = $(this);
        const value = parseInt($input.val());
        const max = parseInt($input.attr('max'));
        
        if (value > max) {
            alert('Cannot exceed available stock quantity (' + max + ')');
            $input.val(max);
            return;
        }
        
        updateQuantity(this, value);
    });

    // Bind plus/minus button click events
    $('.input-group button').on('click', function() {
        console.log('Button clicked:', $(this).text().trim());
        const change = $(this).text().trim() === '+' ? 1 : -1;
        updateQuantity(this, change);
    });

    // Bind remove button click events
    $('.btn-danger').not('form .btn-danger').on('click', function() {
        console.log('Remove button clicked');
        const cartItemId = $(this).data('id');
        removeItem(cartItemId);
    });
});

