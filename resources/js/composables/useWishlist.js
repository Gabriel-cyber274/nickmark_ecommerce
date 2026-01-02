import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const WISHLIST_STORAGE_KEY = 'guest_wishlist';

// Global state
const wishlistItems = ref([]);
const isAuthenticated = ref(false);

export function useWishlist(auth = null) {
    // Initialize authentication state
    if (auth !== null) {
        isAuthenticated.value = !!auth;
    }

    // Load wishlist from localStorage on mount
    const loadGuestWishlist = () => {
        if (!isAuthenticated.value) {
            const stored = localStorage.getItem(WISHLIST_STORAGE_KEY);
            if (stored) {
                try {
                    wishlistItems.value = JSON.parse(stored);
                } catch (e) {
                    wishlistItems.value = [];
                }
            }
        }
    };

    // Save wishlist to localStorage
    const saveGuestWishlist = () => {
        if (!isAuthenticated.value) {
            localStorage.setItem(WISHLIST_STORAGE_KEY, JSON.stringify(wishlistItems.value));
        }
    };

    // Sync guest wishlist to server when user logs in
    const syncWishlistToServer = async () => {
        const guestWishlist = localStorage.getItem(WISHLIST_STORAGE_KEY);
        console.log('Starting wishlist sync:', {
            hasGuestWishlist: !!guestWishlist,
            isAuthenticated: isAuthenticated.value,
            guestWishlist: guestWishlist
        });
        
        if (guestWishlist && isAuthenticated.value) {
            try {
                const productIds = JSON.parse(guestWishlist);
                console.log('Product IDs to sync:', productIds);
                
                if (productIds.length > 0) {
                    const response = await fetch('/api/wishlist/sync', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        credentials: 'include',
                        body: JSON.stringify({ product_ids: productIds })
                    });

                    console.log('Sync response status:', response.status);
                    
                    if (response.ok) {
                        const result = await response.json();
                        console.log('Sync successful:', result);
                        
                        // Clear local storage
                        localStorage.removeItem(WISHLIST_STORAGE_KEY);
                        
                        // Fetch updated server wishlist
                        await fetchServerWishlist();
                        
                        return true;
                    } else {
                        const errorData = await response.json();
                        console.error('Failed to sync wishlist:', errorData);
                        return false;
                    }
                }
                console.log('No items to sync');
                return true;
            } catch (error) {
                console.error('Error syncing wishlist:', error);
                return false;
            }
        }
        console.log('Nothing to sync - no guest wishlist or not authenticated');
        return true;
    };

    // Fetch wishlist from server for authenticated users
    const fetchServerWishlist = async () => {
        if (isAuthenticated.value) {
            try {
                const response = await fetch('/api/wishlist', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    credentials: 'include'
                });

                if (response.ok) {
                    const data = await response.json();
                    wishlistItems.value = data.map(item => item.product_id);
                } else {
                    console.error('Failed to fetch server wishlist');
                }
            } catch (error) {
                console.error('Error fetching wishlist:', error);
            }
        }
    };

    // Toggle product in wishlist
    const toggleWishlist = async (productId) => {
        if (isAuthenticated.value) {
            // For authenticated users, use API
            try {
                const response = await fetch('/api/wishlist/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    credentials: 'include',
                    body: JSON.stringify({ product_id: productId })
                });

                const data = await response.json();
                
                if (response.ok) {
                    // Update local state based on response
                    if (data.inWishlist) {
                        if (!wishlistItems.value.includes(productId)) {
                            wishlistItems.value.push(productId);
                        }
                    } else {
                        wishlistItems.value = wishlistItems.value.filter(id => id !== productId);
                    }
                    return data;
                } else {
                    if (response.status === 401) {
                        isAuthenticated.value = false;
                        return toggleLocalWishlist(productId);
                    }
                    throw new Error(data.message || 'Failed to toggle wishlist');
                }
            } catch (error) {
                console.error('Error toggling wishlist:', error);
                isAuthenticated.value = false;
                return toggleLocalWishlist(productId);
            }
        } else {
            return toggleLocalWishlist(productId);
        }
    };

    // Helper function for local wishlist toggle
    const toggleLocalWishlist = (productId) => {
        const index = wishlistItems.value.indexOf(productId);
        let inWishlist;
        
        if (index > -1) {
            wishlistItems.value.splice(index, 1);
            inWishlist = false;
        } else {
            wishlistItems.value.push(productId);
            inWishlist = true;
        }
        
        saveGuestWishlist();
        
        return { 
            message: inWishlist ? 'Added to wishlist' : 'Removed from wishlist',
            inWishlist: inWishlist
        };
    };

    // Check if product is in wishlist
    const isInWishlist = (productId) => {
        return wishlistItems.value.includes(productId);
    };

    // Get wishlist count
    const wishlistCount = computed(() => wishlistItems.value.length);

    // Initialize
    if (isAuthenticated.value) {
        fetchServerWishlist();
    } else {
        loadGuestWishlist();
    }

    return {
        wishlistItems,
        wishlistCount,
        toggleWishlist,
        isInWishlist,
        syncWishlistToServer,
        fetchServerWishlist
    };
}