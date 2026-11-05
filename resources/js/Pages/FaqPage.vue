<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, nextTick, onUnmounted, computed } from 'vue';
import Skeleton from './Skeleton.vue'

let prop = defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    faq: {
        type: Array
    },
    auth: {
        type: Object  
    }
});

onMounted(()=> {
    console.log('faq', prop.faq)
})

// Safe base URL for SEO
const baseUrl = computed(() => {
    if (typeof window !== 'undefined' && window.location) {
        return window.location.origin;
    }
    return '';
});
</script>

<template>
    <Head title="FAQ - Nickmark">
        <meta name="description" content="Frequently asked questions about Nickmark - shipping, returns, payments, and more.">
        <link rel="canonical" :href="`${baseUrl}/faq`">
    </Head>

    <Skeleton page='faq' :auth="auth">
        <main class="main page-fade">
            <div class="page-header text-center" style="background-image: url('assets/images/page-header-bg.jpg')">
                <div class="container">
                    <h1 class="page-title">F.A.Q<span>Pages</span></h1>
                </div><!-- End .container -->
            </div><!-- End .page-header -->

            <div class="page-content mt-3">
                <div class="container">
                    <!-- <h2 class="title text-center mb-3">Shipping Information</h2> -->
                    <div class="accordion accordion-rounded" id="accordion-1">
                        <!-- 动态渲染 FAQ 数据 -->
                        <div 
                            v-for="(item, index) in faq" 
                            :key="item.id" 
                            class="card card-box card-sm bg-light lift-card"
                        >
                            <div class="card-header" :id="`heading-${index + 1}`">
                                <h2 class="card-title">
                                    <a 
                                        :class="{'collapsed': index !== 0}" 
                                        role="button" 
                                        data-toggle="collapse" 
                                        :href="`#collapse-${index + 1}`" 
                                        :aria-expanded="index === 0 ? 'true' : 'false'" 
                                        :aria-controls="`collapse-${index + 1}`"
                                    >
                                        {{ item.question }}
                                    </a>
                                </h2>
                            </div><!-- End .card-header -->
                            <div 
                                :id="`collapse-${index + 1}`" 
                                :class="['collapse', index === 0 ? 'show' : '']" 
                                :aria-labelledby="`heading-${index + 1}`" 
                                data-parent="#accordion-1"
                            >
                                <div class="card-body">
                                    {{ item.answer }}
                                </div><!-- End .card-body -->
                            </div><!-- End .collapse -->
                        </div><!-- End .card -->

                        <!-- 原始示例数据，可作为备份或参考 -->
                        <!-- 可以删除以下静态内容，或保留作为模板 -->
                        <!-- 
                        <div class="card card-box card-sm bg-light">
                            <div class="card-header" id="heading-1">
                                <h2 class="card-title">
                                    <a role="button" data-toggle="collapse" href="#collapse-1" aria-expanded="true" aria-controls="collapse-1">
                                        How will my parcel be delivered?
                                    </a>
                                </h2>
                            </div>
                            <div id="collapse-1" class="collapse show" aria-labelledby="heading-1" data-parent="#accordion-1">
                                <div class="card-body">
                                    Lorem ipsum dolor sit amet...
                                </div>
                            </div>
                        </div>
                        -->
                    </div><!-- End .accordion -->

                </div><!-- End .container -->
            </div><!-- End .page-content -->

            <div class="cta cta-display bg-image pt-4 pb-4" style="background-image: url(assets/images/backgrounds/cta/bg-7.jpg);">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-10 col-lg-9 col-xl-7">
                            <div class="row no-gutters flex-column flex-sm-row align-items-sm-center">
                                <div class="col">
                                    <h3 class="cta-title text-white">If You Have More Questions</h3><!-- End .cta-title -->
                                    <!-- <p class="cta-desc text-white">Quisque volutpat mattis eros</p> -->
                                </div><!-- End .col -->

                                <div class="col-auto">
                                    <Link href="/contact-us" class="btn btn-outline-white"><span>CONTACT US</span><i class="icon-long-arrow-right"></i></Link>
                                </div><!-- End .col-auto -->
                            </div><!-- End .row no-gutters -->
                        </div><!-- End .col-md-10 col-lg-9 -->
                    </div><!-- End .row -->
                </div><!-- End .container -->
            </div><!-- End .cta -->
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
