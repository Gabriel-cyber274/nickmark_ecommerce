<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import Skeleton from './Skeleton.vue'
import { usePage } from '@inertiajs/vue3';

const page = usePage();

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    auth: Object,
    orders: {
        type: Array,
        default: () => []
    },
    states: {
        type: Array,
        default: () => []
    },
    cities: {
        type: Array,
        default: () => []
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

// Account form
const accountForm = useForm({
    name: props.auth?.name || '',
    email: props.auth?.email || '',
    current_password: '',
    password: '',
    password_confirmation: ''
});

// Address form
const addressForm = useForm({
    phone: props.auth?.user_info?.phone || '',
    state_id: props.auth?.user_info?.state_id || '',
    city_id: props.auth?.user_info?.city_id || '',
    address: props.auth?.user_info?.address || '',
    postal_code: props.auth?.user_info?.postal_code || ''
});

// Filtered cities based on selected state
const filteredCities = computed(() => {
    if (!addressForm.state_id) return [];
    return props.cities.filter(city => city.state_id == addressForm.state_id);
});

// Watch for state changes and reset city if needed
watch(() => addressForm.state_id, (newStateId, oldStateId) => {
    if (newStateId !== oldStateId) {
        const cityExists = filteredCities.value.some(city => city.id == addressForm.city_id);
        if (!cityExists) {
            addressForm.city_id = '';
        }
    }
});

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
            window.$('#signin-modal').modal('hide');
            window.location.href = '/'
        }
    } catch (error) {
        console.error('Logout error:', error);
    }
}

const submitAccountForm = () => {
    accountForm.post('/user/update-account', {
        preserveScroll: true,
        onSuccess: () => {
            accountForm.reset('current_password', 'password', 'password_confirmation');
        }
    });
}

const submitAddressForm = () => {
    addressForm.post('/user/update-address', {
        preserveScroll: true
    });
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN'
    }).format(amount);
}

const getStatusClass = (status) => {
    const statusClasses = {
        'pending': 'badge-warning',
        'processing': 'badge-info',
        'completed': 'badge-success',
        'cancelled': 'badge-danger',
        'shipped': 'badge-primary'
    };
    return statusClasses[status] || 'badge-secondary';
}

// Get items count safely
const getItemsCount = (order) => {
    return order.items_count || order.items?.length || 0;
}

</script>

<template>
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
    
    <Skeleton page="dashboard" :auth="auth">
        <main class="main">
            <div class="page-header text-center" style="background-image: url('assets/images/page-header-bg.jpg')">
                <div class="container">
                    <h1 class="page-title">My Account<span>Shop</span></h1>
                </div>
            </div>
            
            <nav aria-label="breadcrumb" class="breadcrumb-nav mb-3">
                <div class="container">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><Link href="/">Home</Link></li>
                        <li class="breadcrumb-item"><Link href="/categories">Shop</Link></li>
                        <li class="breadcrumb-item active" aria-current="page">My Account</li>
                    </ol>
                </div>
            </nav>

            <div class="page-content">
                <div class="dashboard">
                    <div class="container">
                        <div class="row">
                            <aside class="col-md-4 col-lg-3">
                                <ul class="nav nav-dashboard flex-column mb-3 mb-md-0" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="tab-dashboard-link" data-toggle="tab" href="#tab-dashboard" role="tab">Dashboard</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab-orders-link" data-toggle="tab" href="#tab-orders" role="tab">Orders</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab-address-link" data-toggle="tab" href="#tab-address" role="tab">Address</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab-account-link" data-toggle="tab" href="#tab-account" role="tab">Account Details</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#" @click.prevent="handleLogout">Sign Out</a>
                                    </li>
                                </ul>
                            </aside>

                            <div class="col-md-8 col-lg-9">
                                <div class="tab-content">
                                    <!-- Dashboard Tab -->
                                    <div class="tab-pane fade show active" id="tab-dashboard" role="tabpanel">
                                        <p>Hello <span class="font-weight-normal text-dark">{{ auth.name }}</span> (<a href="#" @click.prevent="handleLogout">Log out</a>)</p>
                                        <p>From your account dashboard you can view your <a href="#tab-orders" class="tab-trigger-link link-underline">recent orders</a>, manage your <a href="#tab-address" class="tab-trigger-link">shipping and billing addresses</a>, and <a href="#tab-account" class="tab-trigger-link">edit your password and account details</a>.</p>
                                    </div>

                                    <!-- Orders Tab -->
                                    <div class="tab-pane fade" id="tab-orders" role="tabpanel">
                                        <div v-if="!orders || orders.length === 0">
                                            <p>No order has been made yet.</p>
                                            <Link href="/categories" class="btn btn-outline-primary-2">
                                                <span>GO SHOP</span>
                                                <i class="icon-long-arrow-right"></i>
                                            </Link>
                                        </div>
                                        <div v-else>
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Order</th>
                                                            <th>Date</th>
                                                            <th>Status</th>
                                                            <th>Total</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="order in orders" :key="order.id">
                                                            <td>#{{ order.reference }}</td>
                                                            <td>{{ formatDate(order.created_at) }}</td>
                                                            <td>
                                                                <span class="badge" :class="getStatusClass(order.status)">
                                                                    {{ order.status }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                {{ formatCurrency(order.total) }} 
                                                                <span class="text-muted small d-block">
                                                                    {{ getItemsCount(order) }} item(s)
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <Link :href="`/orders/${order.id}`" class="btn btn-sm btn-outline-primary-2">
                                                                    View
                                                                </Link>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Address Tab -->
                                    <div class="tab-pane fade" id="tab-address" role="tabpanel">
                                        <p>The following address will be used on the checkout page by default.</p>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="card card-dashboard">
                                                    <div class="card-body">
                                                        <h3 class="card-title">Billing/Shipping Address</h3>
                                                        
                                                        <div v-if="auth.user_info" class="mb-3">
                                                            <p>
                                                                <strong>{{ auth.name }}</strong><br>
                                                                {{ auth.user_info.address }}<br>
                                                                {{ auth.user_info.city?.name }}, {{ auth.user_info.state?.name }}<br>
                                                                <span v-if="auth.user_info.postal_code">{{ auth.user_info.postal_code }}<br></span>
                                                                {{ auth.user_info.phone }}<br>
                                                                {{ auth.email }}
                                                            </p>
                                                        </div>
                                                        <div v-else class="mb-3">
                                                            <p>You have not set up your address yet.</p>
                                                        </div>

                                                        <button class="btn btn-sm btn-outline-primary-2" data-toggle="collapse" data-target="#editAddressForm">
                                                            <i class="icon-edit"></i> Edit Address
                                                        </button>

                                                        <div id="editAddressForm" class="collapse mt-3">
                                                            <form @submit.prevent="submitAddressForm">
                                                                <div class="form-group">
                                                                    <label>Phone *</label>
                                                                    <input 
                                                                        type="text" 
                                                                        class="form-control" 
                                                                        v-model="addressForm.phone"
                                                                        required
                                                                    >
                                                                    <div v-if="addressForm.errors.phone" class="text-danger small">{{ addressForm.errors.phone }}</div>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label>Address *</label>
                                                                    <textarea 
                                                                        class="form-control" 
                                                                        v-model="addressForm.address"
                                                                        rows="3"
                                                                        required
                                                                    ></textarea>
                                                                    <div v-if="addressForm.errors.address" class="text-danger small">{{ addressForm.errors.address }}</div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label>State *</label>
                                                                            <select 
                                                                                class="form-control" 
                                                                                v-model="addressForm.state_id"
                                                                                required
                                                                            >
                                                                                <option value="">Select State</option>
                                                                                <option 
                                                                                    v-for="state in states" 
                                                                                    :key="state.id" 
                                                                                    :value="state.id"
                                                                                >
                                                                                    {{ state.name }}
                                                                                </option>
                                                                            </select>
                                                                            <div v-if="addressForm.errors.state_id" class="text-danger small">{{ addressForm.errors.state_id }}</div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label>City *</label>
                                                                            <select 
                                                                                class="form-control" 
                                                                                v-model="addressForm.city_id"
                                                                                :disabled="!addressForm.state_id"
                                                                                required
                                                                            >
                                                                                <option value="">Select City</option>
                                                                                <option 
                                                                                    v-for="city in filteredCities" 
                                                                                    :key="city.id" 
                                                                                    :value="city.id"
                                                                                >
                                                                                    {{ city.name }}
                                                                                </option>
                                                                            </select>
                                                                            <div v-if="addressForm.errors.city_id" class="text-danger small">{{ addressForm.errors.city_id }}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label>Postal Code</label>
                                                                    <input 
                                                                        type="text" 
                                                                        class="form-control" 
                                                                        v-model="addressForm.postal_code"
                                                                    >
                                                                    <div v-if="addressForm.errors.postal_code" class="text-danger small">{{ addressForm.errors.postal_code }}</div>
                                                                </div>

                                                                <button 
                                                                    type="submit" 
                                                                    class="btn btn-outline-primary-2"
                                                                    :disabled="addressForm.processing"
                                                                >
                                                                    <span>{{ addressForm.processing ? 'SAVING...' : 'SAVE ADDRESS' }}</span>
                                                                    <i class="icon-long-arrow-right"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Account Tab -->
                                    <div class="tab-pane fade" id="tab-account" role="tabpanel">
                                        <form @submit.prevent="submitAccountForm">
                                            <div class="form-group">
                                                <label>Name *</label>
                                                <input 
                                                    type="text" 
                                                    class="form-control" 
                                                    v-model="accountForm.name"
                                                    required
                                                >
                                                <div v-if="accountForm.errors.name" class="text-danger small">{{ accountForm.errors.name }}</div>
                                            </div>

                                            <div class="form-group">
                                                <label>Email address *</label>
                                                <input 
                                                    type="email" 
                                                    class="form-control" 
                                                    v-model="accountForm.email"
                                                    required
                                                >
                                                <div v-if="accountForm.errors.email" class="text-danger small">{{ accountForm.errors.email }}</div>
                                            </div>

                                            <div class="form-group">
                                                <label>Current password (leave blank to leave unchanged)</label>
                                                <input 
                                                    type="password" 
                                                    class="form-control"
                                                    v-model="accountForm.current_password"
                                                >
                                                <div v-if="accountForm.errors.current_password" class="text-danger small">{{ accountForm.errors.current_password }}</div>
                                            </div>

                                            <div class="form-group">
                                                <label>New password (leave blank to leave unchanged)</label>
                                                <input 
                                                    type="password" 
                                                    class="form-control"
                                                    v-model="accountForm.password"
                                                >
                                                <div v-if="accountForm.errors.password" class="text-danger small">{{ accountForm.errors.password }}</div>
                                            </div>

                                            <div class="form-group">
                                                <label>Confirm new password</label>
                                                <input 
                                                    type="password" 
                                                    class="form-control mb-2"
                                                    v-model="accountForm.password_confirmation"
                                                >
                                            </div>

                                            <button 
                                                type="submit" 
                                                class="btn btn-outline-primary-2"
                                                :disabled="accountForm.processing"
                                            >
                                                <span>{{ accountForm.processing ? 'SAVING...' : 'SAVE CHANGES' }}</span>
                                                <i class="icon-long-arrow-right"></i>
                                            </button>
                                        </form>
                                    </div>
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
.badge-primary {
    background-color: #007bff;
    color: white;
}
.badge-secondary {
    background-color: #6c757d;
    color: white;
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