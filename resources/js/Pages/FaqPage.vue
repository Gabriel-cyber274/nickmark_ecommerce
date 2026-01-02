<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, nextTick, onUnmounted } from 'vue';
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
</script>

<template>
<Skeleton page='faq' :auth="auth">
        <main class="main">
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
                            class="card card-box card-sm bg-light"
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