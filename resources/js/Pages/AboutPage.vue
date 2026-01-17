<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, nextTick, onUnmounted } from 'vue';
import Skeleton from './Skeleton.vue'

let props = defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    auth: {
        type: Object  
    },
    companyReview: Array
});

onMounted(()=> {
    console.log('review', props.companyReview)
})

// Initialize Owl Carousel after component mounts
onMounted(() => {
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

                // Initialize testimonials carousel
                // Initialize testimonials carousel
                $('.owl-testimonials-photo').owlCarousel({
                    nav: false,
                    dots: true,
                    margin: 20,
                    loop: true, // Changed to true for continuous sliding
                    items: 1,
                    autoplay: true, // Enable auto-slide
                    autoplayTimeout: 5000, // Time between slides (5 seconds)
                    autoplayHoverPause: true, // Pause on mouse hover
                    responsive: {
                        1200: {
                            nav: true,
                            items: 1
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

</script>

<template>
<Skeleton page="about" :auth="auth">
        <main class="main mt-3">
            <div class="container">
	        	<div class="page-header page-header-big text-center" style="background-image: url('assets/images/aboutus.jpg')">
        			<h1 class="page-title text-white">About us<span class="text-white">Who we are</span></h1>
	        	</div><!-- End .page-header -->
            </div><!-- End .container -->

            <div class="page-content pb-0">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6 mb-3 mb-lg-0">
                            <h2 class="title">Our Vision</h2><!-- End .title -->
                            <p>To become a go-to provider of quality technology solutions that enable smarter work, provide seamless productivity and lasting customer trust. </p>
                        </div><!-- End .col-lg-6 -->
                        
                        <div class="col-lg-6">
                            <h2 class="title">Our Mission</h2><!-- End .title -->
                            <p>To provide dependable technology products and support that help businesses and individuals work smarter, faster, and with confidence. </p>
                        </div><!-- End .col-lg-6 -->
                    </div><!-- End .row -->

                    <div class="mb-5"></div><!-- End .mb-4 -->
                </div><!-- End .container -->

                <div class="mb-2"></div><!-- End .mb-2 -->

                <div class="about-testimonials bg-light-2 pt-6 pb-6">
                    <div class="container">
                        <h2 class="title text-center mb-3">What Customer Say About Us</h2>

                        <div class="owl-carousel owl-simple owl-testimonials-photo">
                        <blockquote 
                            v-for="review in companyReview" 
                            :key="review.id" 
                            class="testimonial text-center"
                        >
                            <i class="icon-quote-left" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
                            <p>"{{ review.review }}"</p>
                            <cite>
                                {{ review.name }}
                                <span>{{ review.relationship }}</span>
                            </cite>
                        </blockquote>
                        </div>
                    </div>
                </div>
            </div><!-- End .page-content -->
        </main><!-- End .main -->
</Skeleton>
    
</template>