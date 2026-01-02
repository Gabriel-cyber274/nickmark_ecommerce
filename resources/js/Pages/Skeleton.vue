<script setup>
import { onMounted, nextTick, onUnmounted, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const searchQuery = ref('');

// Login and Register form data
const loginForm = ref({
    email: '',
    password: ''
});

const registerForm = ref({
    name: '',
    email: '',
    password: ''
});

const loginErrors = ref({});
const registerErrors = ref({});
const isLoggingIn = ref(false);
const isRegistering = ref(false);

const handleLogout = async()=> {
    try {
        const response = await fetch('/api/logout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
        });

        const data = await response.json();

        if (response.ok) {
            // Close the modal
            window.$('#signin-modal').modal('hide');
            
            // Redirect to dashboard
            window.location.href = '/'
            // router.visit('/');
        } else {
        }
    } catch (error) {
    } finally {
    }
}

// Handle Login
const handleLogin = async (event) => {
    event.preventDefault();
    loginErrors.value = {};
    isLoggingIn.value = true;

    try {
        const response = await fetch('/api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(loginForm.value)
        });

        const data = await response.json();

        if (response.ok) {
            // Close the modal
            window.$('#signin-modal').modal('hide');
            
            // Redirect to dashboard
            // router.visit('/dashboard');
            
            window.location.href = '/dashboard'
        } else {
            if (data.errors) {
                loginErrors.value = data.errors;
            } else if (data.message) {
                loginErrors.value = { general: data.message };
            }
        }
    } catch (error) {
        loginErrors.value = { general: 'An error occurred. Please try again.' };
    } finally {
        isLoggingIn.value = false;
    }
};

// Handle Register
const handleRegister = async (event) => {
    event.preventDefault();
    registerErrors.value = {};
    isRegistering.value = true;

    try {
        const response = await fetch('/api/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(registerForm.value)
        });

        const data = await response.json();

        if (response.ok) {
            // Close the modal
            window.$('#signin-modal').modal('hide');
            
            // Redirect to dashboard
            // router.visit('/dashboard');
            window.location.href = '/dashboard'
        } else {
            if (data.errors) {
                registerErrors.value = data.errors;
            } else if (data.message) {
                registerErrors.value = { general: data.message };
            }
        }
    } catch (error) {
        registerErrors.value = { general: 'An error occurred. Please try again.' };
    } finally {
        isRegistering.value = false;
    }
};

onMounted(() => {
    nextTick(() => {
        setTimeout(() => {
            // Initialize mobile menu
            initMobileMenu();
            
            // Initialize carousels
            initCarousels();
            
            // Initialize other features
            initStickyHeader();
            initScrollTop();
            initCountdowns();
        }, 200);
    });
});

// Handle search form submission
const handleSearch = (event) => {
    event.preventDefault();
    
    if (searchQuery.value.trim()) {
        router.get('/categories', { 
            search: searchQuery.value.trim() 
        }, {
            preserveState: false,
        });
    }
};

// Handle mobile search
const handleMobileSearch = (event) => {
    event.preventDefault();
    const mobileSearchInput = document.getElementById('mobile-search');
    
    if (mobileSearchInput && mobileSearchInput.value.trim()) {
        router.get('/categories', { 
            search: mobileSearchInput.value.trim() 
        }, {
            preserveState: false,
        });
    }
};

function initMobileMenu() {
    const mobileMenuToggler = document.querySelector('.mobile-menu-toggler');
    const mobileMenuContainer = document.querySelector('.mobile-menu-container');
    const mobileMenuOverlay = document.querySelector('.mobile-menu-overlay');
    const mobileMenuClose = document.querySelector('.mobile-menu-close');
    
    if (!mobileMenuToggler || !mobileMenuContainer) return;
    
    // Open mobile menu
    mobileMenuToggler.addEventListener('click', function(e) {
        e.preventDefault();
        document.body.classList.add('mmenu-active');
        mobileMenuContainer.classList.add('open');
        mobileMenuOverlay.classList.add('active');
    });
    
    // Close mobile menu
    function closeMobileMenu() {
        document.body.classList.remove('mmenu-active');
        mobileMenuContainer.classList.remove('open');
        mobileMenuOverlay.classList.remove('active');
    }
    
    if (mobileMenuClose) {
        mobileMenuClose.addEventListener('click', closeMobileMenu);
    }
    
    if (mobileMenuOverlay) {
        mobileMenuOverlay.addEventListener('click', closeMobileMenu);
    }
    
    // Handle submenu toggles
    const menuItems = document.querySelectorAll('.mobile-menu li > a');
    menuItems.forEach(item => {
        if (item.nextElementSibling && item.nextElementSibling.tagName === 'UL') {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const parent = this.parentElement;
                const submenu = this.nextElementSibling;
                
                // Toggle current submenu
                parent.classList.toggle('open');
                
                // Slide toggle effect
                if (parent.classList.contains('open')) {
                    submenu.style.display = 'block';
                } else {
                    submenu.style.display = 'none';
                }
            });
        }
    });
}

function initCarousels() {
    if (window.$ && window.$.fn.owlCarousel) {
        $('.owl-carousel').each(function() {
            if ($(this).data('owl.carousel')) {
                $(this).trigger('destroy.owl.carousel');
                $(this).removeClass('owl-loaded owl-drag');
            }
        });

        // Initialize intro slider
        $('.intro-slider').owlCarousel({
            loop: true,
            dots: true,
            nav: false,
            items: 1,
            autoHeight: false,
            responsive: {
                1200: {
                    nav: true,
                    dots: false
                }
            }
        });

        // Initialize other carousels
        $('.new-arrivals .owl-carousel, .trending-products .owl-carousel').each(function() {
            $(this).owlCarousel({
                nav: true,
                dots: true,
                margin: 20,
                loop: false,
                responsive: {
                    0: { items: 2 },
                    480: { items: 2 },
                    768: { items: 3 },
                    992: { items: 4 },
                    1200: { items: 5 }
                }
            });
        });

        // Initialize brand carousel
        $('.owl-simple').owlCarousel({
            nav: false,
            dots: false,
            margin: 30,
            loop: false,
            responsive: {
                0: { items: 2 },
                420: { items: 3 },
                600: { items: 4 },
                900: { items: 5 },
                1024: { items: 6 }
            }
        });
    }
}

function initStickyHeader() {
    setTimeout(() => {
        const header = document.querySelector('.header-bottom.sticky-header');
        const headerTop = document.querySelector('.header-top');
        const headerMiddle = document.querySelector('.header-middle');
        
        if (!header) return;
        
        function updateStickyHeader() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const stickyStart = (headerTop?.offsetHeight || 0) + (headerMiddle?.offsetHeight || 0);

            if (scrollTop > stickyStart) {
                header.classList.add('fixed', 'sticky-fixed');
            } else {
                header.classList.remove('fixed', 'sticky-fixed');
            }
        }
        
        let ticking = false;
        window.addEventListener('scroll', function() {
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    updateStickyHeader();
                    ticking = false;
                });
                ticking = true;
            }
        });
        
        updateStickyHeader();
    }, 100);
}

function initScrollTop() {
    const scrollTop = document.getElementById('scroll-top');
    if (scrollTop) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 400) {
                scrollTop.classList.add('show');
            } else {
                scrollTop.classList.remove('show');
            }
        });

        scrollTop.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
}

function initCountdowns() {
    if (window.$ && window.$.fn.countdown) {
        $('.deal-countdown').each(function() {
            const $this = $(this);
            const until = $this.data('until');
            if (until) {
                $this.countdown(until);
            }
        });
    }
}

onUnmounted(() => {
    // Clean up event listeners
    document.body.classList.remove('mmenu-active');
});

let prop = defineProps({
    page: {
        type: String,
        required: false,
        default: null,
    },
    auth: {
        type: Object  
    }
})

const categories = ref([])

onMounted(async () => {
    const response = await fetch('/api/categories')
    categories.value = await response.json()
})
</script>

<template>
    <div class="page-wrapper">
        <header class="header header-intro-clearance header-4">
            <div class="header-top">
                <div class="container">
                    <div class="header-left">
                        <a href="tel:#"><i class="icon-phone"></i>Call: +0123 456 789</a>
                    </div>

                    <!-- <div class="header-right">
                        <ul class="top-menu">
                            <li>
                                <a href="#">Links</a>
                                <ul>
                                    <li><a href="#signin-modal" data-toggle="modal">Sign in / Sign up</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div> -->
                   <div class="header-right">
                        <ul class="top-menu">
                            <li>
                                <a href="#">Account</a>
                                <ul>
                                    <template v-if="auth != null">
                                        <li v-if="page != 'dashboard'"><Link href="/dashboard">Dashboard</Link></li>
                                        <!-- <li><a href="#" @click.prevent="handleLogout">Logout</a></li> -->
                                        <li><button @click.prevent="handleLogout" class="logout-btn" style="background: none; border: none; color: inherit; cursor: pointer; font: inherit; padding: 0;">Logout</button></li>
                                    </template>
                                    <template v-else>
                                        <li><a href="#signin-modal" data-toggle="modal">Sign in / Sign up</a></li>
                                    </template>
                                </ul>
                            </li>
                        </ul>
                    </div>
               
               
                </div>
            </div>

            <div class="header-middle">
                <div class="container">
                    <div class="header-left">
                        <button class="mobile-menu-toggler">
                            <span class="sr-only">Toggle mobile menu</span>
                            <i class="icon-bars"></i>
                        </button>
                        
                        <Link href="/" class="logo">
                            <img src="/assets/images/demos/demo-4/logo.png" alt="Molla Logo" width="105" height="25">
                        </Link>
                    </div>

                    <div class="header-center">
                        <div class="header-search header-search-extended header-search-visible d-none d-lg-block">
                            <a href="#" class="search-toggle" role="button"><i class="icon-search"></i></a>
                            <form @submit="handleSearch" method="get">
                                <div class="header-search-wrapper search-wrapper-wide">
                                    <label for="q" class="sr-only">Search</label>
                                    <button class="btn btn-primary" type="submit"><i class="icon-search"></i></button>
                                    <input 
                                        type="search" 
                                        class="form-control" 
                                        v-model="searchQuery"
                                        name="q" 
                                        id="q" 
                                        placeholder="Search products, categories..." 
                                        required>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="header-right">
                        <div class="wishlist">
                            <a href="wishlist.html" title="Wishlist">
                                <div class="icon">
                                    <i class="icon-heart-o"></i>
                                    <span class="wishlist-count badge">3</span>
                                </div>
                                <p>Wishlist</p>
                            </a>
                        </div>

                        <div class="dropdown cart-dropdown">
                            <a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-display="static">
                                <div class="icon">
                                    <i class="icon-shopping-cart"></i>
                                    <span class="cart-count">2</span>
                                </div>
                                <p>Cart</p>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right">
                                <div class="dropdown-cart-products">
                                    <div class="product">
                                        <div class="product-cart-details">
                                            <h4 class="product-title">
                                                <a href="product.html">Beige knitted elastic runner shoes</a>
                                            </h4>
                                            <span class="cart-product-info">
                                                <span class="cart-product-qty">1</span>
                                                x $84.00
                                            </span>
                                        </div>
                                        <figure class="product-image-container">
                                            <a href="product.html" class="product-image">
                                                <img src="/assets/images/products/cart/product-1.jpg" alt="product">
                                            </a>
                                        </figure>
                                        <a href="#" class="btn-remove" title="Remove Product"><i class="icon-close"></i></a>
                                    </div>

                                    <div class="product">
                                        <div class="product-cart-details">
                                            <h4 class="product-title">
                                                <a href="product.html">Blue utility pinafore denim dress</a>
                                            </h4>
                                            <span class="cart-product-info">
                                                <span class="cart-product-qty">1</span>
                                                x $76.00
                                            </span>
                                        </div>
                                        <figure class="product-image-container">
                                            <a href="product.html" class="product-image">
                                                <img src="/assets/images/products/cart/product-2.jpg" alt="product">
                                            </a>
                                        </figure>
                                        <a href="#" class="btn-remove" title="Remove Product"><i class="icon-close"></i></a>
                                    </div>
                                </div>

                                <div class="dropdown-cart-total">
                                    <span>Total</span>
                                    <span class="cart-total-price">$160.00</span>
                                </div>

                                <div class="dropdown-cart-action">
                                    <a href="cart.html" class="btn btn-primary">View Cart</a>
                                    <a href="checkout.html" class="btn btn-outline-primary-2"><span>Checkout</span><i class="icon-long-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="header-bottom sticky-header">
                <div class="container">
                    <div class="header-left">
                        <div class="dropdown category-dropdown">
                            <a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-display="static" title="Browse Categories">
                                Browse Categories <i class="icon-angle-down"></i>
                            </a>

                            <div class="dropdown-menu">
                                <nav class="side-nav">
                                    <ul class="menu-vertical sf-arrows">
                                        <li v-for="category in categories" :key="category.id">
                                            <Link :href="`/categories?categories%5B0%5D=${category.id}`">{{ category.name }}</Link>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <div class="header-center">
                        <nav class="main-nav">
                            <ul class="menu sf-arrows">
                                <li :class="['megamenu-container', page == 'home'&&'active']">
                                    <Link href="/" class="sf-with-ul">Home</Link>
                                </li>
                                <li :class="['megamenu-container', page == 'about'&&'active']">
                                    <Link href="/about" class="sf-with-ul">About</Link>
                                </li>
                                <li :class="['megamenu-container', (page == 'categories' || page == 'product')&&'active']">
                                    <Link href="/categories" class="sf-with-ul">Shop</Link>
                                </li>
                                <li :class="['megamenu-container', page == 'contact-us'&&'active']">
                                    <Link href="/contact-us" class="sf-with-ul">Contact Us</Link>
                                </li>
                                <li :class="['megamenu-container', page == 'faq'&&'active']">
                                    <Link href="/faq" class="sf-with-ul">FAQ</Link>
                                </li>
                            </ul>
                        </nav>
                    </div>

                    <div class="header-right">
                        <i class="la la-lightbulb-o"></i><p>Clearance<span class="highlight">&nbsp;Up to 30% Off</span></p>
                    </div>
                </div>
            </div>
        </header>

        <slot></slot>

        <footer class="footer">
        	<div class="footer-middle">
	            <div class="container">
	            	<div class="row">
	            		<div class="col-sm-6 col-lg-3">
	            			<div class="widget widget-about">
	            				<img src="/assets/images/demos/demo-4/logo-footer.png" class="footer-logo" alt="Footer Logo" width="105" height="25">
	            				<p>Praesent dapibus, neque id cursus ucibus, tortor neque egestas augue, eu vulputate magna eros eu erat. </p>
	            				<div class="widget-call">
                                    <i class="icon-phone"></i>
                                    Got Question? Call us 24/7
                                    <a href="tel:#">+0123 456 789</a>         
                                </div>
	            			</div>
	            		</div>

	            		<div class="col-sm-6 col-lg-3">
	            			<div class="widget">
	            				<h4 class="widget-title">Useful Links</h4>
	            				<ul class="widget-list">
	            					<li><Link href="/about">About</Link></li>
                                    <li><Link href="/categories">Shop</Link></li>
	            					<li><Link href="/faq">FAQ</Link></li>
	            					<li><Link href="/contact-us">Contact us</Link></li>
	            				</ul>
	            			</div>
	            		</div>

	            		<div class="col-sm-6 col-lg-3">
	            			<div class="widget">
	            				<h4 class="widget-title">Category Links</h4>
	            				<ul class="widget-list">
	            					<li v-for="category in categories" :key="category.id">
                                        <Link :href="`/categories?categories%5B0%5D=${category.id}`">{{ category.name }}</Link>
                                    </li>
	            				</ul>
	            			</div>
	            		</div>

	            		<div class="col-sm-6 col-lg-3">
	            			<div class="widget">
	            				<h4 class="widget-title">My Account</h4>
	            				<ul class="widget-list">
	            					<li><a href="#">Sign In</a></li>
	            					<li><a href="cart.html">View Cart</a></li>
	            					<li><a href="#">My Wishlist</a></li>
	            					<li><a href="#">My Order</a></li>
	            					<li><a href="#">Help</a></li>
	            				</ul>
	            			</div>
	            		</div>
	            	</div>
	            </div>
	        </div>

	        <div class="footer-bottom">
	        	<div class="container">
	        		<p class="footer-copyright">Copyright © 2019 Molla Store. All Rights Reserved.</p>
	        	</div>
	        </div>
        </footer>
    </div>
    
    <button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>

    <!-- Mobile Menu -->
    <div class="mobile-menu-overlay"></div>

    <div class="mobile-menu-container mobile-menu-light">
        <div class="mobile-menu-wrapper">
            <span class="mobile-menu-close"><i class="icon-close"></i></span>
            
            <form @submit="handleMobileSearch" method="get" class="mobile-search">
                <label for="mobile-search" class="sr-only">Search</label>
                <input type="search" class="form-control" name="mobile-search" id="mobile-search" placeholder="Search in..." required>
                <button class="btn btn-primary" type="submit"><i class="icon-search"></i></button>
            </form>

            <ul class="nav nav-pills-mobile nav-border-anim" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="mobile-menu-link" data-toggle="tab" href="#mobile-menu-tab" role="tab" aria-controls="mobile-menu-tab" aria-selected="true">Menu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="mobile-cats-link" data-toggle="tab" href="#mobile-cats-tab" role="tab" aria-controls="mobile-cats-tab" aria-selected="false">Categories</a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="mobile-menu-tab" role="tabpanel" aria-labelledby="mobile-menu-link">
                    <nav class="mobile-nav">
                        <ul class="mobile-menu">
                            <li class="active">
                                <Link href="/">Home</Link>
                            </li>
                            <li>
                                <Link href="/about">About</Link>
                            </li>
                            <li>
                                <Link href="/categories" class="sf-with-ul">Shop</Link>
                            </li>
                            <li>
                                <Link href="/contact-us">Contact Us</Link>
                            </li>
                            <li>
                                <Link href="/faq">FAQ</Link>
                            </li>
                        </ul>
                    </nav>
                </div>
                <div class="tab-pane fade" id="mobile-cats-tab" role="tabpanel" aria-labelledby="mobile-cats-link">
                    <nav class="mobile-cats-nav">
                        <ul class="mobile-cats-menu">
                            <li v-for="category in categories" :key="category.id">
                                <Link :href="`/categories?categories%5B0%5D=${category.id}`">{{ category.name }}</Link>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

            <div class="social-icons">
                <a href="#" class="social-icon" target="_blank" title="Facebook"><i class="icon-facebook-f"></i></a>
                <a href="#" class="social-icon" target="_blank" title="Twitter"><i class="icon-twitter"></i></a>
                <a href="#" class="social-icon" target="_blank" title="Instagram"><i class="icon-instagram"></i></a>
                <a href="#" class="social-icon" target="_blank" title="Youtube"><i class="icon-youtube"></i></a>
            </div>
        </div>
    </div>

    <!-- Sign in / Register Modal -->
    <div class="modal fade" id="signin-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="icon-close"></i></span>
                    </button>

                    <div class="form-box">
                        <div class="form-tab">
                            <ul class="nav nav-pills nav-fill nav-border-anim" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="signin-tab" data-toggle="tab" href="#signin" role="tab" aria-controls="signin" aria-selected="true">Sign In</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="register-tab" data-toggle="tab" href="#register" role="tab" aria-controls="register" aria-selected="false">Register</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="tab-content-5">
                                <!-- Login Tab -->
                                <div class="tab-pane fade show active" id="signin" role="tabpanel" aria-labelledby="signin-tab">
                                    <form @submit="handleLogin">
                                        <div v-if="loginErrors.general" class="alert alert-danger">
                                            {{ loginErrors.general }}
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="singin-email">Email address *</label>
                                            <input 
                                                type="email" 
                                                class="form-control" 
                                                :class="{ 'is-invalid': loginErrors.email }"
                                                id="singin-email" 
                                                v-model="loginForm.email"
                                                required>
                                            <div v-if="loginErrors.email" class="invalid-feedback d-block">
                                                {{ loginErrors.email[0] }}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="singin-password">Password *</label>
                                            <input 
                                                type="password" 
                                                class="form-control" 
                                                :class="{ 'is-invalid': loginErrors.password }"
                                                id="singin-password" 
                                                v-model="loginForm.password"
                                                required>
                                            <div v-if="loginErrors.password" class="invalid-feedback d-block">
                                                {{ loginErrors.password[0] }}
                                            </div>
                                        </div>

                                        <div class="form-footer">
                                            <button type="submit" class="btn btn-outline-primary-2" :disabled="isLoggingIn">
                                                <span>{{ isLoggingIn ? 'LOGGING IN...' : 'LOG IN' }}</span>
                                                <i class="icon-long-arrow-right"></i>
                                            </button>

                                            <a href="#" class="forgot-link">Forgot Your Password?</a>
                                        </div>
                                    </form>
                                </div>
                                
                                <!-- Register Tab -->
                                <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                                    <form @submit="handleRegister">
                                        <div v-if="registerErrors.general" class="alert alert-danger">
                                            {{ registerErrors.general }}
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="register-name">Your name *</label>
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                :class="{ 'is-invalid': registerErrors.name }"
                                                id="register-name" 
                                                v-model="registerForm.name"
                                                required>
                                            <div v-if="registerErrors.name" class="invalid-feedback d-block">
                                                {{ registerErrors.name[0] }}
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="register-email">Your email address *</label>
                                            <input 
                                                type="email" 
                                                class="form-control" 
                                                :class="{ 'is-invalid': registerErrors.email }"
                                                id="register-email" 
                                                v-model="registerForm.email"
                                                required>
                                            <div v-if="registerErrors.email" class="invalid-feedback d-block">
                                                {{ registerErrors.email[0] }}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="register-password">Password *</label>
                                            <input 
                                                type="password" 
                                                class="form-control" 
                                                :class="{ 'is-invalid': registerErrors.password }"
                                                id="register-password" 
                                                v-model="registerForm.password"
                                                minlength="8"
                                                required>
                                            <div v-if="registerErrors.password" class="invalid-feedback d-block">
                                                {{ registerErrors.password[0] }}
                                            </div>
                                            <small class="form-text text-muted">Minimum 8 characters</small>
                                        </div>

                                        <div class="form-footer">
                                            <button type="submit" class="btn btn-outline-primary-2" :disabled="isRegistering">
                                                <span>{{ isRegistering ? 'SIGNING UP...' : 'SIGN UP' }}</span>
                                                <i class="icon-long-arrow-right"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.menu.sf-arrows .sf-with-ul::after {
    display: none !important;
}

.is-invalid {
    border-color: #dc3545 !important;
}

.invalid-feedback {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}
</style>