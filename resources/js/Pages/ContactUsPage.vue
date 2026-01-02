<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { onMounted, nextTick, onUnmounted, ref, reactive } from 'vue';
import Skeleton from './Skeleton.vue'

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    auth: {
        type: Object  
    }
});

// Contact form state
const showSuccessMessage = ref(false);
const successMessage = ref('');
const isSubmitting = ref(false);

const contactForm = reactive({
    name: '',
    email: '',
    phone: '',
    subject: '',
    message: '',
});

const contactFormErrors = reactive({
    name: '',
    email: '',
    phone: '',
    subject: '',
    message: '',
});

// Function to reset contact form
const resetContactForm = () => {
    contactForm.name = '';
    contactForm.email = '';
    contactForm.phone = '';
    contactForm.subject = '';
    contactForm.message = '';
    contactFormErrors.name = '';
    contactFormErrors.email = '';
    contactFormErrors.phone = '';
    contactFormErrors.subject = '';
    contactFormErrors.message = '';
};

// Function to validate email
const isValidEmail = (email) => {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
};

// Function to submit contact form
const submitContactForm = () => {
    // Reset errors
    contactFormErrors.name = '';
    contactFormErrors.email = '';
    contactFormErrors.phone = '';
    contactFormErrors.subject = '';
    contactFormErrors.message = '';

    // Validate form
    let isValid = true;

    if (!contactForm.name.trim()) {
        contactFormErrors.name = 'Name is required';
        isValid = false;
    }

    if (!contactForm.email.trim()) {
        contactFormErrors.email = 'Email is required';
        isValid = false;
    } else if (!isValidEmail(contactForm.email)) {
        contactFormErrors.email = 'Please enter a valid email';
        isValid = false;
    }

    if (!contactForm.message.trim()) {
        contactFormErrors.message = 'Message is required';
        isValid = false;
    }

    if (!isValid) {
        return;
    }

    // Set loading state
    isSubmitting.value = true;

    router.post('/contact-us', {
        name: contactForm.name,
        email: contactForm.email,
        phone: contactForm.phone,
        subject: contactForm.subject,
        message: contactForm.message
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Show success message
            successMessage.value = 'Thank you for contacting us! We will get back to you soon.';
            showSuccessMessage.value = true;
            
            // Reset form
            resetContactForm();
            
            // Reset loading state
            isSubmitting.value = false;
            
            // Hide success message after 5 seconds
            setTimeout(() => {
                showSuccessMessage.value = false;
            }, 5000);
        },
        onError: (errors) => {
            // Handle validation errors from backend
            if (errors.name) contactFormErrors.name = errors.name;
            if (errors.email) contactFormErrors.email = errors.email;
            if (errors.phone) contactFormErrors.phone = errors.phone;
            if (errors.subject) contactFormErrors.subject = errors.subject;
            if (errors.message) contactFormErrors.message = errors.message;
            
            // Reset loading state
            isSubmitting.value = false;
        },
        onFinish: () => {
            // Always reset loading state when request finishes
            isSubmitting.value = false;
        }
    });
};

</script>

<template>
<Skeleton page='contact-us' :auth="auth">
        <main class="main mt-3">
            <div class="container">
	        	<div class="page-header page-header-big text-center" style="background-image: url('assets/images/contact-header-bg.jpg')">
        			<h1 class="page-title text-white">Contact us<span class="text-white">keep in touch with us</span></h1>
	        	</div><!-- End .page-header -->
            </div><!-- End .container -->

            <div class="page-content pb-0">
                <div class="container">
                    <!-- Success Message -->
                    <div v-if="showSuccessMessage" class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ successMessage }}
                        <button type="button" class="close" @click="showSuccessMessage = false" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                	<div class="row">
                		<div class="col-lg-6 mb-2 mb-lg-0">
                			<h2 class="title mb-1">Contact Information</h2><!-- End .title mb-2 -->
                			<p class="mb-3">Vestibulum volutpat, lacus a ultrices sagittis, mi neque euismod dui, eu pulvinar nunc sapien ornare nisl. Phasellus pede arcu, dapibus eu, fermentum et, dapibus sed, urna.</p>
                			<div class="row">
                				<div class="col-sm-7">
                					<div class="contact-info">
                						<h3>The Office</h3>

                						<ul class="contact-list">
                							<li>
                								<i class="icon-map-marker"></i>
	                							70 Washington Square South New York, NY 10012, United States
	                						</li>
                							<li>
                								<i class="icon-phone"></i>
                								<a href="tel:#">+92 423 567</a>
                							</li>
                							<li>
                								<i class="icon-envelope"></i>
                								<a href="mailto:#">info@Molla.com</a>
                							</li>
                						</ul><!-- End .contact-list -->
                					</div><!-- End .contact-info -->
                				</div><!-- End .col-sm-7 -->

                				<div class="col-sm-5">
                					<div class="contact-info">
                						<h3>The Office</h3>

                						<ul class="contact-list">
                							<li>
                								<i class="icon-clock-o"></i>
	                							<span class="text-dark">Monday-Saturday</span> <br>11am-7pm ET
	                						</li>
                							<li>
                								<i class="icon-calendar"></i>
                								<span class="text-dark">Sunday</span> <br>11am-6pm ET
                							</li>
                						</ul><!-- End .contact-list -->
                					</div><!-- End .contact-info -->
                				</div><!-- End .col-sm-5 -->
                			</div><!-- End .row -->
                		</div><!-- End .col-lg-6 -->
                		<div class="col-lg-6">
                			<h2 class="title mb-1">Got Any Questions?</h2><!-- End .title mb-2 -->
                			<p class="mb-2">Use the form below to get in touch with the sales team</p>

                			<form @submit.prevent="submitContactForm" class="contact-form mb-3">
                				<div class="row">
                					<div class="col-sm-6">
                                        <label for="cname" class="sr-only">Name</label>
                						<input 
                                            type="text" 
                                            class="form-control" 
                                            id="cname" 
                                            placeholder="Name *" 
                                            v-model="contactForm.name"
                                            :class="{ 'is-invalid': contactFormErrors.name }"
                                            required>
                                        <div v-if="contactFormErrors.name" class="invalid-feedback">
                                            {{ contactFormErrors.name }}
                                        </div>
                					</div><!-- End .col-sm-6 -->

                					<div class="col-sm-6">
                                        <label for="cemail" class="sr-only">Email</label>
                						<input 
                                            type="email" 
                                            class="form-control" 
                                            id="cemail" 
                                            placeholder="Email *" 
                                            v-model="contactForm.email"
                                            :class="{ 'is-invalid': contactFormErrors.email }"
                                            required>
                                        <div v-if="contactFormErrors.email" class="invalid-feedback">
                                            {{ contactFormErrors.email }}
                                        </div>
                					</div><!-- End .col-sm-6 -->
                				</div><!-- End .row -->

                				<div class="row">
                					<div class="col-sm-6">
                                        <label for="cphone" class="sr-only">Phone</label>
                						<input 
                                            type="tel" 
                                            class="form-control" 
                                            id="cphone" 
                                            required
                                            placeholder="Phone *"
                                            v-model="contactForm.phone"
                                            :class="{ 'is-invalid': contactFormErrors.phone }">
                                        <div v-if="contactFormErrors.phone" class="invalid-feedback">
                                            {{ contactFormErrors.phone }}
                                        </div>
                					</div><!-- End .col-sm-6 -->

                					<div class="col-sm-6">
                                        <label for="csubject" class="sr-only">Subject</label>
                						<input 
                                            type="text" 
                                            class="form-control" 
                                            id="csubject" 
                                            required
                                            placeholder="Subject *"
                                            v-model="contactForm.subject"
                                            :class="{ 'is-invalid': contactFormErrors.subject }">
                                        <div v-if="contactFormErrors.subject" class="invalid-feedback">
                                            {{ contactFormErrors.subject }}
                                        </div>
                					</div><!-- End .col-sm-6 -->
                				</div><!-- End .row -->

                                <label for="cmessage" class="sr-only">Message</label>
                				<textarea 
                                    class="form-control" 
                                    cols="30" 
                                    rows="4" 
                                    id="cmessage" 
                                    required 
                                    placeholder="Message *"
                                    v-model="contactForm.message"
                                    :class="{ 'is-invalid': contactFormErrors.message }"></textarea>
                                <div v-if="contactFormErrors.message" class="invalid-feedback">
                                    {{ contactFormErrors.message }}
                                </div>

                				<button type="submit" class="btn btn-outline-primary-2 btn-minwidth-sm" :disabled="isSubmitting">
                					<span v-if="!isSubmitting">SUBMIT</span>
                					<span v-else>
                						<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
                						SUBMITTING...
                					</span>
            						<i v-if="!isSubmitting" class="icon-long-arrow-right"></i>
                				</button>
                			</form><!-- End .contact-form -->
                		</div><!-- End .col-lg-6 -->
                	</div><!-- End .row -->

                	<hr class="mt-4 mb-5">

                </div><!-- End .container -->
            </div><!-- End .page-content -->
        </main><!-- End .main -->
</Skeleton>
</template>

<style scoped>
.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
    color: #dc3545;
    font-size: 0.875em;
    margin-top: 0.25rem;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
    border-width: 0.15em;
}

.mr-2 {
    margin-right: 0.5rem;
}

button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>