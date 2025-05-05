import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

interface CartItem {
  id: number;
  name: string;
  price: number;
  quantity: number;
  image: string;
}

export const useCartStore = defineStore('cart', () => {
  const items = ref<CartItem[]>([]);

  // Load cart from localStorage on store initialization
  const loadCart = () => {
    const savedCart = localStorage.getItem('cart');
    if (savedCart) {
      items.value = JSON.parse(savedCart);
    }
  };

  // Save cart to localStorage whenever it changes
  const saveCart = () => {
    localStorage.setItem('cart', JSON.stringify(items.value));
  };

  // Add item to cart
  const addItem = (product: { id: number; name: string; final_price: number; thumnail_img: string }) => {
    const existingItem = items.value.find(item => item.id === product.id);

    if (existingItem) {
      existingItem.quantity += 1;
    } else {
      items.value.push({
        id: product.id,
        name: product.name,
        price: product.final_price,
        quantity: 1,
        image: product.thumnail_img
      });
    }
    saveCart();
  };

  // Remove item from cart
  const removeItem = (productId: number) => {
    items.value = items.value.filter(item => item.id !== productId);
    saveCart();
  };

  // Update item quantity
  const updateQuantity = (productId: number, quantity: number) => {
    const item = items.value.find(item => item.id === productId);
    if (item) {
      item.quantity = quantity;
      saveCart();
    }
  };

  // Clear cart
  const clearCart = () => {
    items.value = [];
    saveCart();
  };

  // Computed properties
  const totalItems = computed(() => {
    return items.value.reduce((total, item) => total + item.quantity, 0);
  });

  const totalPrice = computed(() => {
    return items.value.reduce((total, item) => total + (item.price * item.quantity), 0);
  });

  // Initialize cart
  loadCart();

  return {
    items,
    totalItems,
    totalPrice,
    addItem,
    removeItem,
    updateQuantity,
    clearCart
  };
});
