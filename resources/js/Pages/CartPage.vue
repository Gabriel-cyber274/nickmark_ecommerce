<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { onMounted, ref, computed } from 'vue';
import Skeleton from './Skeleton.vue'
import { useCart } from '../composables/useCart';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    auth: Object
});

const { cartItems, cartCount, cartTotal, updateQuantity, removeFromCart } = useCart(props.auth);
const cartProducts = ref([]);
const isLoading = ref(true);
const showRemoveModal = ref(false);
const removeProductId = ref(null);
const isRemoving = ref(false);

// Fetch cart products
const fetchCartProducts = async () => {
    isLoading.value = true;
    
    if (props.auth) {
        const response = await fetch('/api/cart', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'include'
        });
        
        if (response.ok) {
            const data = await response.json();
            cartProducts.value = data;
        }
    } else {
        if (cartItems.value.length > 0) {
            const response = await fetch('/api/cart/products', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ items: cartItems.value })
            });
            
            if (response.ok) {
                cartProducts.value = await response.json();
            }
        }
    }
    
    isLoading.value = false;
};

// Handle quantity change
const handleQuantityChange = async (productId, newQuantity) => {
    if (newQuantity < 1) return;
    
    await updateQuantity(productId, newQuantity);
    await fetchCartProducts();
};

// Handle remove from cart
const handleRemove = (productId) => {
    removeProductId.value = productId;
    showRemoveModal.value = true;
};

const confirmRemove = async () => {
    isRemoving.value = true;
    try {
        await removeFromCart(removeProductId.value);
        await fetchCartProducts();
    } finally {
        isRemoving.value = false;
        showRemoveModal.value = false;
        removeProductId.value = null;
    }
};

// Calculate subtotal
const subtotal = computed(() => {
    return cartProducts.value.reduce((total, item) => {
        const price = parseFloat(item.product?.price || item.price || 0);
        const quantity = item.quantity || 1;
        return total + (price * quantity);
    }, 0);
});

onMounted(() => {
    fetchCartProducts();
});
</script>

<template>
<Skeleton page='cart' :auth="auth">
    <main class="main">
        <div class="page-header text-center" style="background-image: url('assets/images/page-header-bg.jpg')">
            <div class="container">
                <h1 class="page-title">Shopping Cart<span>Shop</span></h1>
            </div>
        </div>

        <div class="page-content mt-3">
            <div class="cart">
                <div class="container">
                    <div v-if="isLoading" class="text-center py-5">
                        <p>Loading cart...</p>
                    </div>
                    
                    <div v-else-if="cartProducts.length === 0" class="text-center py-5">
                        <p class="mb-3">Your cart is empty</p>
                        <Link href="/categories" class="btn btn-outline-primary-2">
                            <span>Continue Shopping</span>
                            <i class="icon-long-arrow-right"></i>
                        </Link>
                    </div>

                    <div v-else class="row">
                        <div class="col-lg-9">
                            <table class="table table-cart table-mobile">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="item in cartProducts" :key="item.product_id || item.id">
                                        <td class="product-col">
                                            <div class="product">
                                                <figure class="product-media">
                                                    <Link :href="`/products/${item.product?.slug || item.slug}`">
                                                        <img 
                                                            :src="item.product?.images?.[0]?.image_url || item.images?.[0]?.image_url" 
                                                            :alt="item.product?.name || item.name"
                                                        >
                                                    </Link>
                                                </figure>

                                                <h3 class="product-title">
                                                    <Link :href="`/products/${item.product?.slug || item.slug}`">
                                                        {{ item.product?.name || item.name }}
                                                    </Link>
                                                </h3>
                                            </div>
                                        </td>
                                        <td class="price-col">
                                            ₦{{ parseFloat(item.product?.price || item.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                        </td>
                                        <td class="quantity-col">
                                            <div class="cart-product-quantity">
                                                <input 
                                                    type="number" 
                                                    class="form-control" 
                                                    :value="item.quantity" 
                                                    @change="handleQuantityChange(item.product_id || item.id, parseInt($event.target.value))"
                                                    min="1" 
                                                    max="100"
                                                >
                                            </div>
                                        </td>
                                        <td class="total-col">
                                            ₦{{ (parseFloat(item.product?.price || item.price) * item.quantity).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                        </td>
                                        <td class="remove-col">
                                            <button 
                                                class="btn-remove" 
                                                @click="handleRemove(item.product_id || item.id)"
                                            >
                                                <i class="icon-close"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="cart-bottom">
                                <Link href="/categories" class="btn btn-outline-dark-2">
                                    <span>CONTINUE SHOPPING</span>
                                    <i class="icon-refresh"></i>
                                </Link>
                            </div>
                        </div>

                        <aside class="col-lg-3">
                            <div class="summary summary-cart">
                                <h3 class="summary-title">Cart Total</h3>

                                <table class="table table-summary">
                                    <tbody>
                                        <tr class="summary-subtotal">
                                            <td>Subtotal:</td>
                                            <td>₦{{ subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</td>
                                        </tr>
                                        <tr class="summary-total">
                                            <td>Total:</td>
                                            <td>₦{{ subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <Link href="/checkout" class="btn btn-outline-primary-2 btn-order btn-block">
                                    PROCEED TO CHECKOUT
                                </Link>
                            </div>

                            <Link href="/categories" class="btn btn-outline-dark-2 btn-block mb-3">
                                <span>CONTINUE SHOPPING</span>
                                <i class="icon-refresh"></i>
                            </Link>
                        </aside>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div v-if="showRemoveModal" class="modal-overlay" @click.self="showRemoveModal = false">
        <div class="modal">
            <h3 class="modal-title">Remove Item</h3>
            <p class="modal-body">Are you sure you want to remove this item from your cart?</p>
            <div class="modal-actions">
                <button class="btn btn-outline-dark-2" @click="showRemoveModal = false" :disabled="isRemoving">Cancel</button>
                <button class="btn btn-outline-primary-2" @click="confirmRemove" :disabled="isRemoving">
                    <span v-if="isRemoving">Removing...</span>
                    <span v-else>Remove</span>
                </button>
            </div>
        </div>
    </div>
</Skeleton>
</template>

<style scoped>
.main {
    padding: 20px;
}
.product-media {
    width: 100px;
    height: 100px;
}

.product-media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1060;
    padding: 1rem;
}

.modal {
    display: block;
    background: #fff;
    border-radius: 8px;
    width: 100%;
    max-width: 420px;
    padding: 1.5rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    position: relative;
    height: max-content;
}

.modal-title {
    margin: 0 0 0.75rem;
    font-size: 1.25rem;
    font-weight: 700;
}

.modal-body {
    margin: 0 0 1.5rem;
    font-size: 1rem;
    color: #555;
}

.modal-actions {
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
    flex-wrap: wrap;
}

.modal-actions .btn {
    padding: 0.5rem 1.25rem;
    flex: 1 1 auto;
    min-width: 110px;
}

@media (max-width: 576px) {
    .modal-actions {
        flex-direction: column;
        justify-content: center;
    }

    .modal-actions .btn {
        width: 100%;
        min-width: auto;
    }
}

.modal-actions .btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>