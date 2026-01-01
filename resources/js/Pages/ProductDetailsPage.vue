<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, nextTick, onUnmounted, ref, reactive } from 'vue';
import Skeleton from './Skeleton.vue'

let prop = defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    productId: {
        type: Number
    },
    recommendProducts: {
        type: Array
    },
    product: {
        type: Object  
    }
});

// State for current main image of the main product
const currentMainImage = ref(null);

// Track current image for each recommended product
const recommendedProductImages = reactive({});

// Initialize recommended products images
const initRecommendedProducts = () => {
    if (prop.recommendProducts && prop.recommendProducts.length > 0) {
        prop.recommendProducts.forEach(product => {
            if (product.images && product.images.length > 0) {
                recommendedProductImages[product.id] = {
                    currentImage: product.images[0].image_url,
                    images: product.images
                };
            }
        });
    }
};

// Function to change main image of recommended product
const changeRecommendedImage = (productId, imageUrl, event) => {
    event.preventDefault();
    event.stopPropagation();
    
    if (recommendedProductImages[productId]) {
        // Update the current image for this product
        recommendedProductImages[productId].currentImage = imageUrl;
        
        // Update the main product image in the carousel item
        const productElement = event.target.closest('.product');
        if (productElement) {
            const mainImage = productElement.querySelector('.product-image');
            if (mainImage) {
                mainImage.src = imageUrl;
            }
        }
        
        // Remove active class from all thumbnails for this product
        const thumbnails = event.target.closest('.product').querySelectorAll('.product-nav-thumbs a');
        thumbnails.forEach(thumb => thumb.classList.remove('active'));
        
        // Add active class to clicked thumbnail
        event.target.closest('a').classList.add('active');
    }
};

// Initialize carousels after component mounts
onMounted(() => {
    // Set initial main image for main product
    if (prop.product?.images?.length > 0) {
        currentMainImage.value = prop.product.images[0].image_url;
    }
    
    // Initialize recommended products images
    initRecommendedProducts();

    nextTick(() => {
        setTimeout(() => {
            // Destroy any existing carousels first
            if (window.$ && window.$.fn.owlCarousel) {
                $('.owl-carousel').each(function() {
                    if ($(this).data('owl.carousel')) {
                        $(this).trigger('destroy.owl.carousel');
                        $(this).removeClass('owl-loaded owl-drag');
                    }
                });

                // Initialize "You May Also Like" carousel
                $('.carousel-equal-height').owlCarousel({
                    nav: false,
                    dots: true,
                    margin: 20,
                    loop: false,
                    responsive: {
                        0: {
                            items: 1
                        },
                        480: {
                            items: 2
                        },
                        768: {
                            items: 3
                        },
                        992: {
                            items: 4
                        },
                        1200: {
                            items: 4,
                            nav: true,
                            dots: false
                        }
                    }
                });
            }
        }, 200);
    });
});

// Cleanup on component unmount
onUnmounted(() => {
    if (window.$ && window.$.fn.owlCarousel) {
        $('.owl-carousel').each(function() {
            if ($(this).data('owl.carousel')) {
                $(this).trigger('destroy.owl.carousel');
                $(this).removeClass('owl-loaded owl-drag');
            }
        });
    }
});

// Function to change main product image
const changeMainImage = (imageUrl, event) => {
    event.preventDefault();
    currentMainImage.value = imageUrl;
    
    // Remove active class from all gallery items
    const galleryItems = document.querySelectorAll('.product-gallery-item');
    galleryItems.forEach(item => item.classList.remove('active'));
    
    // Add active class to clicked item
    event.currentTarget.classList.add('active');
};

// Function to format answer based on question type
const formatAnswer = (answer) => {
    if (!answer.answer) return '—';
    
    // Check if question exists and is boolean type
    if (answer.question && answer.question.type === 'boolean') {
        return answer.answer === '1' || answer.answer === 1 || answer.answer === true ? 'Yes' : 'No';
    }
    
    return answer.answer;
};

onMounted(() => {
    console.log('product', prop.product);
    console.log('recommendproduct', prop.recommendProducts);
});
</script>

<template>
<Skeleton page='product'>
    <main class="main mt-3">
        <div class="page-content">
            <div class="container">
                <div class="product-details-top">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="product-gallery product-gallery-vertical">
                                <div class="row">
                                    <figure class="product-main-image">
                                        <img id="product-zoom" :src="currentMainImage" :data-zoom-image="currentMainImage" alt="product image">

                                        <a href="#" id="btn-product-gallery" class="btn-product-gallery">
                                            <i class="icon-arrows"></i>
                                        </a>
                                    </figure><!-- End .product-main-image -->

                                    <div id="product-zoom-gallery" class="product-image-gallery">
                                        <a v-for="(item, index) in product.images" 
                                           :key="item.id" 
                                           :class="['product-gallery-item', { active: index === 0 }]" 
                                           href="#" 
                                           :data-image="item.image_url" 
                                           :data-zoom-image="item.image_url"
                                           @click="changeMainImage(item.image_url, $event)">
                                            <img :src="item.image_url" alt="product side">
                                        </a>
                                    </div><!-- End .product-image-gallery -->
                                </div><!-- End .row -->
                            </div><!-- End .product-gallery -->
                        </div><!-- End .col-md-6 -->

                        <div class="col-md-6">
                            <div class="product-details">
                                <h1 class="product-title">{{ product.name }}</h1><!-- End .product-title -->

                                <div class="ratings-container">
                                    <div class="ratings">
                                        <div class="ratings-val" style="width: 80%;"></div><!-- End .ratings-val -->
                                    </div><!-- End .ratings -->
                                    <a class="ratings-text" href="#product-review-link" id="review-link">( 2 Reviews )</a>
                                </div><!-- End .rating-container -->

                                <div class="product-price">
                                    ₦{{ parseFloat(product.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                </div><!-- End .product-price -->
                                <div class="product-content">
                                    <p>TYPE: {{ product.is_new? 'NEW': 'USED' }} </p>
                                </div><!-- End .product-content -->


                                <div class="details-filter-row details-row-size">
                                    <label for="qty">Qty:</label>
                                    <div class="product-details-quantity">
                                        <input type="number" id="qty" class="form-control" value="1" min="1" max="10" step="1" data-decimals="0" required>
                                    </div><!-- End .product-details-quantity -->
                                </div><!-- End .details-filter-row -->

                                <div class="product-details-action">
                                    <a href="#" class="btn-product btn-cart"><span>add to cart</span></a>

                                    <div class="details-action-wrapper">
                                        <a href="#" class="btn-product btn-wishlist" title="Wishlist"><span>Add to Wishlist</span></a>
                                    </div><!-- End .details-action-wrapper -->
                                </div><!-- End .product-details-action -->

                                <div class="product-details-footer">
                                    <div class="product-cat">
                                        <span>Category:</span>
                                        <a href="#">{{ product.category.name }}</a>
                                    </div><!-- End .product-cat -->

                                </div><!-- End .product-details-footer -->
                            </div><!-- End .product-details -->
                        </div><!-- End .col-md-6 -->
                    </div><!-- End .row -->
                </div><!-- End .product-details-top -->

                <div class="product-details-tab">
                    <ul class="nav nav-pills justify-content-center" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="product-desc-link" data-toggle="tab" href="#product-desc-tab" role="tab" aria-controls="product-desc-tab" aria-selected="true">Description</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="product-info-link" data-toggle="tab" href="#product-info-tab" role="tab" aria-controls="product-info-tab" aria-selected="false">Additional information</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="product-shipping-link" data-toggle="tab" href="#product-shipping-tab" role="tab" aria-controls="product-shipping-tab" aria-selected="false">Shipping & Returns</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="product-review-link" data-toggle="tab" href="#product-review-tab" role="tab" aria-controls="product-review-tab" aria-selected="false">Reviews (2)</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="product-desc-tab" role="tabpanel" aria-labelledby="product-desc-link">
                            <div class="product-desc-content">
                                <h3>Product Information</h3>
                                <p>{{ product.description }}</p>
                            </div><!-- End .product-desc-content -->
                        </div><!-- .End .tab-pane -->
                        <div class="tab-pane fade" id="product-info-tab" role="tabpanel" aria-labelledby="product-info-link">
                            <div class="product-desc-content">
                                <h3>Product Specifications</h3>
                                <div v-if="product.answers && product.answers.length > 0" class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <tbody>
                                            <tr v-for="answer in product.answers" :key="answer.id">
                                                <td style="width: 40%; font-weight: 600; background-color: #f8f9fa;" class="px-3">
                                                    {{ answer.question?.question || 'Question' }}
                                                </td>
                                                <td style="width: 60%;" class="px-3">
                                                    {{ formatAnswer(answer) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div v-else>
                                    <p>No additional specifications available for this product.</p>
                                </div>
                            </div><!-- End .product-desc-content -->
                        </div><!-- .End .tab-pane -->
                        <div class="tab-pane fade" id="product-shipping-tab" role="tabpanel" aria-labelledby="product-shipping-link">
                            <div class="product-desc-content">
                                <h3>Delivery & returns</h3>
                                <p>We deliver to over 100 countries around the world. For full details of the delivery options we offer, please view our <a href="#">Delivery information</a><br>
                                We hope you'll love every purchase, but if you ever need to return an item you can do so within a month of receipt. For full details of how to make a return, please view our <a href="#">Returns information</a></p>
                            </div><!-- End .product-desc-content -->
                        </div><!-- .End .tab-pane -->
                        <div class="tab-pane fade" id="product-review-tab" role="tabpanel" aria-labelledby="product-review-link">
                            <div class="reviews">
                                <h3>Reviews (2)</h3>
                                <div class="review">
                                    <div class="row no-gutters">
                                        <div class="col-auto">
                                            <h4><a href="#">Samanta J.</a></h4>
                                            <div class="ratings-container">
                                                <div class="ratings">
                                                    <div class="ratings-val" style="width: 80%;"></div><!-- End .ratings-val -->
                                                </div><!-- End .ratings -->
                                            </div><!-- End .rating-container -->
                                            <span class="review-date">6 days ago</span>
                                        </div><!-- End .col -->
                                        <div class="col">
                                            <h4>Good, perfect size</h4>

                                            <div class="review-content">
                                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ducimus cum dolores assumenda asperiores facilis porro reprehenderit animi culpa atque blanditiis commodi perspiciatis doloremque, possimus, explicabo, autem fugit beatae quae voluptas!</p>
                                            </div><!-- End .review-content -->
                                        </div><!-- End .col-auto -->
                                    </div><!-- End .row -->
                                </div><!-- End .review -->

                                <div class="review">
                                    <div class="row no-gutters">
                                        <div class="col-auto">
                                            <h4><a href="#">John Doe</a></h4>
                                            <div class="ratings-container">
                                                <div class="ratings">
                                                    <div class="ratings-val" style="width: 100%;"></div><!-- End .ratings-val -->
                                                </div><!-- End .ratings -->
                                            </div><!-- End .rating-container -->
                                            <span class="review-date">5 days ago</span>
                                        </div><!-- End .col -->
                                        <div class="col">
                                            <h4>Very good</h4>

                                            <div class="review-content">
                                                <p>Sed, molestias, tempore? Ex dolor esse iure hic veniam laborum blanditiis laudantium iste amet. Cum non voluptate eos enim, ab cumque nam, modi, quas iure illum repellendus, blanditiis perspiciatis beatae!</p>
                                            </div><!-- End .review-content -->
                                        </div><!-- End .col-auto -->
                                    </div><!-- End .row -->
                                </div><!-- End .review -->
                            </div><!-- End .reviews -->
                        </div><!-- .End .tab-pane -->
                    </div><!-- End .tab-content -->
                </div><!-- End .product-details-tab -->

                <h2 v-if="recommendProducts.length > 0" class="title text-center mb-4">You May Also Like</h2><!-- End .title text-center -->

                <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow" data-toggle="owl" 
                    data-owl-options='{
                        "nav": false, 
                        "dots": true,
                        "margin": 20,
                        "loop": false,
                        "responsive": {
                            "0": {
                                "items":1
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
                                "items":4,
                                "nav": true,
                                "dots": false
                            }
                        }
                    }'>
                    
                    <!-- Loop through recommend products -->
                    <div v-for="recProduct in recommendProducts" :key="recProduct.id" class="product product-7 text-center">
                        <figure class="product-media">
                            <span v-if="recProduct.is_new" class="product-label label-new">New</span>
                            <Link :href="`/product/${recProduct.id}`">
                                <img :src="recommendedProductImages[recProduct.id]?.currentImage || recProduct.images[0]?.image_url || '/assets/images/products/product-4.jpg'" 
                                     alt="Product image" 
                                     class="product-image">
                            </Link>

                            <div class="product-action-vertical">
                                <a href="#" class="btn-product-icon btn-wishlist btn-expandable"><span>add to wishlist</span></a>
                                <Link :href="`/product/${recProduct.id}`" class="btn-product-icon btn-quickview" title="Quick view"><span>Quick view</span></Link>
                            </div><!-- End .product-action-vertical -->

                            <div class="product-action">
                                <a href="#" class="btn-product btn-cart"><span>add to cart</span></a>
                            </div><!-- End .product-action -->
                        </figure><!-- End .product-media -->

                        <div class="product-body">
                            <div class="product-cat">
                                <a href="#">{{ recProduct.category?.name || 'Category' }}</a>
                            </div><!-- End .product-cat -->
                            <h3 class="product-title">
                                <Link :href="`/product/${recProduct.id}`">{{ recProduct.name }}</Link>
                            </h3><!-- End .product-title -->
                            <div class="product-price">
                                ₦{{ parseFloat(recProduct.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                            </div><!-- End .product-price -->
                            <div class="ratings-container">
                                <div class="ratings">
                                    <div class="ratings-val" style="width: 80%;"></div><!-- End .ratings-val -->
                                </div><!-- End .ratings -->
                                <span class="ratings-text">( 0 Reviews )</span>
                            </div><!-- End .rating-container -->
                            <div class="product-nav product-nav-thumbs">
                                <a v-for="(image, index) in recProduct.images" 
                                   :key="image.id" 
                                   href="#" 
                                   :class="{ active: index === 0 }"
                                   @click="changeRecommendedImage(recProduct.id, image.image_url, $event)">
                                    <img :src="image.image_url" alt="product thumbnail">
                                </a>
                            </div><!-- End .product-nav -->
                        </div><!-- End .product-body -->
                    </div><!-- End .product -->

                </div><!-- End .owl-carousel -->
            </div><!-- End .container -->
        </div><!-- End .page-content -->
    </main><!-- End .main -->
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


</style>