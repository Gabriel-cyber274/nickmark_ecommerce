<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { onMounted, nextTick, onUnmounted, ref, reactive } from 'vue';
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
    googleMapsApiKey: {
        type: String,
        default: ''
    }
});

// Contact form state
const showSuccessMessage = ref(false);
const successMessage = ref('');
const isSubmitting = ref(false);
const mapLoaded = ref(false);

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

// Google Map variables
const map = ref(null);
const marker = ref(null);
const userLocation = reactive({
    lat: null,
    lng: null,
    isAvailable: false
});
const officeLocation = {
    lat: 6.449887516810459, // Approximate coordinates for Berger Yard, Apapa
    lng: 3.318690995033432
};


// Load Google Maps script
const loadGoogleMaps = () => {
    if (window.google && window.google.maps) {
        initMap();
        return;
    }

    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key=${props.googleMapsApiKey}&callback=initMap`;
    script.async = true;
    script.defer = true;
    window.initMap = initMap;
    document.head.appendChild(script);
};

// Initialize Google Map
const initMap = () => {
    const mapContainer = document.getElementById('map');
    if (!mapContainer) return;

    map.value = new google.maps.Map(mapContainer, {
        center: officeLocation,
        zoom: 15,
        mapTypeControl: true,
        streetViewControl: true,
        fullscreenControl: true,
        zoomControl: true,
        styles: [
            {
                featureType: "poi",
                elementType: "labels",
                stylers: [{ visibility: "off" }]
            }
        ]
    });

    // Add marker for office location
    marker.value = new google.maps.Marker({
        position: officeLocation,
        map: map.value,
        title: 'NickMark Technologies Ltd',
        animation: google.maps.Animation.DROP
    });

    // Add info window
    const infoWindow = new google.maps.InfoWindow({
        content: `
            <div style="padding: 10px; max-width: 250px;">
                <h4 style="margin-bottom: 5px; color: #333;">NickMark Technologies Ltd</h4>
                <p style="margin: 5px 0; color: #666; font-size: 14px;">
                    11, Euro 65 Berger Yard,<br>
                    Berger Yard Bus/stop,<br>
                    Apapa Oshodi Exp-way,<br>
                    Apapa, Lagos.
                </p>
                <p style="margin: 5px 0; color: #666; font-size: 14px;">
                    📞 <a href="tel:08062965548">08062965548</a>
                </p>
            </div>
        `
    });

    marker.value.addListener('click', () => {
        infoWindow.open(map.value, marker.value);
    });

    // Try to get user's location
    getUserLocation();
    mapLoaded.value = true;
};

// Get user's current location
const getUserLocation = () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                userLocation.lat = position.coords.latitude;
                userLocation.lng = position.coords.longitude;
                userLocation.isAvailable = true;
                
                // Add user location marker
                const userMarker = new google.maps.Marker({
                    position: { lat: userLocation.lat, lng: userLocation.lng },
                    map: map.value,
                    title: 'Your Location',
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 8,
                        fillColor: '#4285F4',
                        fillOpacity: 1,
                        strokeColor: '#FFFFFF',
                        strokeWeight: 2
                    }
                });

                // Add user info window
                const userInfoWindow = new google.maps.InfoWindow({
                    content: '<div style="padding: 10px;"><strong>Your Location</strong></div>'
                });

                userMarker.addListener('click', () => {
                    userInfoWindow.open(map.value, userMarker);
                });

                // Calculate and display route
                displayRoute();
            },
            (error) => {
                console.log("Geolocation error:", error);
                userLocation.isAvailable = false;
            },
            { 
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            }
        );
    } else {
        userLocation.isAvailable = false;
        console.log("Geolocation is not supported by this browser.");
    }
};

// Display route between user and office
const displayRoute = () => {
    if (!userLocation.isAvailable) return;

    const directionsService = new google.maps.DirectionsService();
    const directionsRenderer = new google.maps.DirectionsRenderer({
        map: map.value,
        suppressMarkers: true,
        polylineOptions: {
            strokeColor: '#4285F4',
            strokeOpacity: 0.8,
            strokeWeight: 5
        }
    });

    const request = {
        origin: { lat: userLocation.lat, lng: userLocation.lng },
        destination: officeLocation,
        travelMode: google.maps.TravelMode.DRIVING
    };

    directionsService.route(request, (result, status) => {
        if (status === 'OK') {
            directionsRenderer.setDirections(result);
            
            // Adjust map bounds to show both locations
            const bounds = new google.maps.LatLngBounds();
            bounds.extend(new google.maps.LatLng(userLocation.lat, userLocation.lng));
            bounds.extend(new google.maps.LatLng(officeLocation.lat, officeLocation.lng));
            map.value.fitBounds(bounds, 80); // Add padding
        }
    });
};

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

onMounted(() => {
    nextTick(() => {
        loadGoogleMaps();
    });
});

onUnmounted(() => {
    if (window.google) {
        delete window.initMap;
    }
});
</script>

<template>
<Skeleton page='contact-us' :auth="auth">
        <main class="main mt-3">
            <div class="container">
                <div class="page-header page-header-big text-center" style="background-image: url('assets/images/contactus.jpg')">
                    <h1 class="page-title text-white">Contact us<span class="text-white">Get in Touch with Our Team</span></h1>
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
                            <h2 class="title mb-1">Contact Information</h2>
                            <p class="mb-3">
                                Welcome to NickMark Technologies Ltd, your trusted source for quality IT gadgets and technology accessories. We offer reliable devices at competitive prices, backed by knowledgeable support to help you choose the right solution. Contact us through any of the channels below.
                            </p>
                            <div class="row">
                                <div class="col-sm-7">
                                    <div class="contact-info">
                                        <h3>Our Office</h3>
                                        <ul class="contact-list">
                                            <li>
                                                <i class="icon-map-marker"></i>
                                                <strong>Headquarters:</strong><br>
                                                11, Euro 65 Berger Yard,<br>
                                                Berger Yard Bus/stop,<br>
                                                Apapa Oshodi Expressway,<br>
                                                Apapa, Lagos, Nigeria.
                                            </li>
                                            <li>
                                                <i class="icon-phone"></i>
                                                <strong>Phone:</strong><br>
                                                <a href="tel:08062965548">08062965548</a><br>
                                                <a href="tel:07044043049">07044043049</a><br>
                                                <a href="tel:09027868088">09027868088</a>
                                            </li>
                                            <li>
                                                <i class="icon-envelope"></i>
                                                <strong>Email:</strong><br>
                                                <a href="mailto:nickmarktechnologiesltd@gmail.com">nickmarktechnologiesltd@gmail.com</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-sm-5">
                                    <div class="contact-info">
                                        <h3>Business Hours</h3>
                                        <ul class="contact-list">
                                            <li>
                                                <i class="icon-clock-o"></i>
                                                <strong>Monday - Saturday:</strong><br>
                                                8:00 AM - 6:00 PM (WAT)<br>
                                                <!-- <small>Technical Support: 24/7 Emergency</small> -->
                                            </li>
                                            <!-- <li>
                                                <i class="icon-calendar"></i>
                                                <strong>Saturday:</strong><br>
                                                9:00 AM - 4:00 PM (WAT)
                                            </li> -->
                                            <li>
                                                <i class="icon-calendar-times-o"></i>
                                                <strong>Sunday:</strong><br>
                                                Closed<br>
                                                <small>Emergency support available</small>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <h2 class="title mb-1">Got Any Questions?</h2>
                            <p class="mb-2">    
                            Use the form below to get in touch with the sales team.
                            </p>

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
                                    </div>

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
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <label for="cphone" class="sr-only">Phone</label>
                                        <input 
                                            type="tel" 
                                            class="form-control" 
                                            id="cphone" 
                                            placeholder="Phone Number *"
                                            v-model="contactForm.phone"
                                            :class="{ 'is-invalid': contactFormErrors.phone }">
                                        <div v-if="contactFormErrors.phone" class="invalid-feedback">
                                            {{ contactFormErrors.phone }}
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="csubject" class="sr-only">Subject</label>
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="csubject" 
                                            placeholder="Subject *"
                                            v-model="contactForm.subject"
                                            :class="{ 'is-invalid': contactFormErrors.subject }">
                                        <div v-if="contactFormErrors.subject" class="invalid-feedback">
                                            {{ contactFormErrors.subject }}
                                        </div>
                                    </div>
                                </div>

                                <label for="cmessage" class="sr-only">Message</label>
                                <textarea 
                                    class="form-control" 
                                    cols="30" 
                                    rows="4" 
                                    id="cmessage" 
                                    placeholder="Your Message *"
                                    v-model="contactForm.message"
                                    :class="{ 'is-invalid': contactFormErrors.message }"></textarea>
                                <div v-if="contactFormErrors.message" class="invalid-feedback">
                                    {{ contactFormErrors.message }}
                                </div>

                                <button type="submit" class="btn btn-outline-primary-2 btn-minwidth-sm" :disabled="isSubmitting">
                                    <span v-if="!isSubmitting">SUBMIT MESSAGE</span>
                                    <span v-else>
                                        <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
                                        SENDING...
                                    </span>
                                    <i v-if="!isSubmitting" class="icon-long-arrow-right"></i>
                                </button>
                            </form>

                        </div>
                    </div>

                    <hr class="mt-4 mb-5">

                    <!-- Google Map Section -->
                    <div class="row">
                        <div class="col-12">
                            <h2 class="title mb-3">Find Our Location</h2>
                            <div class="map-container mb-4">
                                <div id="map" style="height: 400px; width: 100%; border-radius: 8px;"></div>
                            </div>
                            
                        </div>
                    </div>

                </div>
            </div>
        </main>
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

.map-container {
    position: relative;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.card {
    border: 1px solid #eaeaea;
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.card-body {
    padding: 1.5rem;
}

.icon-check-circle {
    color: #28a745;
}

.alert-info {
    background-color: #f8f9fa;
    border-color: #e9ecef;
    color: #495057;
}

.response-info {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 4px solid #007bff;
}
</style>