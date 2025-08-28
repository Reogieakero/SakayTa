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
  const ctaObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("visible")
        ctaObserver.unobserve(entry.target)
      }
    })
  }, { threshold: 0.5 })

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