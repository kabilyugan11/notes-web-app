// This file handles user authentication, including login and registration processes, and manages session storage.

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(loginForm);
            fetch('/api/endpoints/auth/login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    sessionStorage.setItem('user', JSON.stringify(data.user));
                    window.location.href = 'dashboard.html';
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }

    if (registerForm) {
        registerForm.addEventListener('submit', function(event) {
            // Prevent the default form submission
            event.preventDefault();
            console.log('Register form submission intercepted');
            
            // Create FormData object from form
            const formData = new FormData(registerForm);
            
            // Log the data being sent
            console.log('Form data being submitted:');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            
            // Make fetch request
            fetch('/api/endpoints/auth/register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Server response:', data);
                if (data.success) {
                    alert('Registration successful! You can now log in.');
                    window.location.href = 'login.html';
                } else {
                    alert('Registration failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error during registration:', error);
                alert('An error occurred during registration. Please try again.');
            });
        });
    }

    const logoutButton = document.getElementById('logoutButton');
    if (logoutButton) {
        logoutButton.addEventListener('click', function() {
            fetch('/api/endpoints/auth/logout.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    sessionStorage.removeItem('user');
                    window.location.href = 'login.html';
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }
});