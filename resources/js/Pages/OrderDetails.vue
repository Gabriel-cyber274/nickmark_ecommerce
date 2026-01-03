<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import Skeleton from './Skeleton.vue';

const props = defineProps({
    order: {
        type: Object,
        required: true
    },
    canLogin: Boolean,
    canRegister: Boolean,
    auth: Object
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN'
    }).format(amount);
};

const getStatusClass = (status) => {
    const statusClasses = {
        'pending': 'badge-warning',
        'processing': 'badge-info',
        'completed': 'badge-success',
        'cancelled': 'badge-danger',
        'shipped': 'badge-primary'
    };
    return statusClasses[status] || 'badge-secondary';
};

const getStatusText = (status) => {
    return status.charAt(0).toUpperCase() + status.slice(1);
};

// Calculate item subtotal
const getItemSubtotal = (item) => {
    return item.product.price * item.quantity;
};

// Calculate total items count
const totalItems = computed(() => {
    return props.order.items.reduce((sum, item) => sum + item.quantity, 0);
});

// Get the first image or a placeholder
const getProductImage = (product) => {
    if (product.images && product.images.length > 0) {
        return product.images[0].image_url;
    }
    return '/assets/images/products/product-placeholder.jpg';
};
</script>

<template>
    <Skeleton page="order-details" :auth="auth">
        <main class="main">
            <div class="page-header text-center" style="background-image: url('/assets/images/page-header-bg.jpg')">
                <div class="container">
                    <h1 class="page-title">Order Details<span>Order #{{ order.reference }}</span></h1>
                </div>
            </div>

            <nav aria-label="breadcrumb" class="breadcrumb-nav mb-3">
                <div class="container">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><Link href="/">Home</Link></li>
                        <li class="breadcrumb-item"><Link href="/dashboard">My Account</Link></li>
                        <li class="breadcrumb-item active" aria-current="page">Order #{{ order.reference }}</li>
                    </ol>
                </div>
            </nav>

            <div class="page-content pb-3">
                <div class="container">
                    <!-- Order Summary Card -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h4 class="mb-2">Order #{{ order.reference }}</h4>
                                            <p class="mb-1">
                                                <strong>Date:</strong> {{ formatDate(order.created_at) }}
                                            </p>
                                            <p class="mb-1">
                                                <strong>Status:</strong> 
                                                <span class="badge ml-2" :class="getStatusClass(order.status)">
                                                    {{ getStatusText(order.status) }}
                                                </span>
                                            </p>
                                            <p class="mb-0">
                                                <strong>Payment Method:</strong> {{ order.payment_method }}
                                            </p>
                                        </div>
                                        <div class="col-md-4 text-md-right mt-3 mt-md-0">
                                            <h3 class="mb-0">{{ formatCurrency(order.total) }}</h3>
                                            <p class="text-muted mb-0">{{ totalItems }} item(s)</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Order Items -->
                        <div class="col-lg-8 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Order Items</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-cart table-mobile mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Price</th>
                                                    <th>Quantity</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="item in order.items" :key="item.id">
                                                    <td class="product-col">
                                                        <div class="product">
                                                            <figure class="product-media">
                                                                <Link :href="`/product/${item.product.id}`">
                                                                    <img 
                                                                        :src="getProductImage(item.product)" 
                                                                        :alt="item.product.name"
                                                                        style="width: 80px; height: 80px; object-fit: cover;"
                                                                    >
                                                                </Link>
                                                            </figure>
                                                            <div class="product-title-container">
                                                                <h3 class="product-title">
                                                                    <Link :href="`/product/${item.product.id}`">
                                                                        {{ item.product.name }}
                                                                    </Link>
                                                                </h3>
                                                                <span class="product-category text-muted">
                                                                    {{ item.product.category?.name }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="price-col">{{ formatCurrency(item.product.price) }}</td>
                                                    <td class="quantity-col">{{ item.quantity }}</td>
                                                    <td class="total-col">{{ formatCurrency(getItemSubtotal(item)) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Note (if exists) -->
                            <div v-if="order.order_note" class="card mt-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Order Note</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ order.order_note }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Order Summary Sidebar -->
                        <div class="col-lg-4">
                            <!-- Shipping Address -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Shipping Address</h5>
                                </div>
                                <div class="card-body">
                                    <address>
                                        <strong>{{ order.name }}</strong><br>
                                        {{ order.address }}<br>
                                        {{ order.city?.name }}, {{ order.state?.name }}<br>
                                        {{ order.postal_code }}<br>
                                        <br>
                                        <strong>Phone:</strong> {{ order.phone }}<br>
                                        <strong>Email:</strong> {{ order.email }}
                                    </address>
                                </div>
                            </div>

                            <!-- Order Totals -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Order Summary</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-summary">
                                        <tbody>
                                            <tr class="summary-subtotal">
                                                <td>Subtotal:</td>
                                                <td>{{ formatCurrency(order.subtotal || order.total) }}</td>
                                            </tr>
                                            <tr v-if="order.discount" class="summary-discount">
                                                <td>
                                                    Discount:
                                                    <span class="text-muted small d-block">
                                                        Code: {{ order.discount.code }}
                                                    </span>
                                                </td>
                                                <td class="text-success">
                                                    -{{ order.discount.percentage }}%
                                                </td>
                                            </tr>
                                            <tr class="summary-shipping">
                                                <td>Shipping:</td>
                                                <td>
                                                    {{ formatCurrency(order.total - (order.subtotal || order.total)) }}
                                                </td>
                                            </tr>
                                            <tr class="summary-total">
                                                <td><strong>Total:</strong></td>
                                                <td><strong>{{ formatCurrency(order.total) }}</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-3">
                                <Link 
                                    href="/dashboard" 
                                    class="btn btn-outline-primary-2 btn-block"
                                >
                                    <span>BACK TO ORDERS</span>
                                    <i class="icon-long-arrow-left"></i>
                                </Link>
                                
                                <Link 
                                    href="/categories" 
                                    class="btn btn-outline-dark-2 btn-block mt-2"
                                >
                                    <span>CONTINUE SHOPPING</span>
                                    <i class="icon-long-arrow-right"></i>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </Skeleton>
</template>

<style scoped>
.card {
    border: 1px solid #ebebeb;
    border-radius: 0;
}

.card-header {
    background-color: #f9f9f9;
    border-bottom: 1px solid #ebebeb;
    padding: 1rem 1.5rem;
}

.card-body {
    padding: 1.5rem;
}

.badge {
    padding: 0.35em 0.65em;
    font-size: 0.75em;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.badge-info {
    background-color: #17a2b8;
    color: white;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.badge-danger {
    background-color: #dc3545;
    color: white;
}

.badge-secondary {
    background-color: #6c757d;
    color: white;
}

.badge-primary {
    background-color: #007bff;
    color: white;
}

.product {
    display: flex;
    align-items: center;
}

.product-media {
    margin-right: 1rem;
    margin-bottom: 0;
}

.product-title {
    font-size: 1rem;
    margin-bottom: 0.25rem;
}

.product-category {
    font-size: 0.875rem;
}

.table-summary tbody tr td {
    padding: 0.5rem 0;
    border: none;
}

.table-summary tbody tr:last-child td {
    padding-top: 1rem;
    border-top: 2px solid #ebebeb;
}

address {
    margin-bottom: 0;
    line-height: 1.8;
}

@media (max-width: 767px) {
    .product {
        flex-direction: column;
        text-align: center;
    }
    
    .product-media {
        margin-right: 0;
        margin-bottom: 1rem;
    }
}
</style>