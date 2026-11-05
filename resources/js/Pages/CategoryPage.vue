<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { useWishlist } from '../composables/useWishlist';
import { useCart } from '../composables/useCart';
import { usePage } from '@inertiajs/vue3';
import Skeleton from './Skeleton.vue';

const page = usePage();

let prop = defineProps({
    category: {
        type: Object
    },
    products: {
        type: Array
    },
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    auth: {
        type: Object
    }
});

const { isInWishlist, toggleWishlist } = useWishlist(prop.auth);
const { addToCart, isInCart } = useCart(prop.auth);

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

watch(() => page.props.flash, (flash) => {
    if (flash?.success) {
        showToast(flash.success, 'success');
    }
    if (flash?.error) {
        showToast(flash.error, 'error');
    }
}, { deep: true, immediate: true });

const handleWishlistToggle = async (productId) => {
    try {
        const result = await toggleWishlist(productId);
        const isAdded = isInWishlist(productId);
        showToast(
            isAdded
                ? 'Product added to wishlist!'
                : 'Product removed from wishlist!',
            'success'
        );
    } catch (error) {
        console.error('Error toggling wishlist:', error);
        showToast('Failed to update wishlist. Please try again.', 'error');
    }
};

const handleAddToCart = async (productId) => {
    try {
        const result = await addToCart(productId, 1);
        showToast('Product added to cart!', 'success');
    } catch (error) {
        console.error('Error adding to cart:', error);
        showToast('Failed to add product to cart. Please try again.', 'error');
    }
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0

    }).format(price);
};

const getRatingWidth = (product) => {
    if (!product.reviews || product.reviews.length === 0) return 0;
    const avg = product.reviews.reduce((sum, r) => sum + (r.rate || 0), 0) / product.reviews.length;
    return Math.round((avg / 5) * 100);
};

const breadcrumbJsonLd = computed(() => {
    return JSON.stringify({
        '@context': 'https://schema.org/',
        '@type': 'BreadcrumbList',
        itemListElement: [
            {
                '@type': 'ListItem',
                position: 1,
                name: 'Home',
                item: baseUrl.value
            },
            {
                '@type': 'ListItem',
                position: 2,
                name: prop.category.name,
                item: baseUrl.value + '/categories/' + prop.category.slug
            }
        ]
    });
});
// Safe base URL for SEO
const baseUrl = computed(() => {
    if (typeof window !== 'undefined' && window.location) {
        return window.location.origin;
    }
    return '';
});

</script>

<template>
    <Head :title="`${prop.category.name} - Nickmark`">
        <meta
            name="description"
            :content="`Shop ${prop.category.name} products at best prices. Quality electronics and gadgets from Nickmark.`"
        >
        <link rel="canonical" :href="`${baseUrl}/categories/${prop.category.slug}`">
        <meta property="og:title" :content="`${prop.category.name} - Nickmark`">
        <meta property="og:description" :content="`Shop ${prop.category.name} products at best prices. Quality electronics and gadgets from Nickmark.`">
        <meta property="og:type" content="website">
        <meta property="og:url" :href="`${baseUrl}/categories/${prop.category.slug}`">
        <meta name="twitter:title" :content="`${prop.category.name} - Nickmark`">
        <meta name="twitter:description" :content="`Shop ${prop.category.name} products at best prices.`">
        <meta name="twitter:card" content="summary_large_image">
    </Head>

    <!-- JSON-LD BreadcrumbList for Category -->
    <div v-html="breadcrumbJsonLd" style="display:none"></div>

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

            <Skeleton page="categories" :auth="auth">
        <main class="main mt-3 page-fade">
            <div class="page-header text-center" style="background-image: url('assets/images/page-header-bg.jpg')">
                <div class="container">
                    <h1 class="page-title">{{ prop.category.name }}</h1>
                </div>
            </div>

            <div class="page-content mt-4">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-9">
                            <div class="products mb-3">
                                <div class="row justify-content-center">
                                    <div
                                        v-for="product in products"
                                        :key="product.id"
                                        class="col-12 col-md-4 col-lg-4"
                                    >
                                        <div class="product product-7 text-center lift-card">
                                            <figure class="product-media zoom-img">
                                                <span v-if="product.is_new" class="product-label label-new">New</span>
                                                <span v-else-if="product.is_featured" class="product-label label-top">Featured</span>

                                                <Link :href="`/products/${product.slug}`">
                                                    <img
                                                        :src="product.images && product.images.length > 0 ? product.images[0].image_url : '/assets/images/products/product-default.jpg'"
                                                        :alt="product.name + ' product image'"
                                                        class="product-image"
                                                        @error="$event.target.src='/assets/images/products/product-default.jpg'"
                                                    >
                                                </Link>

                                                <div class="product-action-vertical">
                                                    <a
                                                        href="#"
                                                        @click.prevent="handleWishlistToggle(product.id)"
                                                        class="btn-product-icon btn-wishlist"
                                                        :class="{ 'active': isInWishlist(product.id) }"
                                                        :title="isInWishlist(product.id) ? 'Remove from wishlist' : 'Add to wishlist'"
                                                    ></a>

                                                    <Link
                                                        :href="`/products/${product.slug}`"
                                                        class="btn-product-icon btn-quickview"
                                                        title="Quick view"
                                                    ><span>Quick view</span></Link>
                                                </div>

                                                <div class="product-action">
                                                    <a href="#" @click.prevent="handleAddToCart(product.id)" class="btn-product btn-cart"><span>add to cart</span></a>
                                                </div>
                                            </figure>

                                            <div class="product-body">
                                                <div class="product-cat">
                                                    <a :href="`/categories/${product.category.slug}`">{{ product.category?.name || 'Category' }}</a>
                                                </div>
                                                <h3 class="product-title">
                                                    <Link :href="`/products/${product.slug}`">{{ product.name }}</Link>
                                                </h3>
                                                <div class="product-price flex-column">
                                                    <div>₦{{ parseFloat(product.price).toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</div>
                                                    <span v-if="product.previous_price && product.previous_price > product.price" class="old-price" style="text-decoration: line-through;">
                                                        ₦{{ parseFloat(product.previous_price).toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                                                    </span>
                                                </div>
                                                <div class="ratings-container">
                                                    <div class="ratings">
                                                        <div class="ratings-val" :style="{ width: getRatingWidth(product) + '%' }"></div>
                                                    </div>
                                                    <span class="ratings-text">({{ product.views || 0 }} Views)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="!products || products.length === 0" class="col-12 text-center py-5">
                                        <p class="text-muted">No products found in this category.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </Skeleton>
</template>

<style scoped>
.main {
    padding: 20px;
}

/* ===== Smooth Page Animations ===== */
.page-fade {
    animation: pageFadeIn 0.6s ease-out forwards;
}

@keyframes pageFadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Staggered list items */
.stagger > * {
    opacity: 0;
    animation: fadeUp 0.5s ease forwards;
}
.stagger > *:nth-child(1) { animation-delay: 0.05s; }
.stagger > *:nth-child(2) { animation-delay: 0.1s; }
.stagger > *:nth-child(3) { animation-delay: 0.15s; }
.stagger > *:nth-child(4) { animation-delay: 0.2s; }
.stagger > *:nth-child(5) { animation-delay: 0.25s; }
.stagger > *:nth-child(6) { animation-delay: 0.3s; }

@keyframes fadeUp {
    to { opacity: 1; transform: translateY(0); }
}

/* Hover lift card */
.lift-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.lift-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 35px rgba(0,0,0,0.12);
}

/* Image hover zoom */
.zoom-img {
    overflow: hidden;
}
.zoom-img img {
    transition: transform 0.5s ease;
}
.zoom-img:hover img {
    transform: scale(1.06);
}

/* Button subtle lift */
.btn-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.btn-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 14px rgba(0,0,0,0.15);
}

/* Table row hover */
.table-hover-row tr {
    transition: background 0.2s ease, transform 0.2s ease;
}
.table-hover-row tr:hover {
    background: #f8f9fa;
    transform: translateX(4px);
}

/* Focus ring for form inputs */
.focus-input:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    border-color: #3b82f6;
    transition: box-shadow 0.2s ease, border-color 0.2s ease;
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
