<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { onMounted, nextTick, onUnmounted, ref, reactive } from 'vue';
import Skeleton from './Skeleton.vue';

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
    },
    auth: {
        type: Object  
    }
});

// State for current main image of the main product
const currentMainImage = ref(null);

// Track current image for each recommended product
const recommendedProductImages = reactive({});

// Review form state
const showReviewForm = ref(false);
const reviewForm = reactive({
    name: '',
    email: '',
    rate: 5,
    comment: ''
});

const reviewFormErrors = reactive({
    name: '',
    email: '',
    comment: ''
});

// Function to toggle review form
const toggleReviewForm = () => {
    showReviewForm.value = !showReviewForm.value;
    if (!showReviewForm.value) {
        resetReviewForm();
    }
};

// Function to reset review form
const resetReviewForm = () => {
    reviewForm.name = '';
    reviewForm.email = '';
    reviewForm.rate = 5;
    reviewForm.comment = '';
    reviewFormErrors.name = '';
    reviewFormErrors.email = '';
    reviewFormErrors.comment = '';
};

// Function to validate email
const isValidEmail = (email) => {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
};

// Function to submit review
const submitReview = () => {
    // Reset errors
    reviewFormErrors.name = '';
    reviewFormErrors.email = '';
    reviewFormErrors.comment = '';

    // Validate form
    let isValid = true;

    if (!reviewForm.name.trim()) {
        reviewFormErrors.name = 'Name is required';
        isValid = false;
    }

    if (!reviewForm.email.trim()) {
        reviewFormErrors.email = 'Email is required';
        isValid = false;
    } else if (!isValidEmail(reviewForm.email)) {
        reviewFormErrors.email = 'Please enter a valid email';
        isValid = false;
    }

    if (!reviewForm.comment.trim()) {
        reviewFormErrors.comment = 'Comment is required';
        isValid = false;
    }

    if (!isValid) {
        return;
    }

    
    router.post(`/product/${prop.productId}/review`, {
        name: reviewForm.name,
        email: reviewForm.email,
        rate: reviewForm.rate,
        comment: reviewForm.comment
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Reset and hide form on success
            resetReviewForm();
            showReviewForm.value = false;
        },
        onError: (errors) => {
            // Handle validation errors from backend
            if (errors.name) reviewFormErrors.name = errors.name;
            if (errors.email) reviewFormErrors.email = errors.email;
            if (errors.comment) reviewFormErrors.comment = errors.comment;
        }
    });
};

// Function to get stars percentage for rating
const getStarsPercentage = (rate) => {
    return (rate / 5) * 100;
};

// Function to format date
const formatDate = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 0) return 'Today';
    if (diffDays === 1) return 'Yesterday';
    if (diffDays < 7) return `${diffDays} days ago`;
    if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
    if (diffDays < 365) return `${Math.floor(diffDays / 30)} months ago`;
    return `${Math.floor(diffDays / 365)} years ago`;
};

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
        recommendedProductImages[productId].currentImage = imageUrl;
        
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

// Initialize carousels after component mounts
onMounted(() => {
    if (prop.product?.images?.length > 0) {
        currentMainImage.value = prop.product.images[0].image_url;
    }
    
    initRecommendedProducts();

    nextTick(() => {
        setTimeout(() => {
            if (window.$ && window.$.fn.owlCarousel) {
                $('.owl-carousel').each(function() {
                    if ($(this).data('owl.carousel')) {
                        $(this).trigger('destroy.owl.carousel');
                        $(this).removeClass('owl-loaded owl-drag');
                    }
                });

                $('.carousel-equal-height').owlCarousel({
                    nav: false,
                    dots: true,
                    margin: 20,
                    loop: false,
                    responsive: {
                        0: { items: 1 },
                        480: { items: 2 },
                        768: { items: 3 },
                        992: { items: 4 },
                        1200: { items: 4, nav: true, dots: false }
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
    
    const galleryItems = document.querySelectorAll('.product-gallery-item');
    galleryItems.forEach(item => item.classList.remove('active'));
    event.currentTarget.classList.add('active');
};

// Function to format answer based on question type
const formatAnswer = (answer) => {
    if (!answer.answer) return '—';
    
    if (answer.question && answer.question.type === 'boolean') {
        return answer.answer === '1' || answer.answer === 1 || answer.answer === true ? 'Yes' : 'No';
    }
    
    return answer.answer;
};
</script>

<template>
<Skeleton page='product' :auth="auth">
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
                                    </figure>

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
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="product-details">
                                <h1 class="product-title">{{ product.name }}</h1>

                                <div class="ratings-container">
                                    <div class="ratings">
                                        <div class="ratings-val" style="width: 80%;"></div>
                                    </div>
                                    <a class="ratings-text" href="#product-review-link" id="review-link">( {{ product.reviews?.length || 0 }} Reviews )</a>
                                </div>

                                <div class="product-price">
                                    ₦{{ parseFloat(product.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                </div>
                                
                                <div class="product-content">
                                    <p>TYPE: {{ product.is_new? 'NEW': 'USED' }} </p>
                                    <p class="my-3">BRAND: {{ product.brand_name }} </p>
                                </div>

                                <div class="details-filter-row details-row-size">
                                    <label for="qty">Qty:</label>
                                    <div class="product-details-quantity">
                                        <input type="number" id="qty" class="form-control" value="1" min="1" max="10" step="1" data-decimals="0" required>
                                    </div>
                                </div>

                                <div class="product-details-action">
                                    <a href="#" class="btn-product btn-cart"><span>add to cart</span></a>

                                    <div class="details-action-wrapper">
                                        <a href="#" class="btn-product btn-wishlist" title="Wishlist"><span>Add to Wishlist</span></a>
                                    </div>
                                </div>

                                <div class="product-details-footer">
                                    <div class="product-cat">
                                        <span>Category:</span>
                                        <a href="#">{{ product.category.name }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
                            <a class="nav-link" id="product-review-link" data-toggle="tab" href="#product-review-tab" role="tab" aria-controls="product-review-tab" aria-selected="false">Reviews ({{ product.reviews?.length || 0 }})</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="product-desc-tab" role="tabpanel" aria-labelledby="product-desc-link">
                            <div class="product-desc-content">
                                <h3>Product Information</h3>
                                <p>{{ product.description }}</p>
                            </div>
                        </div>
                        
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
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="product-shipping-tab" role="tabpanel" aria-labelledby="product-shipping-link">
                            <div class="product-desc-content">
                                <h3>Delivery & returns</h3>
                                <p>We deliver to over 100 countries around the world. For full details of the delivery options we offer, please view our <a href="#">Delivery information</a><br>
                                We hope you'll love every purchase, but if you ever need to return an item you can do so within a month of receipt. For full details of how to make a return, please view our <a href="#">Returns information</a></p>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="product-review-tab" role="tabpanel" aria-labelledby="product-review-link">
                            <div class="reviews">
                                <h3>Reviews ({{ product.reviews?.length || 0 }})</h3>
                                
                                <!-- Existing Reviews -->
                                <div v-if="product.reviews && product.reviews.length > 0">
                                    <div v-for="review in product.reviews" :key="review.id" class="review">
                                        <div class="row no-gutters">
                                            <div class="col-auto">
                                                <h4><a href="#">{{ review.user?.name || review.name }}</a></h4>
                                                <div class="ratings-container">
                                                    <div class="ratings">
                                                        <div class="ratings-val" :style="{ width: getStarsPercentage(review.rate) + '%' }"></div>
                                                    </div>
                                                </div>
                                                <span class="review-date">{{ formatDate(review.created_at) }}</span>
                                            </div>
                                            <div class="col">
                                                <div class="review-content">
                                                    <p>{{ review.comment }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div v-else>
                                    <p class="mb-4">No reviews yet. Be the first to review this product!</p>
                                </div>

                                <!-- Add Review Button -->
                                <div class="mt-4">
                                    <button @click="toggleReviewForm" class="btn btn-outline-primary-2">
                                        <span>{{ showReviewForm ? 'Cancel' : 'Write a Review' }}</span>
                                        <i :class="showReviewForm ? 'icon-close' : 'icon-long-arrow-right'"></i>
                                    </button>
                                </div>

                                <!-- Review Form -->
                                <div v-if="showReviewForm" class="review-form mt-4">
                                    <h3 class="mb-3">Write Your Review</h3>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="review-name">Name <span class="required">*</span></label>
                                                <input 
                                                    type="text" 
                                                    class="form-control" 
                                                    id="review-name" 
                                                    v-model="reviewForm.name"
                                                    :class="{ 'is-invalid': reviewFormErrors.name }"
                                                    placeholder="Enter your name"
                                                >
                                                <div v-if="reviewFormErrors.name" class="invalid-feedback">
                                                    {{ reviewFormErrors.name }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="review-email">Email <span class="required">*</span></label>
                                                <input 
                                                    type="email" 
                                                    class="form-control" 
                                                    id="review-email" 
                                                    v-model="reviewForm.email"
                                                    :class="{ 'is-invalid': reviewFormErrors.email }"
                                                    placeholder="Enter your email"
                                                >
                                                <div v-if="reviewFormErrors.email" class="invalid-feedback">
                                                    {{ reviewFormErrors.email }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Your Rating <span class="required">*</span></label>
                                        <div class="rating-stars">
                                            <span 
                                                v-for="star in 5" 
                                                :key="star"
                                                @click="reviewForm.rate = star"
                                                class="star"
                                                :class="{ 'active': star <= reviewForm.rate }"
                                            >
                                                ★
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="review-comment">Your Review <span class="required">*</span></label>
                                        <textarea 
                                            class="form-control" 
                                            id="review-comment" 
                                            rows="5"
                                            v-model="reviewForm.comment"
                                            :class="{ 'is-invalid': reviewFormErrors.comment }"
                                            placeholder="Write your review here..."
                                        ></textarea>
                                        <div v-if="reviewFormErrors.comment" class="invalid-feedback">
                                            {{ reviewFormErrors.comment }}
                                        </div>
                                    </div>

                                    <button @click="submitReview" class="btn btn-outline-primary-2">
                                        <span>Submit Review</span>
                                        <i class="icon-long-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h2 v-if="recommendProducts.length > 0" class="title text-center mb-4">You May Also Like</h2>

                <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow" data-toggle="owl">
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
                            </div>

                            <div class="product-action">
                                <a href="#" class="btn-product btn-cart"><span>add to cart</span></a>
                            </div>
                        </figure>

                        <div class="product-body">
                            <div class="product-cat">
                                <a href="#">{{ recProduct.category?.name || 'Category' }}</a>
                            </div>
                            <h3 class="product-title">
                                <Link :href="`/product/${recProduct.id}`">{{ recProduct.name }}</Link>
                            </h3>
                            <div class="product-price">
                                ₦{{ parseFloat(recProduct.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                            </div>
                            <div class="ratings-container">
                                <div class="ratings">
                                    <div class="ratings-val" style="width: 80%;"></div>
                                </div>
                                <span class="ratings-text">( 0 Reviews )</span>
                            </div>
                            <div class="product-nav product-nav-thumbs">
                                <a v-for="(image, index) in recProduct.images" 
                                   :key="image.id" 
                                   href="#" 
                                   :class="{ active: index === 0 }"
                                   @click="changeRecommendedImage(recProduct.id, image.image_url, $event)">
                                    <img :src="image.image_url" alt="product thumbnail">
                                </a>
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

.review-form {
    background-color: #f8f9fa;
    padding: 30px;
    border-radius: 5px;
}

.rating-stars {
    display: flex;
    gap: 5px;
    font-size: 28px;
    margin-top: 5px;
}

.rating-stars .star {
    cursor: pointer;
    color: #ddd;
    transition: color 0.2s;
}

.rating-stars .star.active {
    color: #ffc107;
}

.rating-stars .star:hover {
    color: #ffc107;
}

.required {
    color: #f00;
}

.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
    color: #dc3545;
    font-size: 0.875em;
    margin-top: 0.25rem;
}
</style>