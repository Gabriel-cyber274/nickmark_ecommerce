<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { onMounted, ref, computed, watch } from 'vue';
import Skeleton from './Skeleton.vue';
import { useCart } from '../composables/useCart';

import { usePage } from '@inertiajs/vue3';

const page = usePage();



const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    auth: Object,
    stateandcity: Array,
    codes: Array 
});

onMounted(()=> {
    console.log('auth', props.auth)
})


// Watch for flash messages (add this with your existing code)
watch(() => page.props.flash, (flash) => {
    if (flash?.success) {
        showToast(flash.success, 'success');
    }
    if (flash?.error) {
        showToast(flash.error, 'error');
    }
}, { deep: true, immediate: true });


const prepareOrderData = () => {
    return {
        first_name: checkoutForm.value.first_name,
        last_name: checkoutForm.value.last_name,
        email: checkoutForm.value.email,
        phone: checkoutForm.value.phone,
        address: checkoutForm.value.address,
        address_line_2: checkoutForm.value.address_line_2,
        city: checkoutForm.value.city,
        state: checkoutForm.value.state,
        postal_code: checkoutForm.value.postal_code,
        delivery_method: checkoutForm.value.delivery_method,
        shipping_fee: checkoutForm.value.shipping_fee,
        discount_code: appliedDiscount.value?.code || null,
        notes: checkoutForm.value.notes,
        cart_items: cartProducts.value.map(item => ({
            product_id: item.product_id || item.id,
            quantity: item.quantity,
            price: parseFloat(item.product?.price || item.price)
        })),
        subtotal: subtotal.value,
        total: total.value
    };
};

const { cartItems } = useCart(props.auth);
const cartProducts = ref([]);
const isLoading = ref(true);
const isCartLoaded = ref(false);
const cities = ref([]);
const discountCode = ref('');
const appliedDiscount = ref(null);
const toast = ref({
    show: false,
    message: '',
    type: 'error'
});

// Show toast notification
const showToast = (message, type = 'error') => {
    toast.value.message = message;
    toast.value.type = type;
    toast.value.show = true;
    
    setTimeout(() => {
        toast.value.show = false;
    }, 5000);
};

// Close toast manually
const closeToast = () => {
    toast.value.show = false;
};

// Form data
const checkoutForm = ref({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    address: '',
    address_line_2: '',
    city: '',
    state: '',
    postal_code: '',
    delivery_method: 'pickup',
    shipping_fee: 0,
    create_account: false,
    password: '',
    notes: ''
});

// Get selected state object
const selectedState = computed(() => {
    return props.stateandcity.find(state => state.name === checkoutForm.value.state);
});

// Get selected city object
const selectedCity = computed(() => {
    if (!selectedState.value || !checkoutForm.value.city) return null;
    return selectedState.value.cities.find(city => city.name === checkoutForm.value.city);
});

// Calculate dispatch fee based on city or state
const dispatchFee = computed(() => {
    if (checkoutForm.value.delivery_method !== 'dispatch') return 0;
    
    // First check if city has a dispatch fee
    if (selectedCity.value?.dispatch_fee) {
        return parseFloat(selectedCity.value.dispatch_fee.amount);
    }
    
    // If city doesn't have fee, use state's dispatch fee
    if (selectedState.value?.dispatch_fee) {
        return parseFloat(selectedState.value.dispatch_fee.amount);
    }
    
    // Default fee if neither city nor state has a fee set
    return 2000;
});

// Watch state changes to update cities
watch(() => checkoutForm.value.state, (newState) => {
    checkoutForm.value.city = '';
    
    if (newState && selectedState.value) {
        cities.value = selectedState.value.cities || [];
    } else {
        cities.value = [];
    }
});

// Watch delivery method and city/state changes to update shipping fee
watch([
    () => checkoutForm.value.delivery_method,
    () => checkoutForm.value.state,
    () => checkoutForm.value.city
], () => {
    if (checkoutForm.value.delivery_method === 'pickup') {
        checkoutForm.value.shipping_fee = 0;
    } else {
        checkoutForm.value.shipping_fee = dispatchFee.value;
    }
});

// Fetch cart products
const fetchCartProducts = async () => {
    isLoading.value = true;
    isCartLoaded.value = false;
    
    if (props.auth) {
        const response = await fetch('/api/cart', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'include'
        });
        
        if (response.ok) {
            cartProducts.value = await response.json();
        }
    } else {
        if (cartItems.value.length > 0) {
            const response = await fetch('/api/cart/products', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ items: cartItems.value })
            });
            
            if (response.ok) {
                cartProducts.value = await response.json();
            }
        }
    }
    
    isLoading.value = false;
    isCartLoaded.value = true;
};

// Calculate subtotal
const subtotal = computed(() => {
    return cartProducts.value.reduce((total, item) => {
        const price = parseFloat(item.product?.price || item.price || 0);
        const quantity = item.quantity || 1;
        return total + (price * quantity);
    }, 0);
});

// Calculate discount amount
const discountAmount = computed(() => {
    if (!appliedDiscount.value) return 0;
    
    // Check if subtotal (without shipping) meets minimum amount
    if (subtotal.value < parseFloat(appliedDiscount.value.min_amount)) {
        return 0;
    }
    
    return parseFloat(appliedDiscount.value.discount_amount);
});

// Calculate total
const total = computed(() => {
    return subtotal.value + checkoutForm.value.shipping_fee - discountAmount.value;
});

// Apply discount code
const applyDiscountCode = () => {
    if (!discountCode.value.trim()) {
        showToast('Please enter a discount code', 'warning');
        return;
    }
    
    // Check if codes array exists and has items
    if (!props.codes || !Array.isArray(props.codes) || props.codes.length === 0) {
        showToast('No discount codes available at the moment', 'error');
        return;
    }
    
    // Find the code in the codes array
    const code = props.codes.find(c => c.code.toUpperCase() === discountCode.value.trim().toUpperCase());
    
    if (!code) {
        showToast('Invalid discount code', 'error');
        return;
    }
    
    
    // Check if subtotal meets minimum amount
    if (subtotal.value < parseFloat(code.min_amount)) {
        showToast(`Minimum order amount of ₦${parseFloat(code.min_amount).toLocaleString()} required for this code`, 'warning');
        return;
    }
    
    // Apply the discount
    appliedDiscount.value = code;
    showToast(`Discount code applied! You saved ₦${parseFloat(code.discount_amount).toLocaleString()}`, 'success');
};

// Remove discount code
const removeDiscountCode = () => {
    appliedDiscount.value = null;
    discountCode.value = '';
    showToast('Discount code removed', 'info');
};

// Handle account creation
const handleCreateAccount = async () => {
    if (!checkoutForm.value.create_account) return true;
    
    try {
        const response = await fetch('/api/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                name: `${checkoutForm.value.first_name} ${checkoutForm.value.last_name}`,
                email: checkoutForm.value.email,
                password: checkoutForm.value.password
            })
        });

        const data = await response.json();

        if (response.ok) {
            showToast('Account created successfully!', 'success');
            return true;
        } else {
            if (data.errors) {
                const errorMessages = Object.values(data.errors).flat();
                showToast(errorMessages.join(', '), 'error');
            } else if (data.message) {
                showToast(data.message, 'error');
            }
            return false;
        }
    } catch (error) {
        console.error('Error creating account:', error);
        showToast('An error occurred while creating account. Please try again.', 'error');
        return false;
    }
};

// Handle Paystack payment
const handlePaystackPayment = async () => {
    try {
        const orderData = prepareOrderData();
        
        const response = await fetch('/paystack/initiate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(orderData)
        });

        const data = await response.json();

        if (response.ok && data.authorization_url) {
            // Redirect to Paystack payment page
            window.location.href = data.authorization_url;
        } else {
            showToast('Failed to initialize payment. Please try again.', 'error');
        }
    } catch (error) {
        console.error('Payment error:', error);
        showToast('An error occurred. Please try again.', 'error');
    }
};


// Handle WhatsApp order
const handleWhatsAppOrder = async () => {
    try {
        const orderData = prepareOrderData();
        
        const response = await fetch('/order/whatsapp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(orderData)
        });

        const data = await response.json();

        if (response.ok && data.success) {
            // Construct WhatsApp message
            const orderDetails = `
🛍️ *New Order* - Ref: ${data.reference}

*Customer Details:*
Name: ${checkoutForm.value.first_name} ${checkoutForm.value.last_name}
Email: ${checkoutForm.value.email}
Phone: ${checkoutForm.value.phone}
Address: ${checkoutForm.value.address}${checkoutForm.value.address_line_2 ? ', ' + checkoutForm.value.address_line_2 : ''}
City: ${checkoutForm.value.city}
State: ${checkoutForm.value.state}
Postal Code: ${checkoutForm.value.postal_code}

*Delivery Method:* ${checkoutForm.value.delivery_method === 'pickup' ? 'Pickup' : 'Dispatch'}

*Order Items:*
${cartProducts.value.map(item => 
    `- ${item.product?.name || item.name} (x${item.quantity}) - ₦${(parseFloat(item.product?.price || item.price) * item.quantity).toLocaleString()}`
).join('\n')}

*Subtotal:* ₦${subtotal.value.toLocaleString()}
*Shipping:* ₦${checkoutForm.value.shipping_fee.toLocaleString()}
${appliedDiscount.value ? `*Discount (${appliedDiscount.value.code}):* -₦${discountAmount.value.toLocaleString()}\n` : ''}*Total:* ₦${total.value.toLocaleString()}

${checkoutForm.value.notes ? `*Notes:* ${checkoutForm.value.notes}` : ''}
            `.trim();
            
            const encodedMessage = encodeURIComponent(orderDetails);
            const whatsappUrl = `https://wa.me/YOUR_PHONE_NUMBER?text=${encodedMessage}`;
            
            // Show success message
            showToast('Order created successfully! Redirecting to WhatsApp...', 'success');
            
            // Redirect after a short delay
            setTimeout(() => {
                window.open(whatsappUrl, '_blank');
                // Redirect to home or order confirmation page
                router.visit('/');
            }, 1500);
        } else {
            showToast(data.message || 'Failed to create order. Please try again.', 'error');
        }
    } catch (error) {
        console.error('WhatsApp order error:', error);
        showToast('An error occurred. Please try again.', 'error');
    }
};


// Handle order submission
const handleSubmit = async (paymentMethod) => {
    if (!checkoutForm.value.first_name || !checkoutForm.value.last_name || 
        !checkoutForm.value.email || !checkoutForm.value.phone || 
        !checkoutForm.value.address || !checkoutForm.value.city || 
        !checkoutForm.value.state) {
        showToast('Please fill in all required fields', 'warning');
        return;
    }
    
    if (checkoutForm.value.create_account && !checkoutForm.value.password) {
        showToast('Please enter a password to create an account', 'warning');
        return;
    }
    
    if (checkoutForm.value.create_account) {
        const accountCreated = await handleCreateAccount();
        if (!accountCreated) {
            return;
        }
    }
    
    if (paymentMethod === 'paystack') {
        await handlePaystackPayment();
    } else if (paymentMethod === 'whatsapp') {
        handleWhatsAppOrder();
    }
};


// If state/city mapping fails, fall back to empty values
watch(() => checkoutForm.value.state, () => {
    // Only update city if we successfully set a state
    if (checkoutForm.value.state && props.auth?.user_info?.city_id) {
        const state = props.stateandcity.find(s => s.name === checkoutForm.value.state);
        if (state && state.cities) {
            const city = state.cities.find(c => c.id == props.auth.user_info.city_id);
            checkoutForm.value.city = city ? city.name : '';
        }
    }
});

onMounted(() => {
    fetchCartProducts();
    
    if (props.auth) {
        // Fill basic user info
        checkoutForm.value.first_name = props.auth.name?.split(' ')[0] || '';
        checkoutForm.value.last_name = props.auth.name?.split(' ').slice(1).join(' ') || '';
        checkoutForm.value.email = props.auth.email || '';
        
        // Check if user_info exists and fill the rest
        if (props.auth.user_info) {
            const userInfo = props.auth.user_info;
            
            checkoutForm.value.phone = userInfo.phone || '';
            checkoutForm.value.address = userInfo.address || '';
            checkoutForm.value.postal_code = userInfo.postal_code || '';
            
            // To set state and city, we need to find the names from the stateandcity prop
            if (userInfo.state_id && props.stateandcity) {
                // Find state by state_id
                const state = props.stateandcity.find(s => s.id == userInfo.state_id);
                if (state) {
                    checkoutForm.value.state = state.name;
                    
                    // After setting state, find and set city
                    if (userInfo.city_id && state.cities) {
                        const city = state.cities.find(c => c.id == userInfo.city_id);
                        if (city) {
                            checkoutForm.value.city = city.name;
                        }
                    }
                }
            }
        }
    }
});
</script>

<template>
<Skeleton page='checkout' :auth="auth">
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

    <main class="main">
        <div class="page-header text-center" style="background-image: url('assets/images/page-header-bg.jpg')">
            <div class="container">
                <h1 class="page-title">Checkout<span>Shop</span></h1>
            </div>
        </div>

        <div class="page-content mt-3">
            <div class="checkout">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-9">
                            <h2 class="checkout-title">Billing Details</h2>
                            
                            <div class="row">
                                <div class="col-sm-6">
                                    <label>First Name *</label>
                                    <input type="text" class="form-control" v-model="checkoutForm.first_name" required>
                                </div>

                                <div class="col-sm-6">
                                    <label>Last Name *</label>
                                    <input type="text" class="form-control" v-model="checkoutForm.last_name" required>
                                </div>
                            </div>

                            <label>Email address *</label>
                            <input type="email" class="form-control" v-model="checkoutForm.email" required>

                            <label>Phone *</label>
                            <input type="tel" class="form-control" v-model="checkoutForm.phone" required>

                            <label>Street address *</label>
                            <input type="text" class="form-control" v-model="checkoutForm.address" placeholder="House number and Street name" required>
                            <input type="text" class="form-control" v-model="checkoutForm.address_line_2" placeholder="Apartments, suite, unit etc ... (optional)">

                            <div class="row">
                                <div class="col-sm-6">
                                    <label>State *</label>
                                    <select class="form-control" v-model="checkoutForm.state" required>
                                        <option value="">Select State</option>
                                        <option v-for="state in stateandcity" :key="state.id" :value="state.name">
                                            {{ state.name }}
                                        </option>
                                    </select>
                                </div>

                                <div class="col-sm-6">
                                    <label>City *</label>
                                    <select class="form-control" v-model="checkoutForm.city" :disabled="!checkoutForm.state" required>
                                        <option value="">{{ checkoutForm.state ? 'Select City' : 'Select state first' }}</option>
                                        <option v-for="city in cities" :key="city.id" :value="city.name">
                                            {{ city.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <label>Postcode / ZIP *</label>
                            <input type="text" class="form-control" v-model="checkoutForm.postal_code" required>

                            <div class="mt-4">
                                <h3>Delivery Method</h3>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="pickup" value="pickup" v-model="checkoutForm.delivery_method" class="custom-control-input">
                                    <label class="custom-control-label" for="pickup">
                                        Pickup - Free
                                    </label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="dispatch" value="dispatch" v-model="checkoutForm.delivery_method" class="custom-control-input">
                                    <label class="custom-control-label" for="dispatch">
                                        Dispatch <span v-if="dispatchFee != 0">- ₦{{ dispatchFee.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</span>
                                        <small v-if="selectedCity?.dispatch_fee" class="text-muted d-block">
                                            ({{ checkoutForm.city }} rate)
                                        </small>
                                        <small v-else-if="selectedState?.dispatch_fee" class="text-muted d-block">
                                            ({{ checkoutForm.state }} rate)
                                        </small>
                                    </label>
                                </div>
                            </div>

                            <div class="mt-4" v-if="isCartLoaded && cartProducts.length > 0">
                                <h3>Discount Code</h3>
                                <div v-if="!appliedDiscount" class="input-group">
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        v-model="discountCode"
                                        placeholder="Enter coupon code"
                                        @keyup.enter="applyDiscountCode"
                                    >
                                    <div class="input-group-append">
                                        <button 
                                            class="btn btn-outline-primary-2" 
                                            type="button"
                                            @click="applyDiscountCode"
                                        >
                                            Apply
                                        </button>
                                    </div>
                                </div>
                                <div v-else class="alert alert-success d-flex justify-content-between align-items-center">
                                    <span>
                                        <strong>{{ appliedDiscount.code }}</strong> applied
                                        - Save ₦{{ parseFloat(appliedDiscount.discount_amount).toLocaleString() }}
                                    </span>
                                    <button 
                                        type="button" 
                                        class="btn btn-sm btn-outline-danger"
                                        @click="removeDiscountCode"
                                    >
                                        Remove
                                    </button>
                                </div>
                            </div>

                            <div v-if="!auth" class="mt-4">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="checkout-create-acc" v-model="checkoutForm.create_account">
                                    <label class="custom-control-label" for="checkout-create-acc">Create an account?</label>
                                </div>
                                
                                <div v-if="checkoutForm.create_account" class="mt-3">
                                    <label>Password *</label>
                                    <input type="password" class="form-control" v-model="checkoutForm.password" required>
                                    <small class="text-muted">Create a password to save your order history</small>
                                </div>
                            </div>

                            <label class="mt-3">Order notes (optional)</label>
                            <textarea class="form-control" v-model="checkoutForm.notes" cols="30" rows="4" placeholder="Notes about your order, e.g. special notes for delivery"></textarea>
                        </div>

                        <aside class="col-lg-3">
                            <div class="summary">
                                <h3 class="summary-title">Your Order</h3>

                                <table class="table table-summary">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr v-for="item in cartProducts" :key="item.product_id || item.id">
                                            <td>
                                                <a href="#">{{ item.product?.name || item.name }} × {{ item.quantity }}</a>
                                            </td>
                                            <td>₦{{ (parseFloat(item.product?.price || item.price) * item.quantity).toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</td>
                                        </tr>
                                        
                                        <tr class="summary-subtotal">
                                            <td>Subtotal:</td>
                                            <td>₦{{ subtotal.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</td>
                                        </tr>
                                        
                                        <tr>
                                            <td>Shipping:</td>
                                            <td>₦{{ checkoutForm.shipping_fee.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</td>
                                        </tr>
                                        
                                        <tr v-if="appliedDiscount && discountAmount > 0" class="text-success">
                                            <td>Discount ({{ appliedDiscount.code }}):</td>
                                            <td>-₦{{ discountAmount.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</td>
                                        </tr>
                                        
                                        <tr class="summary-total">
                                            <td>Total:</td>
                                            <td>₦{{ total.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="mt-3">
                                    <h4 class="mb-3">Payment Method</h4>
                                    
                                    <button type="button" @click="handleSubmit('paystack')" class="btn btn-outline-primary-2 btn-block mb-2">
                                        <span class="btn-text">Pay with Paystack</span>
                                    </button>
                                    
                                    <button type="button" @click="handleSubmit('whatsapp')" class="btn btn-outline-success-2 btn-block">
                                        <span class="btn-text">Order via WhatsApp</span>
                                    </button>
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        </div>
    </main>
</Skeleton>
</template>

<style scoped>
.btn-outline-success-2 {
    color: #25D366;
    border-color: #25D366;
}

.btn-outline-success-2:hover {
    background-color: #25D366;
    border-color: #25D366;
    color: #fff;
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