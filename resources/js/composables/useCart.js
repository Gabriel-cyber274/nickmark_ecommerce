import { ref, computed } from 'vue';

const CART_STORAGE_KEY = 'guest_cart';

// Global state
const cartItems = ref([]);
const isAuthenticated = ref(false);

export function useCart(auth = null) {
    // Initialize authentication state
    if (auth !== null) {
        isAuthenticated.value = !!auth;
    }

    // Load cart from localStorage
    const loadGuestCart = () => {
        if (!isAuthenticated.value) {
            const stored = localStorage.getItem(CART_STORAGE_KEY);
            if (stored) {
                try {
                    cartItems.value = JSON.parse(stored);
                } catch (e) {
                    cartItems.value = [];
                }
            }
        }
    };

    // Save cart to localStorage
    const saveGuestCart = () => {
        if (!isAuthenticated.value) {
            localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cartItems.value));
        }
    };

    // Sync guest cart to server when user logs in
    const syncCartToServer = async () => {
        const guestCart = localStorage.getItem(CART_STORAGE_KEY);
        console.log('Starting cart sync:', {
            hasGuestCart: !!guestCart,
            isAuthenticated: isAuthenticated.value
        });
        
        if (guestCart && isAuthenticated.value) {
            try {
                const cartData = JSON.parse(guestCart);
                console.log('Cart items to sync:', cartData);
                
                if (cartData.length > 0) {
                    const response = await fetch('/api/cart/sync', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        credentials: 'include',
                        body: JSON.stringify({ items: cartData })
                    });

                    console.log('Sync response status:', response.status);
                    
                    if (response.ok) {
                        const result = await response.json();
                        console.log('Sync successful:', result);
                        
                        // Clear local storage
                        localStorage.removeItem(CART_STORAGE_KEY);
                        
                        // Fetch updated server cart
                        await fetchServerCart();
                        
                        return true;
                    } else {
                        const errorData = await response.json();
                        console.error('Failed to sync cart:', errorData);
                        return false;
                    }
                }
                return true;
            } catch (error) {
                console.error('Error syncing cart:', error);
                return false;
            }
        }
        return true;
    };

    // Fetch cart from server for authenticated users
    const fetchServerCart = async () => {
        if (isAuthenticated.value) {
            try {
                const response = await fetch('/api/cart', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    credentials: 'include'
                });

                if (response.ok) {
                    const data = await response.json();
                    cartItems.value = data;
                } else {
                    console.error('Failed to fetch server cart');
                }
            } catch (error) {
                console.error('Error fetching cart:', error);
            }
        }
    };

    // Add product to cart
    const addToCart = async (productId, quantity = 1) => {
        if (isAuthenticated.value) {
            try {
                const response = await fetch('/api/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    credentials: 'include',
                    body: JSON.stringify({ product_id: productId, quantity })
                });

                const data = await response.json();
                
                if (response.ok) {
                    await fetchServerCart();
                    return data;
                } else {
                    throw new Error(data.message || 'Failed to add to cart');
                }
            } catch (error) {
                console.error('Error adding to cart:', error);
                return addToLocalCart(productId, quantity);
            }
        } else {
            return addToLocalCart(productId, quantity);
        }
    };

    // Helper function for local cart addition
    const addToLocalCart = (productId, quantity) => {
        const existingItem = cartItems.value.find(item => item.product_id === productId);
        
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            cartItems.value.push({ product_id: productId, quantity });
        }
        
        saveGuestCart();
        
        return { 
            message: 'Added to cart',
            success: true
        };
    };

    // Update cart item quantity
    const updateQuantity = async (productId, quantity) => {
        if (quantity <= 0) {
            return removeFromCart(productId);
        }

        if (isAuthenticated.value) {
            try {
                const response = await fetch('/api/cart/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    credentials: 'include',
                    body: JSON.stringify({ product_id: productId, quantity })
                });

                const data = await response.json();
                
                if (response.ok) {
                    await fetchServerCart();
                    return data;
                } else {
                    throw new Error(data.message || 'Failed to update cart');
                }
            } catch (error) {
                console.error('Error updating cart:', error);
                return updateLocalQuantity(productId, quantity);
            }
        } else {
            return updateLocalQuantity(productId, quantity);
        }
    };

    // Helper function for local quantity update
    const updateLocalQuantity = (productId, quantity) => {
        const item = cartItems.value.find(item => item.product_id === productId);
        
        if (item) {
            item.quantity = quantity;
            saveGuestCart();
        }
        
        return { 
            message: 'Cart updated',
            success: true
        };
    };

    // Remove product from cart
    const removeFromCart = async (productId) => {
        if (isAuthenticated.value) {
            try {
                const response = await fetch('/api/cart/remove', {
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
                    await fetchServerCart();
                    return data;
                } else {
                    throw new Error(data.message || 'Failed to remove from cart');
                }
            } catch (error) {
                console.error('Error removing from cart:', error);
                return removeFromLocalCart(productId);
            }
        } else {
            return removeFromLocalCart(productId);
        }
    };

    // Helper function for local cart removal
    const removeFromLocalCart = (productId) => {
        cartItems.value = cartItems.value.filter(item => item.product_id !== productId);
        saveGuestCart();
        
        return { 
            message: 'Removed from cart',
            success: true
        };
    };

    // Get cart item
    const getCartItem = (productId) => {
        return cartItems.value.find(item => item.product_id === productId);
    };

    // Check if product is in cart
    const isInCart = (productId) => {
        return cartItems.value.some(item => item.product_id === productId);
    };

    // Get cart count (total items)
    const cartCount = computed(() => {
        return cartItems.value.reduce((total, item) => total + item.quantity, 0);
    });

    // Get cart total
    const cartTotal = computed(() => {
        return cartItems.value.reduce((total, item) => {
            const price = item.product?.price || 0;
            return total + (price * item.quantity);
        }, 0);
    });

    // Initialize
    if (isAuthenticated.value) {
        fetchServerCart();
    } else {
        loadGuestCart();
    }

    return {
        cartItems,
        cartCount,
        cartTotal,
        addToCart,
        updateQuantity,
        removeFromCart,
        getCartItem,
        isInCart,
        syncCartToServer,
        fetchServerCart
    };
}