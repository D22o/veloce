const inputs = document.querySelectorAll('.otp-box');

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

function moveFocus(current, nextIndex) {
    if (current.value.length >= 1 && nextIndex < inputs.length) {
        inputs[nextIndex].focus();
    }
}

function handleBackspace(current, prevIndex) {
    if (event.key === "Backspace" && current.value.length === 0 && prevIndex >= 0) {
        inputs[prevIndex].focus();
    }
}