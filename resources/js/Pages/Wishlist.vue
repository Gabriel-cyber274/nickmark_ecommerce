<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { onMounted, ref, computed } from 'vue';
import Skeleton from './Skeleton.vue';
import { useWishlist } from '../composables/useWishlist';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    auth: Object,
    wishlist: {
        type: Array,
        default: () => []
    }
});

const { wishlistItems, toggleWishlist, isInWishlist } = useWishlist(props.auth);
const guestWishlistProducts = ref([]);
const isLoading = ref(false);

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
                                        <div v-if="item.product?.previous_price || item.previous_price">
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
                                        <button class="btn btn-block btn-outline-primary-2">
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

                        <div class="wishlist-share">
                            <div class="social-icons social-icons-sm mb-2">
                                <label class="social-label">Share on:</label>
                                <a href="#" class="social-icon" title="Facebook" target="_blank"><i class="icon-facebook-f"></i></a>
                                <a href="#" class="social-icon" title="Twitter" target="_blank"><i class="icon-twitter"></i></a>
                                <a href="#" class="social-icon" title="Instagram" target="_blank"><i class="icon-instagram"></i></a>
                                <a href="#" class="social-icon" title="Youtube" target="_blank"><i class="icon-youtube"></i></a>
                                <a href="#" class="social-icon" title="Pinterest" target="_blank"><i class="icon-pinterest"></i></a>
                            </div>
                        </div>
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
</style>