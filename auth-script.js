document.addEventListener("DOMContentLoaded", () => {
  // Handle role selection for signup page
  const roleButtons = document.querySelectorAll(".btn-role")
  let selectedRole = "passenger" // default

  roleButtons.forEach((button) => {
    button.addEventListener("click", function () {
      // Remove active class from all buttons
      roleButtons.forEach((btn) => btn.classList.remove("active"))

      // Add active class to clicked button
      this.classList.add("active")

      // Update selected role
      selectedRole = this.dataset.role

      console.log("Selected role:", selectedRole)
    })
  })

  // Handle login form submission
  const loginForm = document.getElementById("loginForm")
  if (loginForm) {
    loginForm.addEventListener("submit", (e) => {
      e.preventDefault()

      const email = document.getElementById("email").value
      const password = document.getElementById("password").value
      const remember = document.getElementById("remember").checked

      console.log("Login attempt:", { email, password, remember })

      // Add your login logic here
      alert("Login functionality would be implemented here!")
    })
  }

  // Handle signup form submission
  const signupForm = document.getElementById("signupForm")
  if (signupForm) {
    signupForm.addEventListener("submit", (e) => {
      e.preventDefault()

      const email = document.getElementById("email").value
      const password = document.getElementById("password").value
      const notifications = document.getElementById("notifications").checked

      console.log("Signup attempt:", { email, password, role: selectedRole, notifications })

      // Add your signup logic here
      alert(`Account creation for ${selectedRole} would be implemented here!`)
    })
  }

  // Handle social login buttons
  const socialButtons = document.querySelectorAll(".btn-social")
  socialButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const provider = this.classList.contains("btn-google") ? "Google" : "Apple"
      console.log(`${provider} login clicked`)
      alert(`${provider} authentication would be implemented here!`)
    })
  })

  // Add button click animations
  const buttons = document.querySelectorAll(".btn")
  buttons.forEach((button) => {
    button.addEventListener("click", function () {
      this.style.transform = "scale(0.95)"
      setTimeout(() => {
        this.style.transform = "scale(1)"
      }, 150)
    })
  })

  // Animate form on page load
  const authForm = document.querySelector(".auth-form")
  const authLeft = document.querySelector(".auth-left")

  if (authForm && authLeft) {
    // Set initial state
    authForm.style.opacity = "0"
    authForm.style.transform = "translateX(20px)"
    authLeft.style.opacity = "0"
    authLeft.style.transform = "translateX(-20px)"

    // Animate after a delay
    setTimeout(() => {
      authForm.style.transition = "opacity 0.8s ease, transform 0.8s ease"
      authLeft.style.transition = "opacity 0.8s ease, transform 0.8s ease"

      authForm.style.opacity = "1"
      authForm.style.transform = "translateX(0)"
      authLeft.style.opacity = "1"
      authLeft.style.transform = "translateX(0)"
    }, 200)
  }
})

// Password toggle functionality
function togglePassword() {
  const passwordInput = document.getElementById("password")
  const toggleButton = document.querySelector(".password-toggle")

  if (passwordInput.type === "password") {
    passwordInput.type = "text"
    toggleButton.innerHTML = `
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" stroke="currentColor" stroke-width="2" fill="none"/>
        <line x1="1" y1="1" x2="23" y2="23" stroke="currentColor" stroke-width="2"/>
      </svg>
    `
  } else {
    passwordInput.type = "password"
    toggleButton.innerHTML = `
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2" fill="none"/>
        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" fill="none"/>
      </svg>
    `
  }
}
