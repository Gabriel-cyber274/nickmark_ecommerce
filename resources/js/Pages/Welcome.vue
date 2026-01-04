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

</script>

<template>
    <Head title="Welcome" />

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
            <div class="intro-slider-container mb-5">
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
                <div class="col-5 col-md-6">
                    <div class="intro-image">
                        <img :src="product.images[0]?.image_url" :alt="product.name" class="img-fluid">
                    </div>
                </div>
                
                <!-- Right side - Product Content -->
                <div class="col-7 col-md-6">
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
                                ₦{{ parseFloat(product.previous_price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                            </sup>
                            <span class="text-third">
                                ₦{{ parseFloat(product.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                            </span>
                        </div>

                        <div v-if="!product.previous_price" class="intro-price">
                            <sup>Today:</sup>
                            <span class="text-primary">
                                ₦{{ parseFloat(product.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                            </span>
                        </div>

                        <Link :href="'/product/'+product.id" class="btn btn-primary btn-round">
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

            <div class="container">
                <h2 class="title text-center mb-4">Explore Popular Categories</h2><!-- End .title text-center -->
                
                <div class="cat-blocks-container">
                    <div class="row">
                        <div v-for="category in popularCategories" class="col-6 col-sm-4 col-lg-2"  :key="category.id">
                            <Link href="/categories" class="cat-block">
                                <figure>
                                    <span>
                                        <img :src="category.image" alt="Category image">
                                    </span>
                                </figure>

                                <h3 class="cat-block-title">{{ category.name }}</h3><!-- End .cat-block-title -->
                            </Link>
                        </div><!-- End .col-sm-4 col-lg-2 -->

                    </div><!-- End .row -->
                </div><!-- End .cat-blocks-container -->
            </div><!-- End .container -->


            <div class="mb-3"></div><!-- End .mb-5 -->

            <div class="container new-arrivals">
                    <div class="heading heading-flex mb-3">
                        <div class="heading-left">
                            <h2 class="title">New Arrivals</h2><!-- End .title -->
                        </div><!-- End .heading-left -->

                        <div class="heading-right">
                            <ul class="nav nav-pills nav-border-anim justify-content-center" role="tablist">
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
                            <div class="owl-carousel owl-full carousel-equal-height carousel-with-shadow" data-toggle="owl" 
                                data-owl-options='{
                                    "nav": true, 
                                    "dots": true,
                                    "margin": 20,
                                    "loop": false,
                                    "responsive": {
                                        "0": {
                                            "items":2
                                        },
                                        "480": {
                                            "items":2
                                        },
                                        "768": {
                                            "items":3
                                        },
                                        "992": {
                                            "items":4
                                        },
                                        "1200": {
                                            "items":5
                                        }
                                    }
                                }'>
                                <!-- Directly use the "all" products -->
                                <div v-for="product in allProducts" :key="product.id" class="product product-2">
                                    <figure class="product-media">
                                        <span v-if="product.is_new" class="product-label label-circle label-new">New</span>
                                        <span v-if="product.previous_price" class="product-label label-circle label-sale">Sale</span>
                                        <Link :href="'/product/' + product.id">
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
                                            <Link :href="'/product/' + product.id" class="btn-product btn-quickview" title="Quick view"><span>quick view</span></Link>
                                        </div><!-- End .product-action -->
                                    </figure><!-- End .product-media -->

                                    <div class="product-body">
                                        <div class="product-cat">
                                            <Link :href="'/categories/' + product.category_id">{{ product.category.name }}</Link>
                                        </div><!-- End .product-cat -->
                                        <h3 class="product-title">
                                            <Link :href="'/product/' + product.id">{{ product.name }} - <span v-if="product.answers && product.answers.length > 0">
                                                <span v-for="(answer, index) in product.answers.slice(0, 2)" :key="answer.id">
                                                    {{ answer.answer }}
                                                    <span v-if="index < 1 && product.answers.length > 1">, </span>
                                                </span>
                                            </span></Link>
                                        </h3><!-- End .product-title -->
                                        <div v-if="product.previous_price" class="product-price">
                                            <span class="new-price">₦{{ parseFloat(product.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
                                            <span class="old-price">Was ₦{{ parseFloat(product.previous_price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
                                        </div>

                                        <div v-if="!product.previous_price" class="product-price" style="color: #39f !important;">
                                            ₦{{ parseFloat(product.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                        </div><!-- End .product-price -->
                                        <div class="ratings-container">
                                            <div class="ratings">
                                                <div class="ratings-val" :style="{ width: '80%' }"></div>
                                            </div><!-- End .ratings -->
                                            <span class="ratings-text">( 4 Reviews )</span>
                                        </div><!-- End .rating-container -->
                                    </div><!-- End .product-body -->
                                </div><!-- End .product -->
                            </div><!-- End .owl-carousel -->
                        </div><!-- .End .tab-pane -->

                        <!-- Category-specific Tabs using computed property -->
                        <div v-for="item in filteredCategories" 
                            :key="item.categoryName" 
                            class="tab-pane p-0 fade" 
                            :id="'new-' + slugify(item.categoryName) + '-tab'" 
                            :aria-labelledby="'new-' + slugify(item.categoryName) + '-link'">
                            <div class="owl-carousel owl-full carousel-equal-height carousel-with-shadow" data-toggle="owl" 
                                data-owl-options='{
                                    "nav": true, 
                                    "dots": true,
                                    "margin": 20,
                                    "loop": false,
                                    "responsive": {
                                        "0": {
                                            "items":2
                                        },
                                        "480": {
                                            "items":2
                                        },
                                        "768": {
                                            "items":3
                                        },
                                        "992": {
                                            "items":4
                                        },
                                        "1200": {
                                            "items":5
                                        }
                                    }
                                }'>
                                <div v-for="product in item.categoryProducts" :key="product.id" class="product product-2">
                                    <figure class="product-media">
                                        <span v-if="product.is_new" class="product-label label-circle label-new">New</span>
                                        <span v-if="product.previous_price" class="product-label label-circle label-sale">Sale</span>
                                        <Link :href="'/product/' + product.id">
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
                                            <Link :href="'/product/' + product.id" class="btn-product btn-quickview" title="Quick view"><span>quick view</span></Link>
                                        </div><!-- End .product-action -->
                                    </figure><!-- End .product-media -->

                                    <div class="product-body">
                                        <div class="product-cat">
                                            <Link :href="'/categories/' + product.category_id">{{ product.category.name }}</Link>
                                        </div><!-- End .product-cat -->
                                        <h3 class="product-title">
                                            <Link :href="'/product/' + product.id">{{ product.name }} - <span v-if="product.answers && product.answers.length > 0">
                                                <span v-for="(answer, index) in product.answers.slice(0, 2)" :key="answer.id">
                                                    {{ answer.answer }}
                                                    <span v-if="index < 1 && product.answers.length > 1">, </span>
                                                </span>
                                            </span></Link>
                                        </h3><!-- End .product-title -->

                                        <div v-if="product.previous_price" class="product-price">
                                            <span class="new-price">₦{{ parseFloat(product.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
                                            <span class="old-price">Was ₦{{ parseFloat(product.previous_price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
                                        </div>

                                        <div v-if="!product.previous_price" class="product-price" style="color: #39f !important;">
                                            ₦{{ parseFloat(product.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                        </div><!-- End .product-price -->
                                        
                                        <div class="ratings-container">
                                            <div class="ratings">
                                                <div class="ratings-val" :style="{ width: '80%' }"></div>
                                            </div><!-- End .ratings -->
                                            <span class="ratings-text">( 4 Reviews )</span>
                                        </div><!-- End .rating-container -->
                                    </div><!-- End .product-body -->
                                </div><!-- End .product -->
                            </div><!-- End .owl-carousel -->
                        </div><!-- .End .tab-pane -->
                    </div><!-- End .tab-content -->
                </div><!-- End .container -->

            <div class="mb-6"></div><!-- End .mb-6 -->


<div class="container">
  <hr class="mb-0">
  <div class="brand-marquee-container mt-5 mb-5">
    <div class="brand-marquee">
      <div class="brand-marquee-content">
        <Link v-for="brand in brands" :key="brand.id" :href="'/categories?search='+brand.name" class="brand">
          <img :src="brand.src" alt="Brand Name">
        </Link>
        <!-- Duplicate for seamless loop -->
        <Link v-for="brand in brands" :key="`${brand.id}-duplicate`" :href="'/categories?search='+brand.name" class="brand">
          <img :src="brand.src" alt="Brand Name">
        </Link>
      </div>
    </div>
  </div>
</div>

            <div class="mb-5"></div><!-- End .mb-5 -->

            <div class="container for-you">
                <div class="heading heading-flex mb-3">
                    <div class="heading-left">
                        <h2 class="title">Recommendation For You</h2><!-- End .title -->
                    </div><!-- End .heading-left -->

                   <div class="heading-right">
                        <Link href="/categories?sort=popularity" class="title-link">View All Recommendadion <i class="icon-long-arrow-right"></i></Link>
                   </div><!-- End .heading-right -->
                </div><!-- End .heading -->

                <div class="products">
                    <div class="row justify-content-start">
                        <div v-for="product in recommendedProducts" :key="product.id" class="col-6 col-md-4 col-lg-3">
                                                          
                                <div class="product product-2">
                                    <figure class="product-media">
                                        <span v-if="product.is_new" class="product-label label-circle label-new">New</span>
                                        <span v-if="product.previous_price" class="product-label label-circle label-sale">Sale</span>
                                        <Link :href="'/product/' + product.id">
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
                                            <Link :href="'/product/' + product.id" class="btn-product btn-quickview" title="Quick view"><span>quick view</span></Link>
                                        </div><!-- End .product-action -->
                                    </figure><!-- End .product-media -->

                                    <div class="product-body">
                                        <div class="product-cat">
                                            <Link :href="'/categories/' + product.category_id">{{ product.category.name }}</Link>
                                        </div><!-- End .product-cat -->
                                        <h3 class="product-title">
                                            <Link :href="'/product/' + product.id">{{ product.name }} - <span v-if="product.answers && product.answers.length > 0">
                                                <span v-for="(answer, index) in product.answers.slice(0, 2)" :key="answer.id">
                                                    {{ answer.answer }}
                                                    <span v-if="index < 1 && product.answers.length > 1">, </span>
                                                </span>
                                            </span></Link>
                                        </h3><!-- End .product-title -->
                                        <div v-if="product.previous_price" class="product-price">
                                            <span class="new-price">₦{{ parseFloat(product.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
                                            <span class="old-price">Was ₦{{ parseFloat(product.previous_price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
                                        </div>

                                        <div v-if="!product.previous_price" class="product-price" style="color: #39f !important;">
                                            ₦{{ parseFloat(product.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                        </div><!-- End .product-price -->
                                        <div class="ratings-container">
                                            <div class="ratings">
                                                <div class="ratings-val" :style="{ width: '80%' }"></div>
                                            </div><!-- End .ratings -->
                                            <span class="ratings-text">( 4 Reviews )</span>
                                        </div><!-- End .rating-container -->
                                    </div><!-- End .product-body -->
                                </div><!-- End .product -->
                        </div><!-- End .col-sm-6 col-md-4 col-lg-3 -->

                    </div><!-- End .row -->
                </div><!-- End .products -->
            </div><!-- End .container -->

            <div class="mb-4"></div><!-- End .mb-4 -->

            <div class="container">
                <hr class="mb-0">
            </div><!-- End .container -->

            <div class="icon-boxes-container bg-transparent">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-6 col-lg-3">
                            <div class="icon-box icon-box-side">
                                <span class="icon-box-icon text-dark">
                                    <i class="icon-rocket"></i>
                                </span>
                                <div class="icon-box-content">
                                    <h3 class="icon-box-title">Free Shipping</h3><!-- End .icon-box-title -->
                                    <p>Orders $50 or more</p>
                                </div><!-- End .icon-box-content -->
                            </div><!-- End .icon-box -->
                        </div><!-- End .col-sm-6 col-lg-3 -->

                        <div class="col-sm-6 col-lg-3">
                            <div class="icon-box icon-box-side">
                                <span class="icon-box-icon text-dark">
                                    <i class="icon-rotate-left"></i>
                                </span>

                                <div class="icon-box-content">
                                    <h3 class="icon-box-title">Free Returns</h3><!-- End .icon-box-title -->
                                    <p>Within 30 days</p>
                                </div><!-- End .icon-box-content -->
                            </div><!-- End .icon-box -->
                        </div><!-- End .col-sm-6 col-lg-3 -->

                        <div class="col-sm-6 col-lg-3">
                            <div class="icon-box icon-box-side">
                                <span class="icon-box-icon text-dark">
                                    <i class="icon-info-circle"></i>
                                </span>

                                <div class="icon-box-content">
                                    <h3 class="icon-box-title">Get 20% Off 1 Item</h3><!-- End .icon-box-title -->
                                    <p>when you sign up</p>
                                </div><!-- End .icon-box-content -->
                            </div><!-- End .icon-box -->
                        </div><!-- End .col-sm-6 col-lg-3 -->

                        <div class="col-sm-6 col-lg-3">
                            <div class="icon-box icon-box-side">
                                <span class="icon-box-icon text-dark">
                                    <i class="icon-life-ring"></i>
                                </span>

                                <div class="icon-box-content">
                                    <h3 class="icon-box-title">We Support</h3><!-- End .icon-box-title -->
                                    <p>24/7 amazing services</p>
                                </div><!-- End .icon-box-content -->
                            </div><!-- End .icon-box -->
                        </div><!-- End .col-sm-6 col-lg-3 -->
                    </div><!-- End .row -->
                </div><!-- End .container -->
            </div><!-- End .icon-boxes-container -->
        </main><!-- End .main -->

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
</style>
