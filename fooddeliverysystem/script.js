// Global variables
let currentUser = null;
let cart = [];

// API endpoints
const API_URL = window.location.origin + '/fooddeliverysystem/api';

// Utility functions
function showMessage(message, type = 'info') {
    const messageDiv = document.getElementById('message');
    messageDiv.textContent = message;
    messageDiv.className = `alert alert-${type}`;
    messageDiv.style.display = 'block';
    setTimeout(() => {
        messageDiv.style.display = 'none';
    }, 3000);
}

// Authentication functions
async function register(event) {
    event.preventDefault();
    
    const formData = {
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        password: document.getElementById('password').value,
        address: document.getElementById('address').value,
        phone: document.getElementById('phone').value
    };

    try {
        console.log('API URL:', API_URL);
        console.log('Sending registration data:', formData);
        
        const response = await fetch(`${API_URL}/register.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        console.log('Response status:', response.status);
        const data = await response.json();
        console.log('Registration response:', data);

        if (response.ok) {
            showMessage('Registration successful! Please login.', 'success');
            window.location.href = 'login.html';
        } else {
            showMessage(data.message + (data.error ? `: ${data.error}` : ''), 'danger');
        }
    } catch (error) {
        console.error('Registration error:', error);
        showMessage('An error occurred during registration: ' + error.message, 'danger');
    }
}

async function login(event) {
    event.preventDefault();
    
    const formData = {
        email: document.getElementById('email').value,
        password: document.getElementById('password').value
    };

    try {
        const response = await fetch(`${API_URL}/login.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (response.ok) {
            currentUser = data.user;
            localStorage.setItem('currentUser', JSON.stringify(currentUser));
            showMessage('Login successful!', 'success');
            window.location.href = 'index.html';
        } else {
            showMessage(data.message, 'danger');
        }
    } catch (error) {
        showMessage('An error occurred during login.', 'danger');
    }
}

function logout() {
    currentUser = null;
    localStorage.removeItem('currentUser');
    window.location.href = 'login.html';
}

// Cart functions
function updateCart() {
    const dishes = document.querySelectorAll('.dish');
    cart = [];
    
    dishes.forEach(dish => {
        const checkbox = dish.querySelector('.select-dish');
        const quantityInput = dish.querySelector('.quantity');
        const name = dish.querySelector('p').textContent;
        const price = parseFloat(dish.querySelector('.price').textContent.replace('₹', ''));
        const dishId = parseInt(dish.getAttribute('data-id'));
        
        if (checkbox.checked && quantityInput.value > 0) {
            cart.push({
                dish_id: dishId,
                name: name,
                price: price,
                quantity: parseInt(quantityInput.value)
            });
        }
    });
    
    console.log('Updated cart:', cart); // Debug log
    updateCartDisplay();
}

function updateCartDisplay() {
    const cartItems = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    
    if (!cartItems || !cartTotal) {
        console.error('Cart elements not found');
        return;
    }
    
    cartItems.innerHTML = '';
    let total = 0;
    
    cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        
        cartItems.innerHTML += `
            <div class="cart-item">
                <span>${item.name} x ${item.quantity}</span>
                <span>₹${itemTotal}</span>
                <button onclick="removeFromCart(${index})" class="btn btn-sm btn-danger">Remove</button>
            </div>
        `;
    });
    
    cartTotal.textContent = total;
}

function removeFromCart(index) {
    cart.splice(index, 1);
    updateCartDisplay();
}

// Order functions
function goToPayment() {
    console.log('Attempting to go to payment. User:', currentUser, 'Cart:', cart);
    
    if (!currentUser) {
        showMessage('Please login to proceed with payment.', 'warning');
        return;
    }
    
    if (cart.length === 0) {
        showMessage('Your cart is empty!', 'warning');
        return;
    }
    
    const mainSection = document.querySelector('main');
    const paymentSection = document.getElementById('paymentSection');
    
    if (!mainSection || !paymentSection) {
        console.error('Required sections not found:', { mainSection, paymentSection });
        showMessage('Error: Required page elements not found', 'danger');
        return;
    }
    
    // Show payment section
    mainSection.style.display = 'none';
    paymentSection.style.display = 'block';
    
    // Update order summary
    updateOrderSummary();
}

function showCOD() {
    document.getElementById('bankDetails').style.display = 'none';
}

function showOnline() {
    document.getElementById('bankDetails').style.display = 'block';
}

function updateOrderSummary() {
    const orderItems = document.getElementById('orderItems');
    const orderTotal = document.getElementById('orderTotal');
    
    orderItems.innerHTML = '';
    let total = 0;
    
    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        
        orderItems.innerHTML += `
            <div class="order-item">
                <span>${item.name} x ${item.quantity}</span>
                <span>₹${itemTotal}</span>
            </div>
        `;
    });
    
    orderTotal.textContent = total;
}

async function placeOrder() {
    if (!currentUser) {
        showMessage('Please login to place an order.', 'warning');
        return;
    }
    
    if (cart.length === 0) {
        showMessage('Your cart is empty!', 'warning');
        return;
    }
    
    const paymentMethod = document.querySelector('input[name="payment"]:checked')?.value;
    if (!paymentMethod) {
        showMessage('Please select a payment method.', 'warning');
        return;
    }
    
    if (paymentMethod === 'online') {
        const bankName = document.querySelector('#bankDetails input[placeholder="Bank Name"]').value;
        const accountNumber = document.querySelector('#bankDetails input[placeholder="Account Number"]').value;
        const ifscCode = document.querySelector('#bankDetails input[placeholder="IFSC Code"]').value;
        
        if (!bankName || !accountNumber || !ifscCode) {
            showMessage('Please fill in all bank details.', 'warning');
            return;
        }
    }
    
    const totalAmount = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    
    const orderData = {
        user_id: currentUser.id,
        items: cart.map(item => ({
            dish_id: parseInt(item.dish_id),
            quantity: parseInt(item.quantity),
            price: parseFloat(item.price)
        })),
        total_amount: totalAmount,
        payment_method: paymentMethod
    };
    
    console.log('Sending order data:', orderData); // Debug log
    
    try {
        const response = await fetch(`${API_URL}/orders.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(orderData)
        });
        
        const data = await response.json();
        console.log('Order response:', data); // Debug log
        
        if (response.ok) {
            // Show confirmation section
            document.getElementById('paymentSection').style.display = 'none';
            document.getElementById('confirmation').style.display = 'block';
            document.getElementById('orderId').textContent = data.order_id;
            
            // Clear cart
            cart = [];
            updateCartDisplay();
        } else {
            showMessage(data.message || 'Failed to place order.', 'danger');
        }
    } catch (error) {
        console.error('Order error:', error);
        showMessage('An error occurred while placing the order.', 'danger');
    }
}

// Load dishes from API
async function loadDishes() {
    try {
        const response = await fetch(`${API_URL}/dishes.php`);
        const dishes = await response.json();
        
        if (response.ok) {
            const menuContainer = document.getElementById('menu');
            menuContainer.innerHTML = '';
            
            const categories = {};
            
            dishes.forEach(dish => {
                if (!categories[dish.category]) {
                    categories[dish.category] = [];
                }
                categories[dish.category].push(dish);
            });
            
            for (const [category, items] of Object.entries(categories)) {
                const categorySection = document.createElement('div');
                categorySection.className = 'menu-section';
                categorySection.innerHTML = `
                    <h2>${category}</h2>
                    <div class="menu-items">
                        ${items.map(item => `
                            <div class="menu-item">
                                <img src="${item.image}" alt="${item.name}">
                                <h3>${item.name}</h3>
                                <p>${item.description}</p>
                                <p class="price">₹${item.price}</p>
                                <button onclick="addToCart(${item.id}, '${item.name}', ${item.price})" class="btn btn-primary">Add to Cart</button>
                            </div>
                        `).join('')}
                    </div>
                `;
                menuContainer.appendChild(categorySection);
            }
        } else {
            showMessage('Failed to load menu items.', 'danger');
        }
    } catch (error) {
        showMessage('An error occurred while loading the menu.', 'danger');
    }
}

// Delete user function
async function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        return;
    }

    try {
        const response = await fetch(`${API_URL}/delete_user.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                user_id: userId
            })
        });

        const data = await response.json();

        if (data.success) {
            showMessage('User deleted successfully', 'success');
            // If the deleted user is the current user, log them out
            if (currentUser && currentUser.id === userId) {
                logout();
            } else {
                // Refresh the page or update the user list if you have one
                window.location.reload();
            }
        } else {
            showMessage(data.message || 'Failed to delete user', 'danger');
        }
    } catch (error) {
        console.error('Delete user error:', error);
        showMessage('An error occurred while deleting the user', 'danger');
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM Content Loaded');
    
    // Check if user is logged in
    const storedUser = localStorage.getItem('currentUser');
    if (storedUser) {
        try {
            currentUser = JSON.parse(storedUser);
            console.log('Current user:', currentUser);
            
            const userWelcome = document.getElementById('userWelcome');
            const logoutBtn = document.getElementById('logoutBtn');
            
            if (userWelcome) {
                userWelcome.textContent = `Welcome, ${currentUser.name}`;
            }
            
            if (logoutBtn) {
                logoutBtn.style.display = 'inline-block';
            }
        } catch (error) {
            console.error('Error parsing stored user:', error);
            localStorage.removeItem('currentUser');
        }
    }
    
    // Load dishes if on main page
    if (document.getElementById('menu')) {
        loadDishes();
    }
    
    // Add event listeners for cart updates
    document.querySelectorAll('.quantity').forEach(input => {
        input.addEventListener('change', updateCart);
    });

    document.querySelectorAll('.select-dish').forEach(checkbox => {
        checkbox.addEventListener('change', updateCart);
    });

    // Initialize cart
    updateCart();
    
    // Add event listeners for forms
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', register);
    }
    
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', login);
    }
    
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', logout);
    }
});