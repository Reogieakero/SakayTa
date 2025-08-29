// Dashboard JavaScript functionality
document.addEventListener("DOMContentLoaded", () => {
  initializeDashboard()
})

function initializeDashboard() {
  // Animate cards on load
  animateCards()

  // Initialize ride options
  initializeRideOptions()

  // Initialize booking form
  initializeBookingForm()

  // Update ETA periodically
  updateETA()
  setInterval(updateETA, 30000) // Update every 30 seconds
}

function animateCards() {
  const cards = document.querySelectorAll("[data-animate]")

  cards.forEach((card, index) => {
    const delay = card.dataset.delay || 0

    setTimeout(() => {
      card.classList.add("animate")
    }, delay)
  })
}

function initializeRideOptions() {
  const optionCards = document.querySelectorAll(".option-card")

  optionCards.forEach((card) => {
    card.addEventListener("click", function () {
      // Remove active class from all cards
      optionCards.forEach((c) => c.classList.remove("active"))

      // Add active class to clicked card
      this.classList.add("active")

      // Add bounce animation
      this.style.animation = "bounceIn 0.5s ease"
      setTimeout(() => {
        this.style.animation = ""
      }, 500)
    })
  })
}

function initializeBookingForm() {
  const form = document.getElementById("bookingForm")
  const submitBtn = form.querySelector(".btn-animated")

  form.addEventListener("submit", (e) => {
    e.preventDefault()

    // Show loading state
    submitBtn.classList.add("loading")

    // Show loading overlay
    showLoadingOverlay()

    // Simulate booking process
    setTimeout(() => {
      submitBtn.classList.remove("loading")
      hideLoadingOverlay()

      // Show success message
      showNotification("Ride booked successfully! Driver is on the way.", "success")

      // Reset form
      form.reset()

      // Update current ride status
      updateCurrentRideStatus()
    }, 3000)
  })
}

function updateETA() {
  const etaElement = document.querySelector(".eta-time")
  if (etaElement) {
    const currentETA = Number.parseInt(etaElement.textContent)
    if (currentETA > 1) {
      etaElement.textContent = `${currentETA - 1} minutes`
    } else {
      etaElement.textContent = "Arriving now"
      etaElement.style.color = "#FF8C00"
    }
  }
}

function updateCurrentRideStatus() {
  const statusBadge = document.querySelector(".status-badge")
  const etaElement = document.querySelector(".eta-time")

  if (statusBadge) {
    statusBadge.textContent = "Driver Assigned"
    statusBadge.className = "status-badge driver-assigned"
  }

  if (etaElement) {
    etaElement.textContent = "8 minutes"
    etaElement.style.color = "#4CAF50"
  }
}

function toggleUserMenu() {
  const dropdown = document.getElementById("userDropdown")
  dropdown.classList.toggle("show")

  // Close dropdown when clicking outside
  document.addEventListener("click", (e) => {
    if (!e.target.closest(".user-menu")) {
      dropdown.classList.remove("show")
    }
  })
}

function showModal(type) {
  const overlay = document.getElementById("modalOverlay")
  overlay.classList.add("show")

  // Create modal content based on type
  let modalContent = ""

  switch (type) {
    case "payment":
      modalContent = createPaymentModal()
      break
    case "history":
      modalContent = createHistoryModal()
      break
    case "preferences":
      modalContent = createPreferencesModal()
      break
    case "support":
      modalContent = createSupportModal()
      break
  }

  // Add modal content to overlay
  overlay.innerHTML = `<div class="modal-content">${modalContent}</div>`

  // Animate modal in
  setTimeout(() => {
    overlay.querySelector(".modal-content").style.transform = "scale(1)"
    overlay.querySelector(".modal-content").style.opacity = "1"
  }, 100)
}

function closeModal() {
  const overlay = document.getElementById("modalOverlay")
  overlay.classList.remove("show")
}

function showLoadingOverlay() {
  const overlay = document.getElementById("loadingOverlay")
  overlay.classList.add("show")
}

function hideLoadingOverlay() {
  const overlay = document.getElementById("loadingOverlay")
  overlay.classList.remove("show")
}

function showNotification(message, type = "info") {
  // Create notification element
  const notification = document.createElement("div")
  notification.className = `notification notification-${type}`
  notification.innerHTML = `
        <div class="notification-content">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `

  // Add styles
  notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === "success" ? "#4CAF50" : type === "error" ? "#f44336" : "#4A90E2"};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        z-index: 10001;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `

  document.body.appendChild(notification)

  // Animate in
  setTimeout(() => {
    notification.style.transform = "translateX(0)"
  }, 100)

  // Auto remove after 5 seconds
  setTimeout(() => {
    notification.style.transform = "translateX(100%)"
    setTimeout(() => notification.remove(), 300)
  }, 5000)
}

function callDriver() {
  showNotification("Calling driver...", "info")
  // Simulate call functionality
}

function messageDriver() {
  showNotification("Opening chat with driver...", "info")
  // Simulate messaging functionality
}

function showRideDetails(rideId) {
  showNotification("Loading ride details...", "info")
  // Simulate showing ride details
}

function showAllRides() {
  showNotification("Loading all rides...", "info")
  // Simulate showing all rides
}

// Modal content creators
function createPaymentModal() {
  return `
        <h3>Payment Methods</h3>
        <div class="payment-methods">
            <div class="payment-method">
                <span>Cash</span>
                <input type="radio" name="payment" value="cash" checked>
            </div>
            <div class="payment-method">
                <span>GCash</span>
                <input type="radio" name="payment" value="gcash">
            </div>
            <div class="payment-method">
                <span>Credit Card</span>
                <input type="radio" name="payment" value="card">
            </div>
        </div>
        <button class="btn btn-primary" onclick="closeModal()">Save</button>
    `
}

function createHistoryModal() {
  return `
        <h3>Ride History</h3>
        <div class="history-list">
            <div class="history-item">
                <span>DORSU → Don Luis</span>
                <span>₱220</span>
            </div>
            <div class="history-item">
                <span>Matiag → Novo</span>
                <span>₱220</span>
            </div>
        </div>
        <button class="btn btn-primary" onclick="closeModal()">Close</button>
    `
}

function createPreferencesModal() {
  return `
        <h3>Preferences</h3>
        <div class="preferences">
            <label>
                <input type="checkbox" checked> SMS Notifications
            </label>
            <label>
                <input type="checkbox" checked> Email Updates
            </label>
            <label>
                <input type="checkbox"> Push Notifications
            </label>
        </div>
        <button class="btn btn-primary" onclick="closeModal()">Save</button>
    `
}

function createSupportModal() {
  return `
        <h3>Support</h3>
        <div class="support-options">
            <button class="support-btn">Report an Issue</button>
            <button class="support-btn">Contact Support</button>
            <button class="support-btn">FAQ</button>
        </div>
        <button class="btn btn-primary" onclick="closeModal()">Close</button>
    `
}

// Add CSS for modal content
const modalStyles = `
    .modal-content {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        max-width: 500px;
        margin: 50px auto;
        transform: scale(0.8);
        opacity: 0;
        transition: all 0.3s ease;
    }
    
    .modal-content h3 {
        margin-bottom: 1.5rem;
        color: #333;
    }
    
    .payment-methods, .history-list, .preferences, .support-options {
        margin-bottom: 1.5rem;
    }
    
    .payment-method, .history-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .preferences label {
        display: block;
        margin-bottom: 0.75rem;
    }
    
    .support-btn {
        display: block;
        width: 100%;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        background: #f8f9fa;
        border: 1px solid #e1e5e9;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .support-btn:hover {
        background: #4A90E2;
        color: white;
    }
`

// Inject modal styles
const styleSheet = document.createElement("style")
styleSheet.textContent = modalStyles
document.head.appendChild(styleSheet)
