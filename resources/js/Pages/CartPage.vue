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

// Fetch cart products
const fetchCartProducts = async () => {
    isLoading.value = true;
    
    if (props.auth) {
        // For authenticated users, products are already loaded with cart items
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
        // For guest users, fetch product details
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
const handleRemove = async (productId) => {
    if (confirm('Remove this item from your cart?')) {
        await removeFromCart(productId);
        await fetchCartProducts();
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
                                                    <Link :href="`/product/${item.product_id || item.id}`">
                                                        <img 
                                                            :src="item.product?.images?.[0]?.image_url || item.images?.[0]?.image_url" 
                                                            :alt="item.product?.name || item.name"
                                                        >
                                                    </Link>
                                                </figure>

                                                <h3 class="product-title">
                                                    <Link :href="`/product/${item.product_id || item.id}`">
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
                                <div class="cart-discount">
                                    <form action="#">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="coupon code">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-primary-2" type="submit">
                                                    <i class="icon-long-arrow-right"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

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
                                        <tr class="summary-shipping">
                                            <td>Shipping:</td>
                                            <td>&nbsp;</td>
                                        </tr>

                                        <tr class="summary-shipping-row">
                                            <td>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="free-shipping" name="shipping" class="custom-control-input" checked>
                                                    <label class="custom-control-label" for="free-shipping">Free Shipping</label>
                                                </div>
                                            </td>
                                            <td>₦0.00</td>
                                        </tr>

                                        <tr class="summary-shipping-row">
                                            <td>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="standard-shipping" name="shipping" class="custom-control-input">
                                                    <label class="custom-control-label" for="standard-shipping">Standard:</label>
                                                </div>
                                            </td>
                                            <td>₦1,000.00</td>
                                        </tr>

                                        <tr class="summary-shipping-row">
                                            <td>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="express-shipping" name="shipping" class="custom-control-input">
                                                    <label class="custom-control-label" for="express-shipping">Express:</label>
                                                </div>
                                            </td>
                                            <td>₦2,000.00</td>
                                        </tr>

                                        <tr class="summary-shipping-estimate" v-if="auth">
                                            <td>
                                                Estimate for Your State<br> 
                                                <Link href="/dashboard">Change address</Link>
                                            </td>
                                            <td>&nbsp;</td>
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
</Skeleton>
</template>

<style scoped>
.product-media {
    width: 100px;
    height: 100px;
}

.product-media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
</style>