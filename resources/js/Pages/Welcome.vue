<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, nextTick, onUnmounted, ref,  computed } from 'vue';
import Skeleton from './Skeleton.vue'
import { useWishlist } from '../composables/useWishlist';
import { usePage } from '@inertiajs/vue3';

import { useCart } from '../composables/useCart';

import { watch } from 'vue';

const page = usePage();

let prop = defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    popularCategories: {
        type: Array
    },
    featuredProducts : {
        type: Array
    },
    recommendedProducts : {
        type: Array
    },
    newProductCategories : {
        type: Object  
    },
    auth: {
        type: Object  
    }
});

// Safe base URL for SEO (avoids SSR window reference errors)
const baseUrl = computed(() => {
    if (typeof window !== 'undefined' && window.location) {
        return window.location.origin;
    }
    return '';
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

const handleWishlistToggle = async (productId) => {
    try {
        const result = await toggleWishlist(productId);
        // Show toast notification
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

// Add this function
const handleAddToCart = async (productId) => {
    try {
        const result = await addToCart(productId, 1);
        // Show toast notification
        showToast('Product added to cart!', 'success');
    } catch (error) {
        console.error('Error adding to cart:', error);
        showToast('Failed to add product to cart. Please try again.', 'error');
    }
};


const popularCategories = ref(prop.popularCategories)

const slugify = (text) => {
    return text
        .toString()
        .toLowerCase()
        .replace(/\s+/g, '-')           // Replace spaces with -
        .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
        .replace(/\-\-+/g, '-')         // Replace multiple - with single -
        .replace(/^-+/, '')             // Trim - from start of text
        .replace(/-+$/, '');            // Trim - from end of text
}


// Computed property to get categories excluding 'all'
const filteredCategories = computed(() => {
    const result = [];
    for (const [categoryName, categoryProducts] of Object.entries( prop.newProductCategories)) {
        if (categoryName !== 'all') {
            result.push({ categoryName, categoryProducts });
        }
    }
    return result;
});

// Get 'all' products separately
const allProducts = computed(() => {
    return  prop.newProductCategories?.all || [];
});

// Helper to calculate average rating percentage for stars display
const getRatingWidth = (product) => {
    if (!product.reviews || product.reviews.length === 0) return 0;
    const avg = product.reviews.reduce((sum, r) => sum + (r.rate || 0), 0) / product.reviews.length;
    return Math.round((avg / 5) * 100);
};

onMounted(()=>{
    console.log('recommend', prop.auth)
})


const brands = ref([
  { id: 1, src: "assets/images/brands/1.png", name: 'samsung' },
  { id: 2, src: "assets/images/brands/2.png", name: 'dell' },
  { id: 3, src: "assets/images/brands/3.png", name: 'apple' },
  { id: 4, src: "assets/images/brands/4.png", name: 'windows' },
  { id: 5, src: "assets/images/brands/5.png", name: 'asus' },
  { id: 6, src: "assets/images/brands/6.png", name: 'hp' },
]);

// Scroll Animation Observer
onMounted(() => {
    const observerOptions = {
        root: null,
        rootMargin: '0px 0px -50px 0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    animatedElements.forEach(el => observer.observe(el));

    onUnmounted(() => {
        observer.disconnect();
    });
});

</script>

<template>
    <Head
        title="Welcome to Nickmark - Quality Electronics & Gadgets"
    >
        <meta name="description" content="Shop the latest electronics, laptops, smartphones, tablets, and accessories at best prices. Quality products with warranty.">
        <link rel="canonical" :href="`${baseUrl}`">
        <meta property="og:title" content="Welcome to Nickmark - Quality Electronics & Gadgets">
        <meta property="og:description" content="Shop the latest electronics, laptops, smartphones, tablets, and accessories at best prices.">
        <meta property="og:type" content="website">
        <meta property="og:url" :href="`${baseUrl}`">
        <meta name="twitter:title" content="Welcome to Nickmark - Quality Electronics & Gadgets">
        <meta name="twitter:description" content="Shop the latest electronics, laptops, smartphones, tablets, and accessories at best prices.">
        <meta name="twitter:card" content="summary_large_image">
    </Head>

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

    <Skeleton page="home" :auth="auth">

        <main class="main">
            <!-- Page Entrance Animation Wrapper -->
            <transition name="page" mode="out-in">
                <div class="page-content" key="welcome-page" style="padding-left: 20px; padding-right: 20px;">
                    <div class="intro-slider-container mb-5 animate-on-scroll" data-animation="fade-up" data-delay="0">
                <div class="intro-slider owl-carousel owl-theme owl-nav-inside owl-light" data-toggle="owl" 
                    data-owl-options='{
                        "dots": true,
                        "nav": false, 
                        "responsive": {
                            "1200": {
                                "nav": true,
                                "dots": false
                            }
                        }
                    }'>
 <div v-for="product in featuredProducts" :key="product.id" class="intro-slide">
        <div class="container">
            <div class="row align-items-center">
                <!-- Left side - Product Image -->
                <div class="col-12 col-md-6">
                    <div class="intro-image">
                        <img :src="product.images[0]?.image_url" :alt="product.name" class="img-fluid">
                    </div>
                </div>
                
                <!-- Right side - Product Content -->
                <div class="col-12 col-md-6">
                    <div class="intro-content">
                        <h3 :class="['intro-subtitle', product.previous_price? 'text-third': 'text-primary']">
                            {{ product.previous_price ?'Deals and Promotions':product.is_new?'New Arrival': 'Promotions' }}
                        </h3>
                        
                        <h1 class="intro-title">
                            {{ product.name }} <br> 
                            <span v-if="product.answers && product.answers.length > 0">
                                <span v-for="(answer, index) in product.answers.slice(0, 2)" :key="answer.id">
                                    {{ answer.answer }}
                                    <span v-if="index < 1 && product.answers.length > 1">, </span>
                                </span>
                            </span>
                        </h1>

                        <div v-if="product.previous_price" class="intro-price">
                            <sup class="intro-old-price"> 
                                &#8358;{{ parseFloat(product.previous_price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                            </sup>
                            <span class="text-third">
                                &#8358;{{ parseFloat(product.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                            </span>
                        </div>

                        <div v-if="!product.previous_price" class="intro-price">
                            <sup>Today:</sup>
                            <span class="text-primary">
                                &#8358;{{ parseFloat(product.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                            </span>
                        </div>

                        <Link :href="`/products/${product.slug}`" class="btn btn-primary btn-round">
                            <span>Shop More</span>
                            <i class="icon-long-arrow-right"></i>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>

                
                </div><!-- End .intro-slider owl-carousel owl-simple -->

                <span class="slider-loader"></span><!-- End .slider-loader -->
            </div><!-- End .intro-slider-container -->

            <div class="container animate-on-scroll" data-animation="fade-up" data-delay="100">
                <h2 class="title text-center mb-4 animate-on-scroll" data-animation="fade-up" data-delay="0">Explore Popular Categories</h2><!-- End .title text-center -->
                
                <div class="cat-blocks-container">
                    <div class="row">
                        <transition-group name="stagger" tag="div" class="row">
                            <div v-for="(category, index) in popularCategories" :key="category.id" class="col-6 col-sm-4 col-lg-2" :style="{ transitionDelay: `${index * 100}ms` }">
                                <Link :href="`/categories/${category.slug}`" class="cat-block animate-hover-lift">
                                    <figure>
                                        <span>
                                            <img :src="category.image" alt="Category image">
                                        </span>
                                    </figure>

                                    <h3 class="cat-block-title">{{ category.name }}</h3><!-- End .cat-block-title -->
                                </Link>
                            </div><!-- End .col-sm-4 col-lg-2 -->
                        </transition-group>
                    </div><!-- End .row -->
                </div><!-- End .cat-blocks-container -->
            </div><!-- End .container -->


            <div class="mb-3"></div><!-- End .mb-5 -->

            <div class="container new-arrivals animate-on-scroll" data-animation="fade-up" data-delay="200">
                    <div class="heading heading-flex mb-3">
                        <div class="heading-left">
                            <h2 class="title animate-on-scroll" data-animation="fade-right" data-delay="0">New Arrivals</h2><!-- End .title -->
                        </div><!-- End .heading-left -->

                        <div class="heading-right">
                            <ul class="nav nav-pills nav-border-anim justify-content-center animate-on-scroll" data-animation="fade-left" data-delay="100" role="tablist">
                                <!-- First tab is "All" -->
                                <li class="nav-item">
                                    <a class="nav-link active" id="new-all-link" data-toggle="tab" href="#new-all-tab" role="tab" aria-controls="new-all-tab" aria-selected="true">All</a>
                                </li>
                                <!-- Other category tabs using computed property -->
                                <li v-for="item in filteredCategories" :key="item.categoryName" class="nav-item">
                                    <a class="nav-link" :id="'new-' + slugify(item.categoryName) + '-link'" data-toggle="tab" :href="'#new-' + slugify(item.categoryName) + '-tab'" role="tab" :aria-controls="'new-' + slugify(item.categoryName) + '-tab'" aria-selected="false">{{ item.categoryName }}</a>
                                </li>
                            </ul>
                        </div><!-- End .heading-right -->
                    </div><!-- End .heading -->

                    <div class="tab-content tab-content-carousel just-action-icons-sm">
                        <!-- All Products Tab -->
                        <div class="tab-pane p-0 fade show active" id="new-all-tab" role="tabpanel" aria-labelledby="new-all-link">
                            <div class="products">
                                <div class="row justify-content-start">
                                    <transition-group name="product-list" tag="div" class="row justify-content-start">
                                        <!-- Directly use the "all" products -->
                                        <div v-for="(product, index) in allProducts" :key="product.id" class="col-6 col-md-4 col-lg-3" :style="{ transitionDelay: `${index * 100}ms` }">
                                            <div class="product product-2 animate-on-scroll animate-hover-lift" data-animation="fade-up" data-delay="0">
                                                <figure class="product-media">
                                                    <span v-if="product.is_new" class="product-label label-circle label-new">New</span>
                                                    <span v-if="product.previous_price" class="product-label label-circle label-sale">Sale</span>
                                                    <Link :href="`/products/${product.slug}`">
                                                        <img :src="product.images[0]?.image_url" :alt="product.name" class="product-image">
                                                    </Link>
            
                                                    <div class="product-action-vertical">
                                                        <a
                                                            href="#"
                                                            @click.prevent="handleWishlistToggle(product.id)"
                                                            class="btn-product-icon btn-wishlist"
                                                            :class="{ 'active': isInWishlist(product.id) }"
                                                            :title="isInWishlist(product.id) ? 'Remove from wishlist' : 'Add to wishlist'"
                                                        ></a>
            
                                                    </div><!-- End .product-action -->
                                                    
            
                                                    <div class="product-action">
                                                        <a href="#" class="btn-product btn-cart" @click.prevent="handleAddToCart(product.id)"  title="Add to cart"><span>add to cart</span></a>
                                                        <Link :href="`/products/${product.slug}`" class="btn-product btn-quickview" title="Quick view"><span>quick view</span></Link>
                                                    </div><!-- End .product-action -->
                                                </figure><!-- End .product-media -->
            
                                                <div class="product-body">
                                                    <div class="product-cat">
                                                        <Link :href="`/categories/${product.category.slug}`">{{ product.category.name }}</Link>
                                                    </div><!-- End .product-cat -->
                                                    <h3 class="product-title">
                                                        <Link :href="`/products/${product.slug}`">{{ product.name }} - <span v-if="product.answers && product.answers.length > 0">
                                                            <span v-for="(answer, index) in product.answers.slice(0, 2)" :key="answer.id">
                                                                {{ answer.answer }}
                                                                <span v-if="index < 1 && product.answers.length > 1">, </span>
                                                            </span>
                                                        </span></Link>
            </h3><!-- End .product-title -->
                                                    <div v-if="product.previous_price" class="product-price">
                                                        <span class="new-price">&#8358;{{ parseFloat(product.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
                                                        <span class="old-price">Was &#8358;{{ parseFloat(product.previous_price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
                                                    </div>
            
                                                    <div v-if="!product.previous_price" class="product-price" style="color: #39f !important;">
                                                        &#8358;{{ parseFloat(product.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                                    </div><!-- End .product-price -->
                                                    <div class="ratings-container">
                                                        <div class="ratings">
                                                            <div class="ratings-val" :style="{ width: getRatingWidth(product) + '%' }"></div>
                                                        </div><!-- End .ratings -->
                                                        <span class="ratings-text">( {{ product.reviews?.length || 0 }} Review{{ (product.reviews?.length || 0) !== 1 ? 's' : '' }} )</span>
                                                    </div><!-- End .rating-container -->
                                                </div><!-- End .product-body -->
                                            </div><!-- End .product -->
                                        </div><!-- End .col -->
                                    </transition-group>
                                </div><!-- End .row -->
                            </div><!-- End .products -->
                        </div><!-- .End .tab-pane -->

                        <!-- Category-specific Tabs using computed property -->
                        <div v-for="item in filteredCategories" 
                            :key="item.categoryName" 
                            class="tab-pane p-0 fade" 
                            :id="'new-' + slugify(item.categoryName) + '-tab'" 
                            :aria-labelledby="'new-' + slugify(item.categoryName) + '-link'">
                            <div class="products">
                                <div class="row justify-content-start">
                                    <transition-group name="product-list" tag="div" class="row justify-content-start">
                                        <div v-for="(product, index) in item.categoryProducts" :key="product.id" class="col-6 col-md-4 col-lg-3" :style="{ transitionDelay: `${index * 100}ms` }">
                                            <div class="product product-2 animate-on-scroll animate-hover-lift" data-animation="fade-up" data-delay="0">
                                                <figure class="product-media">
                                                    <span v-if="product.is_new" class="product-label label-circle label-new">New</span>
                                                    <span v-if="product.previous_price" class="product-label label-circle label-sale">Sale</span>
                                                    <Link :href="`/products/${product.slug}`">
                                                        <img :src="product.images[0]?.image_url" :alt="product.name" class="product-image">
                                                    </Link>
            
                                                    <div class="product-action-vertical">
                                                        <a
                                                            href="#"
                                                            @click.prevent="handleWishlistToggle(product.id)"
                                                            class="btn-product-icon btn-wishlist"
                                                            :class="{ 'active': isInWishlist(product.id) }"
                                                            :title="isInWishlist(product.id) ? 'Remove from wishlist' : 'Add to wishlist'"
                                                        ></a>
                                                    </div><!-- End .product-action -->
            
                                                    <div class="product-action">
                                                        <a href="#" class="btn-product btn-cart" @click.prevent="handleAddToCart(product.id)" title="Add to cart"><span>add to cart</span></a>
                                                        <Link :href="`/products/${product.slug}`" class="btn-product btn-quickview" title="Quick view"><span>quick view</span></Link>
                                                    </div><!-- End .product-action -->
                                                </figure><!-- End .product-media -->
            
                                                <div class="product-body">
                                                    <div class="product-cat">
                                                        <Link :href="`/categories/${product.category.slug}`">{{ product.category.name }}</Link>
                                                    </div><!-- End .product-cat -->
                                                    <h3 class="product-title">
                                                        <Link :href="`/products/${product.slug}`">{{ product.name }} - <span v-if="product.answers && product.answers.length > 0">
                                                            <span v-for="(answer, index) in product.answers.slice(0, 2)" :key="answer.id">
                                                                {{ answer.answer }}
                                                                <span v-if="index < 1 && product.answers.length > 1">, </span>
                                                            </span>
                                                        </span></Link>
                                                    </h3><!-- End .product-title -->
            
                                                    <div v-if="product.previous_price" class="product-price">
                                                        <span class="new-price">&#8358;{{ parseFloat(product.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
                                                        <span class="old-price">Was &#8358;{{ parseFloat(product.previous_price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
                                                    </div>
            
            <div v-if="!product.previous_price" class="product-price" style="color: #39f !important;">
                                                        &#8358;{{ parseFloat(product.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                                    </div><!-- End .product-price -->
                                                    
                                                    <div class="ratings-container">
                                                        <div class="ratings">
                                                            <div class="ratings-val" :style="{ width: getRatingWidth(product) + '%' }"></div>
                                                        </div><!-- End .ratings -->
                                                        <span class="ratings-text">( {{ product.reviews?.length || 0 }} Review{{ (product.reviews?.length || 0) !== 1 ? 's' : '' }} )</span>
                                                    </div><!-- End .rating-container -->
                                                </div><!-- End .product-body -->
                                            </div><!-- End .product -->
                                        </div><!-- End .col -->
                                    </transition-group>
                                </div><!-- End .row -->
                            </div><!-- End .products -->
                        </div><!-- End .tab-pane -->
                    </div><!-- End .tab-content -->
                </div><!-- End .container -->

            <div class="mb-6"></div><!-- End .mb-6 -->


<div class="container animate-on-scroll" data-animation="fade-up" data-delay="300">
  <hr class="mb-0">
  <div class="brand-marquee-container mt-5 mb-5">
    <div class="brand-marquee">
      <div class="brand-marquee-content">
        <Link v-for="brand in brands" :key="brand.id" :href="'/categories?search='+brand.name" class="brand animate-hover-scale">
          <img :src="brand.src" alt="Brand Name">
        </Link>
        <!-- Duplicate for seamless loop -->
        <Link v-for="brand in brands" :key="`${brand.id}-duplicate`" :href="'/categories?search='+brand.name" class="brand animate-hover-scale">
          <img :src="brand.src" alt="Brand Name">
        </Link>
      </div>
    </div>
  </div>
</div>

            <div class="mb-5"></div><!-- End .mb-5 -->

<div class="container for-you animate-on-scroll" data-animation="fade-up" data-delay="400">
                <div class="heading heading-flex mb-3">
                    <div class="heading-left">
                        <h2 class="title animate-on-scroll" data-animation="fade-right" data-delay="0">Recommendation For You</h2><!-- End .title -->
                    </div><!-- End .heading-left -->

                    <div class="heading-right">
                        <Link href="/categories?sort=popularity" class="title-link animate-on-scroll" data-animation="fade-left" data-delay="100">View All Recommendadion <i class="icon-long-arrow-right"></i></Link>
                    </div><!-- End .heading-right -->
                </div><!-- End .heading -->

                <div class="products">
                    <div class="row justify-content-start">
                        <transition-group name="product-list" tag="div" class="row justify-content-start">
                            <div v-for="(product, index) in recommendedProducts" :key="product.id" class="col-6 col-md-4 col-lg-3" :style="{ transitionDelay: `${index * 100}ms` }">
                                <div class="product product-2 animate-on-scroll animate-hover-lift" data-animation="fade-up" data-delay="0">
                                    <figure class="product-media">
                                        <span v-if="product.is_new" class="product-label label-circle label-new">New</span>
                                        <span v-if="product.previous_price" class="product-label label-circle label-sale">Sale</span>
                                        <Link :href="`/products/${product.slug}`">
                                            <img :src="product.images[0]?.image_url" :alt="product.name" class="product-image">
                                        </Link>

                                        <div class="product-action-vertical">
                                        <a
                                                href="#"
                                                @click.prevent="handleWishlistToggle(product.id)"
                                                class="btn-product-icon btn-wishlist"
                                                :class="{ 'active': isInWishlist(product.id) }"
                                                :title="isInWishlist(product.id) ? 'Remove from wishlist' : 'Add to wishlist'"
                                            ></a>
                                        </div><!-- End .product-action -->

                                        <div class="product-action">
                                            <a href="#" class="btn-product btn-cart" @click.prevent="handleAddToCart(product.id)" title="Add to cart"><span>add to cart</span></a>
                                            <Link :href="`/products/${product.slug}`" class="btn-product btn-quickview" title="Quick view"><span>quick view</span></Link>
                                        </div><!-- End .product-action -->
                                    </figure><!-- End .product-media -->

                                    <div class="product-body">
                                        <div class="product-cat">
                                            <Link :href="`/categories/${product.category.slug}`">{{ product.category.name }}</Link>
                                        </div><!-- End .product-cat -->
                                        <h3 class="product-title">
                                            <Link :href="`/products/${product.slug}`">{{ product.name }} - <span v-if="product.answers && product.answers.length > 0">
                                                <span v-for="(answer, index) in product.answers.slice(0, 2)" :key="answer.id">
                                                    {{ answer.answer }}
                                                    <span v-if="index < 1 && product.answers.length > 1">, </span>
                                                </span>
                                            </span></Link>
                                        </h3><!-- End .product-title -->
                                        <div v-if="product.previous_price" class="product-price">
                                            <span class="new-price">&#8358;{{ parseFloat(product.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
                                            <span class="old-price">Was &#8358;{{ parseFloat(product.previous_price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
                                        </div>

                                        <div v-if="!product.previous_price" class="product-price" style="color: #39f !important;">
                                            &#8358;{{ parseFloat(product.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
</div><!-- End .product-price -->
                                        <div class="ratings-container">
                                            <div class="ratings">
                                                <div class="ratings-val" :style="{ width: getRatingWidth(product) + '%' }"></div>
                                            </div><!-- End .ratings -->
                                            <span class="ratings-text">( {{ product.reviews?.length || 0 }} Review{{ (product.reviews?.length || 0) !== 1 ? 's' : '' }} )</span>
                                        </div><!-- End .rating-container -->
                                    </div><!-- End .product-body -->
                                </div><!-- End .product -->
                            </div><!-- End .col-sm-6 col-md-4 col-lg-3 -->
                        </transition-group><!-- End .row -->
                    </div><!-- End .row -->
                </div><!-- End .products -->
            </div><!-- End .container -->

            <div class="mb-4"></div><!-- End .mb-4 -->

            <div class="container">
                <hr class="mb-0">
            </div><!-- End .container -->
        </div>
    </transition>
</main>

</Skeleton>
</template>


<style scoped>
.brand img {
    width: 76px !important;
}
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

/* Single-column product grid only on screens below 767px */
@media (max-width: 767px) {
    .products {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
    }
    .products .row {
        display: contents;
    }
    .products [class*="col-"] {
        width: 100% !important;
        max-width: 100% !important;
        flex: 0 0 100% !important;
    }
}


.intro-slide {
    padding: 60px 0;
    background-color: #f4f4f4; /* Optional: add a background color */
}

.intro-image {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.intro-image img {
    width: 100%;
    height: auto;
    object-fit: cover;
    max-height: 500px;
}

.intro-content {
    padding: 20px;
}

.intro-subtitle {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 15px;
    text-transform: uppercase;
}

.intro-title {
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 20px;
    line-height: 1.2;
}

.intro-price {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 30px;
}

.intro-old-price {
    text-decoration: line-through;
    color: #999;
    font-size: 20px;
    margin-right: 10px;
}

/* Responsive adjustments */
@media (max-width: 767px) {
    .intro-slide {
        padding: 30px 0;
    }
    
    .intro-image {
        margin-bottom: 30px;
    }
    
    .intro-title {
        font-size: 28px;
    }
    
    .intro-price {
        font-size: 24px;
    }

    /* Ellipsis for long titles on mobile */
    .intro-title {
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
    }

    /* Full-width Shop More button on mobile */
    .intro-content .btn {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Mobile-friendly heights for slider container and stage */
    .intro-slider-container,
    :deep(.owl-stage-outer) {
        height: 500px !important;
        min-height: 500px !important;
        max-height: 500px !important;
    }

    /* Intro image more visible on mobile */
    .intro-image img {
        max-height: 400px;
        width: 100%;
        object-fit: cover;
    }
}


.brand-marquee-container {
  overflow: hidden;
  position: relative;
  width: 100%;
}

.brand-marquee {
  display: flex;
  width: 100%;
}

.brand-marquee-content {
  display: flex;
  animation: scroll 20s linear infinite;
  gap: 30px;
}

.brand-marquee-content:hover {
  animation-play-state: paused;
}

.brand {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: transform 0.3s ease;
}

.brand:hover {
  transform: scale(1.1);
}

.brand img {
  width: 76px !important;
  height: auto;
  display: block;
}

@keyframes scroll {
  0% {
    transform: translateX(0);
  }
  100% {
    transform: translateX(-50%);
  }
}

/* Responsive adjustments */
@media (max-width: 767px) {
  .brand-marquee-content {
    animation-duration: 15s;
    gap: 20px;
  }
  
  .brand img {
    width: 60px !important;
  }
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

/* Page Transition Animations */
.page-enter-active,
.page-leave-active {
    transition: all 0.4s ease;
}

.page-enter-from {
    opacity: 0;
    transform: translateY(20px);
}

.page-leave-to {
    opacity: 0;
    transform: translateY(-20px);
}

/* Stagger Animation for Lists */
.stagger-enter-active,
.stagger-leave-active {
    transition: all 0.5s ease;
}

.stagger-enter-from,
.stagger-leave-to {
    opacity: 0;
    transform: translateY(30px);
}

.stagger-move {
    transition: transform 0.5s ease;
}

/* Product List Animation */
.product-list-enter-active,
.product-list-leave-active {
    transition: all 0.5s ease;
}

.product-list-enter-from,
.product-list-leave-to {
    opacity: 0;
    transform: translateY(30px) scale(0.95);
}

.product-list-move {
    transition: transform 0.5s ease;
}

/* Scroll-triggered Animations */
.animate-on-scroll {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.animate-on-scroll.animated {
    opacity: 1;
    transform: translateY(0);
}

.animate-on-scroll[data-animation="fade-up"] {
    transform: translateY(40px);
}

.animate-on-scroll[data-animation="fade-down"] {
    transform: translateY(-40px);
}

.animate-on-scroll[data-animation="fade-left"] {
    transform: translateX(40px);
}

.animate-on-scroll[data-animation="fade-right"] {
    transform: translateX(-40px);
}

.animate-on-scroll[data-animation="zoom-in"] {
    transform: scale(0.9);
}

.animate-on-scroll[data-animation="zoom-out"] {
    transform: scale(1.1);
}

.animate-on-scroll.animated[data-animation="fade-up"],
.animate-on-scroll.animated[data-animation="fade-down"],
.animate-on-scroll.animated[data-animation="fade-left"],
.animate-on-scroll.animated[data-animation="fade-right"],
.animate-on-scroll.animated[data-animation="zoom-in"],
.animate-on-scroll.animated[data-animation="zoom-out"] {
    transform: translate(0) scale(1);
}

/* Hover Lift Animation */
.animate-hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.animate-hover-lift:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
}

/* Hover Scale Animation */
.animate-hover-scale {
    transition: transform 0.3s ease;
}

.animate-hover-scale:hover {
    transform: scale(1.05);
}

/* Category Block Hover */
.cat-block.animate-hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.cat-block.animate-hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

.cat-block.animate-hover-lift figure {
    transition: transform 0.3s ease;
}

.cat-block.animate-hover-lift:hover figure {
    transform: scale(1.05);
}

/* Product Card Enhancement */
.product.product-2.animate-hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product.product-2.animate-hover-lift:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
}

.product.product-2.animate-hover-lift .product-image {
    transition: transform 0.5s ease;
}

.product.product-2.animate-hover-lift:hover .product-image {
    transform: scale(1.05);
}

/* Intro Slide Animations */
.intro-slide {
    animation: slideUpFade 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.intro-image {
    animation: zoomIn 1s cubic-bezier(0.25, 0.46, 0.45, 0.94) 0.2s both;
}

.intro-content {
    animation: slideRightFade 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) 0.4s both;
}

.intro-subtitle {
    animation: fadeIn 0.6s ease 0.5s both;
}

.intro-title {
    animation: slideUpFade 0.6s ease 0.6s both;
}

.intro-price {
    animation: slideUpFade 0.6s ease 0.7s both;
}

.intro-content .btn {
    animation: slideUpFade 0.6s ease 0.8s both;
}

/* Keyframe Animations */
@keyframes slideUpFade {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideRightFade {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideLeftFade {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes zoomIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

/* Tab Content Animation */
.tab-pane {
    animation: fadeIn 0.4s ease;
}

/* Loading Skeleton Pulse Animation */
.skeleton-loader {
    animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

/* Reduced Motion Support */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
    
    .brand-marquee-content {
        animation: none;
    }
    
    .animate-on-scroll {
        opacity: 1;
        transform: none;
        transition: none;
    }
}

/* Smooth Scroll Behavior */
html {
    scroll-behavior: smooth;
}

/* Focus Visible for Accessibility */
:focus-visible {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

/* Button Ripple Effect */
.btn {
    position: relative;
    overflow: hidden;
}

.btn::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.4s ease, height 0.4s ease;
    pointer-events: none;
}

.btn:active::after {
    width: 300px;
    height: 300px;
    transition: 0s;
}
</style>











