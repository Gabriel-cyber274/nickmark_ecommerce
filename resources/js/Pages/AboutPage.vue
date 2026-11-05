<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, nextTick, onUnmounted, computed } from 'vue';
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

// Safe base URL for SEO
const baseUrl = computed(() => {
    if (typeof window !== 'undefined' && window.location) {
        return window.location.origin;
    }
    return '';
});

</script>

<template>
    <Head title="About Us - Nickmark">
        <meta name="description" content="Learn more about Nickmark - your trusted source for quality electronics, laptops, smartphones, and gadgets.">
        <link rel="canonical" :href="`${baseUrl}/about`">
    </Head>

    <Skeleton page="about" :auth="auth">
        <main class="main mt-3 page-fade">
            <div class="container">
	        	<div class="page-header page-header-big text-center" style="background-image: url('assets/images/aboutus.jpg')">
        			<h1 class="page-title text-white">About us<span class="text-white">Who we are</span></h1>
	        	</div><!-- End .page-header -->
            </div><!-- End .container -->

            <div class="page-content pb-0">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6 mb-3 mb-lg-0 lift-card">
                            <h2 class="title">Our Vision</h2><!-- End .title -->
                            <p>To become a go-to provider of quality technology solutions that enable smarter work, provide seamless productivity and lasting customer trust. </p>
                        </div><!-- End .col-lg-6 -->
                        
                        <div class="col-lg-6 lift-card">
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
                            class="testimonial text-center lift-card"
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
