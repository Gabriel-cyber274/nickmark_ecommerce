<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { onMounted, nextTick, onUnmounted, ref, reactive, computed, watch } from 'vue';
import Skeleton from './Skeleton.vue';
import { useWishlist } from '../composables/useWishlist';
import { useCart } from '../composables/useCart';

import { usePage } from '@inertiajs/vue3';


const page = usePage();

let prop = defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    categories: {
        type: Array
    },
    products: {
        type: Object
    },
    availableBrands: {
        type: Array,
        default: () => []
    },
    priceRange: {
        type: Object,
        default: () => ({ min: 0, max: 100000 })
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    auth: {
        type: Object  
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

const { isInWishlist, toggleWishlist } = useWishlist(prop.auth);
const { addToCart, isInCart } = useCart(prop.auth);

// Add this function
const handleAddToCart = async (productId) => {
    try {
        const result = await addToCart(productId, 1);
        console.log(result.message);
        showToast('Product added to cart!', 'success');
    } catch (error) {
        console.error('Error adding to cart:', error);
        showToast('Failed to add product to cart. Please try again.', 'error');
    }
};

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


// Track current image for each product
const productImages = reactive({});

// Filter state
const selectedCategories = ref(prop.filters.categories || []);
const selectedBrands = ref(prop.filters.brands || []);
const sortBy = ref(prop.filters.sort || 'date');

// Price range state
const minPrice = ref(prop.filters.min_price || prop.priceRange.min);
const maxPrice = ref(prop.filters.max_price || prop.priceRange.max);
const isPriceChanging = ref(false);

// Initialize product images
const initProductImages = () => {
    if (prop.products?.data && prop.products.data.length > 0) {
        prop.products.data.forEach(product => {
            if (product.images && Array.isArray(product.images) && product.images.length > 0) {
                productImages[product.id] = {
                    currentImage: product.images[0].image_url,
                    images: product.images
                };
            } else {
                productImages[product.id] = {
                    currentImage: '/assets/images/products/product-default.jpg',
                    images: []
                };
            }
        });
    }
};

// Function to change product image
const changeProductImage = (productId, imageUrl, event) => {
    event.preventDefault();
    event.stopPropagation();
    
    if (productImages[productId]) {
        productImages[productId].currentImage = imageUrl;
        
        const productElement = event.target.closest('.product');
        if (productElement) {
            const mainImage = productElement.querySelector('.product-image');
            if (mainImage) {
                mainImage.src = imageUrl;
            }
        }
        
        const thumbnails = event.target.closest('.product').querySelectorAll('.product-nav-thumbs a');
        thumbnails.forEach(thumb => thumb.classList.remove('active'));
        
        event.target.closest('a').classList.add('active');
    }
};

// Toggle category filter
const toggleCategory = (categoryId) => {
    const index = selectedCategories.value.indexOf(categoryId);
    if (index > -1) {
        selectedCategories.value.splice(index, 1);
    } else {
        selectedCategories.value.push(categoryId);
    }
    applyFilters();
};

// Toggle brand filter
const toggleBrand = (brand) => {
    const index = selectedBrands.value.indexOf(brand);
    if (index > -1) {
        selectedBrands.value.splice(index, 1);
    } else {
        selectedBrands.value.push(brand);
    }
    applyFilters();
};

// Change sort order
const changeSortBy = (event) => {
    sortBy.value = event.target.value;
    applyFilters();
};

// Handle price range change
const onPriceChange = () => {
    isPriceChanging.value = true;
};

const onPriceChangeEnd = () => {
    isPriceChanging.value = false;
    applyFilters();
};

// Apply filters by navigating with query parameters
const applyFilters = () => {
    const params = {};
    
    if (selectedCategories.value.length > 0) {
        params.categories = selectedCategories.value;
    }
    
    if (selectedBrands.value.length > 0) {
        params.brands = selectedBrands.value;
    }
    
    if (sortBy.value && sortBy.value !== 'date') {
        params.sort = sortBy.value;
    }
    
    // Add price filters if they differ from default range
    if (minPrice.value !== prop.priceRange.min) {
        params.min_price = minPrice.value;
    }
    
    if (maxPrice.value !== prop.priceRange.max) {
        params.max_price = maxPrice.value;
    }
    
    router.get('/categories', params, {
        preserveState: true,
        preserveScroll: true,
    });
};

// Clear search
const clearSearch = () => {
    router.get('/categories', {}, {
        preserveState: false,
    });
};

// Clear all filters
const clearFilters = (event) => {
    event.preventDefault();
    selectedCategories.value = [];
    selectedBrands.value = [];
    sortBy.value = 'date';
    minPrice.value = prop.priceRange.min;
    maxPrice.value = prop.priceRange.max;
    
    router.get('/categories', {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

// Check if category is selected
const isCategorySelected = (categoryId) => {
    return selectedCategories.value.includes(categoryId);
};

// Check if brand is selected
const isBrandSelected = (brand) => {
    return selectedBrands.value.includes(brand);
};

// Format price for display
const formatPrice = (price) => {
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};

onMounted(() => {
    console.log('categories', prop.categories);
    console.log('products', prop.products);
    console.log('availableBrands', prop.availableBrands);
    console.log('priceRange', prop.priceRange);
    console.log('filters', prop.filters);
    
    // Debug: Check if images are loaded
    if (prop.products?.data) {
        prop.products.data.forEach(product => {
            console.log(`Product ${product.id} images:`, product.images);
        });
    }
    
    // Initialize product images
    initProductImages();
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

<Skeleton page='categories' :auth="auth">
    <main class="main">
        <div class="page-header text-center" style="background-image: url('assets/images/page-header-bg.jpg')">
            <div class="container">
                <h1 class="page-title">SHOP</h1>
            </div>
        </div>

        <div class="page-content mt-4">
            <div class="container">
                <div class="row">
                    <div class="col-lg-9">
                        <div class="toolbox">
                            <div class="toolbox-left">
                                <div class="toolbox-info">
                                    <span v-if="filters.search" class="mr-2">
                                        Search results for: <strong>"{{ filters.search }}"</strong>
                                        <a href="#" @click.prevent="clearSearch" class="ml-2 text-danger">
                                            <i class="icon-close"></i>
                                        </a>
                                    </span>
                                    Showing <span>{{ products.from || 0 }} - {{ products.to || 0 }} of {{ products.total || 0 }}</span> Products
                                </div>
                            </div>

                            <div class="toolbox-right">
                                <div class="toolbox-sort">
                                    <label for="sortby">Sort by:</label>
                                    <div class="select-custom">
                                        <select name="sortby" id="sortby" class="form-control" v-model="sortBy" @change="changeSortBy">
                                            <option value="date">Date</option>
                                            <option value="popularity">Most Popular</option>
                                            <option value="price_low">Price: Low to High</option>
                                            <option value="price_high">Price: High to Low</option>
                                            <option value="name">Name: A to Z</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="products mb-3">
                            <div class="row justify-content-center">
                                <div v-for="product in products.data" :key="product.id" class="col-6 col-md-4 col-lg-4">
                                    <div class="product product-7 text-center">
                                        <figure class="product-media">
                                            <span v-if="product.is_new" class="product-label label-new">New</span>
                                            <span v-else-if="product.is_featured" class="product-label label-top">Featured</span>
                                            
                                            <Link :href="`/product/${product.id}`">
                                                <img :src="productImages[product.id]?.currentImage || (product.images && product.images.length > 0 ? product.images[0].image_url : '/assets/images/products/product-default.jpg')" 
                                                     alt="Product image" 
                                                     class="product-image"
                                                     @error="$event.target.src='/assets/images/products/product-default.jpg'">
                                            </Link>

                                            <div class="product-action-vertical">
                                            <a
                                                href="#"
                                                @click.prevent="handleWishlistToggle(product.id)"
                                                class="btn-product-icon btn-wishlist"
                                                :class="{ 'active': isInWishlist(product.id) }"
                                                :title="isInWishlist(product.id) ? 'Remove from wishlist' : 'Add to wishlist'"
                                            ></a>

                                                <Link :href="`/product/${product.id}`" class="btn-product-icon btn-quickview" title="Quick view"><span>Quick view</span></Link>
                                            </div>

                                            <div class="product-action">
                                                <a href="#" @click.prevent="handleAddToCart(product.id)" class="btn-product btn-cart"><span>add to cart</span></a>
                                            </div>
                                        </figure>

                                        <div class="product-body">
                                            <div class="product-cat">
                                                <a href="#">{{ product.category?.name || 'Category' }}</a>
                                            </div>
                                            <h3 class="product-title">
                                                <Link :href="`/product/${product.id}`">{{ product.name }}</Link>
                                            </h3>
                                            <div class="product-price flex-column">
                                               <div>₦{{ parseFloat(product.price).toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</div> 
                                                <span v-if="product.previous_price && product.previous_price > product.price" class="old-price" style="text-decoration: line-through;"> ₦{{ parseFloat(product.previous_price).toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</span>
                                            </div>
                                            <div class="ratings-container">
                                                <div class="ratings">
                                                    <div class="ratings-val" style="width: 80%;"></div>
                                                </div>
                                                <span class="ratings-text">( {{ product.views || 0 }} Views )</span>
                                            </div>

                                            <div v-if="product.images && product.images.length > 1" class="product-nav product-nav-thumbs">
                                                <a v-for="(image, index) in product.images.slice(0, 3)" 
                                                   :key="image.id" 
                                                   href="#" 
                                                   :class="{ active: index === 0 }"
                                                   @click="changeProductImage(product.id, image.image_url, $event)">
                                                    <img :src="image.image_url" alt="product thumbnail">
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Empty state if no products -->
                                <div v-if="!products.data || products.data.length === 0" class="col-12 text-center py-5">
                                    <p class="text-muted">No products found matching your filters.</p>
                                </div>
                            </div>
                        </div>

                        <nav v-if="products.last_page > 1" aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <li :class="['page-item', { disabled: !products.prev_page_url }]">
                                    <Link :href="products.prev_page_url || '#'" 
                                          class="page-link page-link-prev" 
                                          aria-label="Previous" 
                                          :tabindex="!products.prev_page_url ? -1 : 0"
                                          preserve-state
                                          preserve-scroll>
                                        <span aria-hidden="true"><i class="icon-long-arrow-left"></i></span>Prev
                                    </Link>
                                </li>
                                
                                <li v-for="page in products.last_page" 
                                    :key="page" 
                                    :class="['page-item', { active: page === products.current_page }]">
                                    <Link :href="`/categories?page=${page}`" 
                                          class="page-link"
                                          preserve-state
                                          preserve-scroll>{{ page }}</Link>
                                </li>
                                
                                <li class="page-item-total">of {{ products.last_page }}</li>
                                
                                <li :class="['page-item', { disabled: !products.next_page_url }]">
                                    <Link :href="products.next_page_url || '#'" 
                                          class="page-link page-link-next" 
                                          aria-label="Next"
                                          preserve-state
                                          preserve-scroll>
                                        Next <span aria-hidden="true"><i class="icon-long-arrow-right"></i></span>
                                    </Link>
                                </li>
                            </ul>
                        </nav>
                    </div>

                    <aside class="col-lg-3 order-lg-first">
                        <div class="sidebar sidebar-shop">
                            <div class="widget widget-clean">
                                <label>Filters:</label>
                                <a href="#" class="sidebar-filter-clear" @click="clearFilters">Clean All</a>
                            </div>

                            <div class="widget widget-collapsible">
                                <h3 class="widget-title">
                                    <a data-toggle="collapse" href="#widget-1" role="button" aria-expanded="true" aria-controls="widget-1">
                                        Category
                                    </a>
                                </h3>

                                <div class="collapse show" id="widget-1">
                                    <div class="widget-body">
                                        <div class="filter-items filter-items-count">
                                            <div v-for="category in categories" :key="category.id" class="filter-item">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" 
                                                           class="custom-control-input" 
                                                           :id="`cat-${category.id}`"
                                                           :checked="isCategorySelected(category.id)"
                                                           @change="toggleCategory(category.id)">
                                                    <label class="custom-control-label" :for="`cat-${category.id}`">{{ category.name }}</label>
                                                </div>
                                                <span class="item-count">{{ category.products_count }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="widget widget-collapsible">
                                <h3 class="widget-title">
                                    <a data-toggle="collapse" href="#widget-4" role="button" aria-expanded="true" aria-controls="widget-4">
                                        Brand
                                    </a>
                                </h3>

                                <div class="collapse show" id="widget-4">
                                    <div class="widget-body">
                                        <div v-if="availableBrands.length > 0" class="filter-items">
                                            <div v-for="brand in availableBrands" :key="brand" class="filter-item">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" 
                                                           class="custom-control-input" 
                                                           :id="`brand-${brand}`"
                                                           :checked="isBrandSelected(brand)"
                                                           @change="toggleBrand(brand)">
                                                    <label class="custom-control-label" :for="`brand-${brand}`">{{ brand }}</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else class="text-muted p-3">
                                            No brands available
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="widget widget-collapsible">
                                <h3 class="widget-title">
                                    <a data-toggle="collapse" href="#widget-5" role="button" aria-expanded="true" aria-controls="widget-5">
                                        Price
                                    </a>
                                </h3>

                                <div class="collapse show" id="widget-5">
                                    <div class="widget-body">
                                        <div class="filter-price">
                                            <div class="filter-price-text mb-3">
                                                <strong>Price Range:</strong>
                                                <span class="ml-2">{{ formatPrice(minPrice) }} - {{ formatPrice(maxPrice) }}</span>
                                            </div>
                                            
                                            <div class="price-slider-wrapper">
                                                <div class="mb-3">
                                                    <label class="small text-muted">Min Price: {{ formatPrice(minPrice) }}</label>
                                                    <input 
                                                        type="range" 
                                                        class="form-range w-100" 
                                                        :min="priceRange.min" 
                                                        :max="priceRange.max"
                                                        :step="1000"
                                                        v-model.number="minPrice"
                                                        @input="onPriceChange"
                                                        @change="onPriceChangeEnd"
                                                    >
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="small text-muted">Max Price: {{ formatPrice(maxPrice) }}</label>
                                                    <input 
                                                        type="range" 
                                                        class="form-range w-100" 
                                                        :min="priceRange.min" 
                                                        :max="priceRange.max"
                                                        :step="1000"
                                                        v-model.number="maxPrice"
                                                        @input="onPriceChange"
                                                        @change="onPriceChangeEnd"
                                                    >
                                                </div>
                                                
                                                <div class="d-flex justify-content-between text-muted small">
                                                    <span>{{ formatPrice(priceRange.min) }}</span>
                                                    <span>{{ formatPrice(priceRange.max) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </main>
</Skeleton>
</template>

<style scoped>
.product-media {
    height: 277px;
}
.product-media>a {
    height: 100%;
}
.product-image {
    height: 100%;
    object-fit: cover;
}
.owl-carousel .owl-item img {
    height: 100%;
    object-fit: cover;
}

/* Price Range Slider Styles */
.filter-price {
    padding: 10px 0;
}

.price-slider-wrapper {
    padding: 10px 0;
}

.form-range {
    height: 6px;
    background: #e7e7e7;
    border-radius: 3px;
    outline: none;
    -webkit-appearance: none;
}

.form-range::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 18px;
    height: 18px;
    background: #c96;
    cursor: pointer;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.form-range::-moz-range-thumb {
    width: 18px;
    height: 18px;
    background: #c96;
    cursor: pointer;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.form-range::-webkit-slider-thumb:hover {
    background: #b85;
}

.form-range::-moz-range-thumb:hover {
    background: #b85;
}

/* Added to wishlist */
.btn-wishlist.active::before {
    content: '\f233'; 
    font-family: "molla";
    font-weight: 400;
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