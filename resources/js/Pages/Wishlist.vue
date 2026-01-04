<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { onMounted, ref, computed } from 'vue';
import Skeleton from './Skeleton.vue';
import { useWishlist } from '../composables/useWishlist';
import { useCart } from '../composables/useCart';

import { usePage } from '@inertiajs/vue3';
import { watch } from 'vue';



const page = usePage();



const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    auth: Object,
    wishlist: {
        type: Array,
        default: () => []
    }
});


const toast = ref({
    show: false,
    message: '',
    type: 'success'
});

const showToast = (message, type = 'success') => {
    toast.value.message = message;
    toast.value.type = type;
    toast.value.show = true;
    
    setTimeout(() => {
        toast.value.show = false;
    }, 5000);
};

const closeToast = () => {
    toast.value.show = false;
};

// Watch for flash messages
watch(() => page.props.flash, (flash) => {
    if (flash?.success) {
        showToast(flash.success, 'success');
    }
    if (flash?.error) {
        showToast(flash.error, 'error');
    }
}, { deep: true, immediate: true });


const { wishlistItems, toggleWishlist, isInWishlist } = useWishlist(props.auth);
const guestWishlistProducts = ref([]);
const isLoading = ref(false);

const { addToCart, isInCart } = useCart(props.auth);

// Add this function
const handleAddToCart = async (productId) => {
    try {
        const result = await addToCart(productId, 1);
        console.log(result.message);
        // Optional: show a toast notification
        showToast('Product added to cart!', 'success');
    } catch (error) {
        console.error('Error adding to cart:', error);
        showToast('Failed to add product to cart. Please try again.', 'error');
    }
};

// Fetch product details for guest wishlist
const fetchGuestWishlistProducts = async () => {
    if (!props.auth && wishlistItems.value.length > 0) {
        isLoading.value = true;
        try {
            const response = await fetch('/api/wishlist/products', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ product_ids: wishlistItems.value })
            });

            if (response.ok) {
                guestWishlistProducts.value = await response.json();
            }
        } catch (error) {
            console.error('Error fetching guest wishlist products:', error);
        } finally {
            isLoading.value = false;
        }
    }
};

// Combined wishlist items (for both authenticated and guest users)
const displayWishlist = computed(() => {
    if (props.auth) {
        return props.wishlist;
    }
    return guestWishlistProducts.value;
});

const removeFromWishlist = async (productId) => {
    try {
        await toggleWishlist(productId);
        
        if (props.auth) {
            router.reload({ only: ['wishlist'] });
        } else {
            guestWishlistProducts.value = guestWishlistProducts.value.filter(
                item => item.id !== productId
            );
        }
    } catch (error) {
        console.error('Error removing from wishlist:', error);
    }
};

onMounted(() => {
    if (!props.auth) {
        fetchGuestWishlistProducts();
    }
});
</script>

<template>
      <!-- Toast Notification -->
    <transition name="toast">
        <div v-if="toast.show" :class="['toast-notification', `toast-${toast.type}`]">
            <div class="toast-content">
                <div class="toast-icon">
                    <i v-if="toast.type === 'success'" class="icon-check"></i>
                    <i v-if="toast.type === 'error'" class="icon-close"></i>
                    <i v-if="toast.type === 'warning'" class="icon-exclamation"></i>
                    <i v-if="toast.type === 'info'" class="icon-info"></i>
                </div>
                <div class="toast-message">{{ toast.message }}</div>
                <button class="toast-close" @click="closeToast">
                    <i class="icon-close"></i>
                </button>
            </div>
        </div>
    </transition>
    <Skeleton page='wishlist' :auth="auth">
        <main class="main">
            <div class="page-header text-center" style="background-image: url('assets/images/page-header-bg.jpg')">
                <div class="container">
                    <h1 class="page-title">Wishlist<span>Shop</span></h1>
                </div>
            </div>

            <div class="page-content">
                <div class="container">
                    <div v-if="isLoading" class="text-center py-5">
                        <p>Loading wishlist...</p>
                    </div>

                    <div v-else-if="displayWishlist.length === 0" class="text-center py-5">
                        <h3>Your wishlist is empty</h3>
                        <p class="mb-4">Start adding products to your wishlist!</p>
                        <Link href="/categories" class="btn btn-primary">
                            <span>Continue Shopping</span>
                            <i class="icon-long-arrow-right"></i>
                        </Link>
                    </div>

                    <div v-else>
                        <table class="table table-wishlist table-mobile">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Stock Status</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="item in displayWishlist" :key="item.id || item.product_id">
                                    <td class="product-col">
                                        <div class="product">
                                            <figure class="product-media">
                                                <Link :href="'/product/' + (item.product?.id || item.id)">
                                                    <img 
                                                        :src="item.product?.images?.[0]?.image_url || item.images?.[0]?.image_url" 
                                                        :alt="item.product?.name || item.name">
                                                </Link>
                                            </figure>

                                            <h3 class="product-title">
                                                <Link :href="'/product/' + (item.product?.id || item.id)">
                                                    {{ item.product?.name || item.name }}
                                                </Link>
                                            </h3>
                                        </div>
                                    </td>
                                    <td class="price-col">
                                        <div class="d-flex flex-column" v-if="item.product?.previous_price || item.previous_price">
                                            <span class="new-price">
                                                ₦{{ parseFloat(item.product?.price || item.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                            </span>
                                            <span class="old-price">
                                                ₦{{ parseFloat(item.product?.previous_price || item.previous_price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                            </span>
                                        </div>
                                        <div v-else>
                                            ₦{{ parseFloat(item.product?.price || item.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                        </div>
                                    </td>
                                    <td class="stock-col">
                                        <span class="in-stock">In stock</span>
                                    </td>
                                    <td class="action-col">
                                        <button @click.prevent="handleAddToCart(item.product?.id || item.id)" class="btn btn-block btn-outline-primary-2">
                                            <i class="icon-cart-plus"></i>Add to Cart
                                        </button>
                                    </td>
                                    <td class="remove-col">
                                        <button 
                                            class="btn-remove" 
                                            @click="removeFromWishlist(item.product?.id || item.id)"
                                            title="Remove from wishlist">
                                            <i class="icon-close"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- <div class="wishlist-share">
                            <div class="social-icons social-icons-sm mb-2">
                                <label class="social-label">Share on:</label>
                                <a href="#" class="social-icon" title="Facebook" target="_blank"><i class="icon-facebook-f"></i></a>
                                <a href="#" class="social-icon" title="Twitter" target="_blank"><i class="icon-twitter"></i></a>
                                <a href="#" class="social-icon" title="Instagram" target="_blank"><i class="icon-instagram"></i></a>
                                <a href="#" class="social-icon" title="Youtube" target="_blank"><i class="icon-youtube"></i></a>
                                <a href="#" class="social-icon" title="Pinterest" target="_blank"><i class="icon-pinterest"></i></a>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </main>
    </Skeleton>
</template>

<style scoped>
.old-price {
    text-decoration: line-through;
    color: #999;
    font-size: 0.9em;
    margin-left: 10px;
}

.new-price {
    color: #39f;
    font-weight: 600;
}



/* Toast Notification Styles */
.toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 300px;
    max-width: 500px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    animation: slideIn 0.3s ease-out;
}

.toast-content {
    display: flex;
    align-items: center;
    padding: 16px 20px;
    background: #fff;
    border-radius: 8px;
}

.toast-icon {
    flex-shrink: 0;
    width: 24px;
    height: 24px;
    margin-right: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.toast-message {
    flex: 1;
    font-size: 14px;
    line-height: 1.5;
    color: #333;
}

.toast-close {
    flex-shrink: 0;
    width: 24px;
    height: 24px;
    margin-left: 12px;
    background: none;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0.5;
    transition: opacity 0.2s;
}

.toast-close:hover {
    opacity: 1;
}

.toast-success {
    border-left: 4px solid #10b981;
}

.toast-success .toast-icon {
    color: #10b981;
}

.toast-error {
    border-left: 4px solid #ef4444;
}

.toast-error .toast-icon {
    color: #ef4444;
}

.toast-warning {
    border-left: 4px solid #f59e0b;
}

.toast-warning .toast-icon {
    color: #f59e0b;
}

.toast-info {
    border-left: 4px solid #3b82f6;
}

.toast-info .toast-icon {
    color: #3b82f6;
}

/* Toast Animations */
.toast-enter-active, .toast-leave-active {
    transition: all 0.3s ease;
}

.toast-enter-from {
    transform: translateX(100%);
    opacity: 0;
}

.toast-leave-to {
    transform: translateX(100%);
    opacity: 0;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>