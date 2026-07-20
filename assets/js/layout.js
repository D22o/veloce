function toggleSidebar() {
    document.getElementById('dashboardSidebar').classList.toggle('open');
}

// Modal Operations
function openLogoutModal(event) {
    event.preventDefault(); // Prevents jump behavior on '#' link click
    document.getElementById('logoutModal').classList.add('show');
}

function closeLogoutModal() {
    document.getElementById('logoutModal').classList.remove('show');
}

// Close modal if clicking outside on the background overlay
function closeLogoutModalOnOverlay(event) {
    if (event.target.id === 'logoutModal') {
        closeLogoutModal();
    }
}

function openDeleteModal(carId, carName) {
    const modal = document.getElementById('deleteConfirmationModal');
    const inputId = document.getElementById('deleteCarId');
    const labelName = document.getElementById('deleteCarName');

    // Populate the modal fields dynamically
    inputId.value = carId;
    labelName.textContent = carName;

    // Display the modal using Flexbox alignment
    modal.style.display = 'flex';
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteConfirmationModal');
    modal.style.display = 'none';
}

// Close the modal cleanly if the user clicks outside the box
window.onclick = function(event) {
    const modal = document.getElementById('deleteConfirmationModal');
    if (event.target === modal) {
        closeDeleteModal();
    }
}