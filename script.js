// Smooth scrolling for navigation
document.addEventListener("DOMContentLoaded", () => {
  // Add smooth scroll behavior to buttons
  const buttons = document.querySelectorAll(".btn")

  buttons.forEach((button) => {
    button.addEventListener("click", function (e) {
      // Add click animation
      this.style.transform = "scale(0.95)"
      setTimeout(() => {
        this.style.transform = "scale(1)"
      }, 150)

      // Handle different button actions
      const buttonText = this.textContent.trim()

      switch (buttonText) {
        case "Login":
          console.log("Login clicked")
          // Add login functionality here
          break
        case "Sign Up":
          console.log("Sign Up clicked")
          // Add sign up functionality here
          break
        case "Get Started":
          console.log("Get Started clicked")
          // Add passenger registration functionality here
          break
        case "Drive Now":
          console.log("Drive Now clicked")
          // Add driver registration functionality here
          break
        case "Admin Panel":
          console.log("Admin Panel clicked")
          // Add admin panel access here
          break
        case "Book Your First Ride":
          console.log("Book Your First Ride clicked")
          // Add ride booking functionality here
          break
        case "Become a Driver":
          console.log("Become a Driver clicked")
          // Add driver registration functionality here
          break
      }
    })
  })

  // Add scroll animations for cards and stats
  const observerOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -50px 0px",
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = "1"
        entry.target.style.transform = "translateY(0)"
      }
    })
  }, observerOptions)

  // Observe stats and service cards for animation
  const animatedElements = document.querySelectorAll(".stat-item, .service-card")
  animatedElements.forEach((el) => {
    el.style.opacity = "0"
    el.style.transform = "translateY(20px)"
    el.style.transition = "opacity 0.6s ease, transform 0.6s ease"
    observer.observe(el)
  })

  // New: Animate the hero title and subtitle on page load
  const heroTitle = document.querySelector(".hero-title")
  const heroSubtitle = document.querySelector(".hero-subtitle")

  if (heroTitle && heroSubtitle) {
    // Set initial state for animation
    heroTitle.style.opacity = "0"
    heroTitle.style.transform = "translateY(-20px)"
    heroSubtitle.style.opacity = "0"
    heroSubtitle.style.transform = "translateY(20px)"

    // Animate after a small delay
    setTimeout(() => {
      heroTitle.style.transition = "opacity 0.8s ease, transform 0.8s ease"
      heroSubtitle.style.transition = "opacity 0.8s ease 0.4s, transform 0.8s ease 0.4s" // Delay subtitle animation

      heroTitle.style.opacity = "1"
      heroTitle.style.transform = "translateY(0)"
      heroSubtitle.style.opacity = "1"
      heroSubtitle.style.transform = "translateY(0)"
    }, 200)
  }

  // New: Animate the CTA section when it comes into view
  const ctaSection = document.querySelector(".cta-content")
  const ctaObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("visible")
          ctaObserver.unobserve(entry.target)
        }
      })
    },
    { threshold: 0.5 },
  )

  if (ctaSection) {
    ctaObserver.observe(ctaSection)
  }

  // Counter animation for stats
  function animateCounter(element, target) {
    let current = 0
    const increment = target / 100
    const timer = setInterval(() => {
      current += increment
      if (current >= target) {
        current = target
        clearInterval(timer)
      }

      if (target === 4.8) {
        element.textContent = current.toFixed(1)
      } else {
        element.textContent = Math.floor(current) + (target >= 1000 ? "k +" : " +")
      }
    }, 20)
  }

  // Trigger counter animation when stats section is visible
  const statsObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const statNumbers = entry.target.querySelectorAll(".stat-number")
          statNumbers.forEach((stat) => {
            const text = stat.textContent
            let target

            if (text.includes("3.5k")) target = 3500
            else if (text.includes("200")) target = 200
            else if (text.includes("10k")) target = 10000
            else if (text.includes("4.8")) target = 4.8

            if (target) {
              animateCounter(stat, target)
            }
          })
          statsObserver.unobserve(entry.target)
        }
      })
    },
    { threshold: 0.5 },
  )

  const statsSection = document.querySelector(".stats")
  if (statsSection) {
    statsObserver.observe(statsSection)
  }

  function createDynamicCar() {
    const carsContainer = document.querySelector(".moving-cars-container")
    const car = document.createElement("div")
    car.className = "car dynamic-car"

    // Random car colors and styles
    const colors = ["#4A90E2", "#FF8C00", "#4CAF50", "#9C27B0", "#F44336"]
    const randomColor = colors[Math.floor(Math.random() * colors.length)]

    car.innerHTML = `
      <svg width="50" height="25" viewBox="0 0 50 25" fill="none">
        <rect x="8" y="12" width="34" height="6" rx="3" fill="${randomColor}"/>
        <rect x="12" y="8" width="20" height="6" rx="2" fill="${randomColor}AA"/>
        <circle cx="16" cy="20" r="2.5" fill="#333"/>
        <circle cx="34" cy="20" r="2.5" fill="#333"/>
        <rect x="38" y="14" width="6" height="3" rx="1.5" fill="#FFD700"/>
      </svg>
    `

    // Random positioning and animation
    const topPosition = Math.random() * 60 + 20 // Between 20% and 80%
    const duration = Math.random() * 10 + 15 // Between 15s and 25s
    const direction = Math.random() > 0.5 ? "left" : "right"

    car.style.top = topPosition + "%"
    car.style.opacity = "0.6"

    if (direction === "left") {
      car.style.left = "-100px"
      car.style.animation = `moveDynamicCarLeft ${duration}s linear`
    } else {
      car.style.right = "-100px"
      car.style.animation = `moveDynamicCarRight ${duration}s linear`
      car.style.transform = "scaleX(-1)"
    }

    carsContainer.appendChild(car)

    // Remove car after animation completes
    setTimeout(() => {
      if (car.parentNode) {
        car.parentNode.removeChild(car)
      }
    }, duration * 1000)
  }

  const style = document.createElement("style")
  style.textContent = `
    @keyframes moveDynamicCarLeft {
      0% { left: -100px; transform: translateY(0) rotate(0deg); }
      25% { transform: translateY(-5px) rotate(0.5deg); }
      50% { transform: translateY(3px) rotate(-0.3deg); }
      75% { transform: translateY(-2px) rotate(0.2deg); }
      100% { left: calc(100% + 100px); transform: translateY(0) rotate(0deg); }
    }
    
    @keyframes moveDynamicCarRight {
      0% { right: -100px; transform: translateY(0) scaleX(-1); }
      25% { transform: translateY(-5px) scaleX(-1) rotate(-0.5deg); }
      50% { transform: translateY(3px) scaleX(-1) rotate(0.3deg); }
      75% { transform: translateY(-2px) scaleX(-1) rotate(-0.2deg); }
      100% { right: calc(100% + 100px); transform: translateY(0) scaleX(-1); }
    }
  `
  document.head.appendChild(style)

  function spawnRandomCar() {
    createDynamicCar()
    // Schedule next car spawn between 3-8 seconds
    const nextSpawn = Math.random() * 5000 + 3000
    setTimeout(spawnRandomCar, nextSpawn)
  }

  // Start spawning cars after initial page load
  setTimeout(spawnRandomCar, 2000)

  const serviceCards = document.querySelectorAll(".service-card")
  serviceCards.forEach((card) => {
    card.addEventListener("mouseenter", () => {
      // Add a subtle "engine start" visual effect
      card.style.boxShadow = "0 20px 40px rgba(74, 144, 226, 0.3)"
    })

    card.addEventListener("mouseleave", () => {
      card.style.boxShadow = ""
    })
  })
})

// Add parallax effect to hero background
window.addEventListener("scroll", () => {
  const scrolled = window.pageYOffset
  const heroBackground = document.querySelector(".hero-background")
  const ctaBackground = document.querySelector(".cta-background")

  if (heroBackground) {
    heroBackground.style.transform = `translateY(${scrolled * 0.5}px)`
  }

  if (ctaBackground) {
    ctaBackground.style.transform = `translateY(${scrolled * 0.1}px)`
  }
})
