// --- Tab Switching Logic ---
function switchTab(target) {
    const tabLogin = document.getElementById('tab-login');
    const tabRegister = document.getElementById('tab-register');
    const formLogin = document.getElementById('form-login');
    const formRegister = document.getElementById('form-register');

    if (target === 'login') {
        tabLogin.classList.add('active');
        tabRegister.classList.remove('active');
        formLogin.classList.add('active-form');
        formRegister.classList.remove('active-form');
    } else if (target === 'register') {
        tabRegister.classList.add('active');
        tabLogin.classList.remove('active');
        formRegister.classList.add('active-form');
        formLogin.classList.remove('active-form');
    }
}

// --- Toggle Password Inline Visibility ---
function togglePasswordVisibility(fieldId, iconElement) {
    const passwordInput = document.getElementById(fieldId);
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        iconElement.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        iconElement.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// --- Mock Handler for backend requests ---
function handleAuth(event, type) {
    event.preventDefault();
    
    if (type === 'login') {
        const email = document.getElementById('login-email').value;
        console.log(`Authenticating user: ${email}`);
        // Add backend API fetch logic or routing logic here
        alert('Authentication processed! Redirecting to dashboard...');
    } else if (type === 'register') {
        const email = document.getElementById('reg-email').value;
        console.log(`Registering new user: ${email}`);
        // Add database submission endpoint mapping here
        alert('Registration successful! Account provisioned.');
    }
}