document.querySelectorAll(".btn-role").forEach(btn => {
    btn.addEventListener("click", function() {
        document.querySelector("#role").value = this.dataset.role;

        // Highlight active button
        document.querySelectorAll(".btn-role").forEach(b => b.classList.remove("active"));
        this.classList.add("active");
    });
});


document.addEventListener("DOMContentLoaded", () => {
    // Handle role selection for signup page
    const roleButtons = document.querySelectorAll(".btn-role");
    const roleInput = document.getElementById("role"); // Get the hidden input

    roleButtons.forEach((button) => {
        button.addEventListener("click", function () {
            // Remove active class from all buttons
            roleButtons.forEach((btn) => btn.classList.remove("active"));

            // Add active class to clicked button
            this.classList.add("active");

            // Update the hidden input with the selected role
            roleInput.value = this.dataset.role;

            console.log("Selected role:", roleInput.value);
        });
    });

    // Handle login form submission (assuming this is also in your file)
    const loginForm = document.getElementById("loginForm");
    if (loginForm) {
        loginForm.addEventListener("submit", (e) => {
            e.preventDefault();

            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;
            const remember = document.getElementById("remember").checked;

            console.log("Login attempt:", { email, password, remember });
            // Add your login logic here
            alert("Login functionality would be implemented here!");
        });
    }

    // Handle signup form submission
    const signupForm = document.getElementById("signupForm");
    if (signupForm) {
        signupForm.addEventListener("submit", (e) => {
            e.preventDefault();

            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;
            const role = document.getElementById("role").value;

            console.log("Signup attempt:", { email, password, role });
            
            // Here, the form will be submitted to the register.php script
            // which handles the storage and redirection.
            signupForm.submit();
        });
    }

    // Password toggle functionality
    function togglePassword() {
        const passwordInput = document.getElementById("password");
        const toggleButton = document.querySelector(".password-toggle");

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            toggleButton.innerHTML = `
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.55 18.55 0 0 1-2.91 5.31"/>
                    <path d="M15 12c0 1.66-1.34 3-3 3s-3-1.34-3-3 1.34-3 3-3 3 1.34 3 3z"/>
                    <line x1="1" y1="1" x2="23" y2="23" stroke="currentColor" stroke-width="2"/>
                </svg>
            `;
        } else {
            passwordInput.type = "password";
            toggleButton.innerHTML = `
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2" fill="none"/>
                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" fill="none"/>
                </svg>
            `;
        }
    }
});