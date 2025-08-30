class ParticleSystem {
  constructor(canvas) {
    this.canvas = canvas
    this.ctx = canvas.getContext("2d")
    this.particles = []
    this.connections = []
    this.mouse = { x: 0, y: 0 }

    this.resize()
    this.init()
    this.animate()

    window.addEventListener("resize", () => this.resize())
    window.addEventListener("mousemove", (e) => {
      this.mouse.x = e.clientX
      this.mouse.y = e.clientY
    })
  }

  resize() {
    this.canvas.width = window.innerWidth
    this.canvas.height = window.innerHeight
  }

  init() {
    const particleCount = Math.min(150, Math.floor(window.innerWidth / 10))
    this.particles = []

    for (let i = 0; i < particleCount; i++) {
      this.particles.push({
        x: Math.random() * this.canvas.width,
        y: Math.random() * this.canvas.height,
        vx: (Math.random() - 0.5) * 0.5,
        vy: (Math.random() - 0.5) * 0.5,
        size: Math.random() * 2 + 1,
        opacity: Math.random() * 0.5 + 0.2,
        color: this.getRandomColor(),
        pulse: Math.random() * Math.PI * 2,
        pulseSpeed: 0.02 + Math.random() * 0.02,
      })
    }
  }

  getRandomColor() {
    const colors = ["#4A90E2", "#FF8C00", "#4CAF50", "#87CEEB"]
    return colors[Math.floor(Math.random() * colors.length)]
  }

  animate() {
    this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height)

    // Update and draw particles
    this.particles.forEach((particle, i) => {
      // Update position
      particle.x += particle.vx
      particle.y += particle.vy

      // Wrap around edges
      if (particle.x < 0) particle.x = this.canvas.width
      if (particle.x > this.canvas.width) particle.x = 0
      if (particle.y < 0) particle.y = this.canvas.height
      if (particle.y > this.canvas.height) particle.y = 0

      // Update pulse
      particle.pulse += particle.pulseSpeed
      const pulseFactor = Math.sin(particle.pulse) * 0.5 + 0.5

      // Mouse interaction
      const dx = this.mouse.x - particle.x
      const dy = this.mouse.y - particle.y
      const distance = Math.sqrt(dx * dx + dy * dy)

      if (distance < 100) {
        const force = (100 - distance) / 100
        particle.vx += (dx / distance) * force * 0.01
        particle.vy += (dy / distance) * force * 0.01
      }

      // Apply friction
      particle.vx *= 0.99
      particle.vy *= 0.99

      // Draw particle
      this.ctx.save()
      this.ctx.globalAlpha = particle.opacity * pulseFactor
      this.ctx.fillStyle = particle.color
      this.ctx.beginPath()
      this.ctx.arc(particle.x, particle.y, particle.size * (1 + pulseFactor * 0.5), 0, Math.PI * 2)
      this.ctx.fill()

      // Add glow effect
      this.ctx.shadowBlur = 10
      this.ctx.shadowColor = particle.color
      this.ctx.fill()
      this.ctx.restore()

      // Draw connections
      for (let j = i + 1; j < this.particles.length; j++) {
        const other = this.particles[j]
        const dx = particle.x - other.x
        const dy = particle.y - other.y
        const distance = Math.sqrt(dx * dx + dy * dy)

        if (distance < 120) {
          this.ctx.save()
          this.ctx.globalAlpha = ((120 - distance) / 120) * 0.2
          this.ctx.strokeStyle = particle.color
          this.ctx.lineWidth = 0.5
          this.ctx.beginPath()
          this.ctx.moveTo(particle.x, particle.y)
          this.ctx.lineTo(other.x, other.y)
          this.ctx.stroke()
          this.ctx.restore()
        }
      }
    })

    requestAnimationFrame(() => this.animate())
  }
}

// Enhanced generative road system
class GenerativeRoadSystem {
  constructor() {
    this.container = document.querySelector(".generative-roads")
    this.roads = []
    this.init()
  }

  init() {
    setInterval(() => this.createRoad(), 2000 + Math.random() * 3000)
  }

  createRoad() {
    const road = document.createElement("div")
    road.style.position = "absolute"
    road.style.height = "1px"
    road.style.background = `linear-gradient(90deg, transparent, ${this.getRandomColor()}, transparent)`
    road.style.width = Math.random() * 200 + 50 + "px"
    road.style.top = Math.random() * 100 + "%"
    road.style.left = "-200px"
    road.style.opacity = "0.6"
    road.style.boxShadow = `0 0 10px ${this.getRandomColor()}`
    road.style.animation = `generativeRoad ${8 + Math.random() * 4}s linear forwards`

    this.container.appendChild(road)

    setTimeout(() => {
      if (road.parentNode) {
        road.parentNode.removeChild(road)
      }
    }, 12000)
  }

  getRandomColor() {
    const colors = ["rgba(74, 144, 226, 0.4)", "rgba(255, 140, 0, 0.4)", "rgba(76, 175, 80, 0.4)"]
    return colors[Math.floor(Math.random() * colors.length)]
  }
}

class ThreeDGenerativeSystem {
  constructor(canvas) {
    this.canvas = canvas
    this.ctx = canvas.getContext("2d")
    this.shapes = []
    this.time = 0
    this.mouse = { x: 0, y: 0 }

    this.resize()
    this.init()
    this.animate()

    window.addEventListener("resize", () => this.resize())
    window.addEventListener("mousemove", (e) => {
      this.mouse.x = e.clientX
      this.mouse.y = e.clientY
    })
  }

  resize() {
    this.canvas.width = window.innerWidth
    this.canvas.height = window.innerHeight
  }

  init() {
    this.shapes = []
    const shapeCount = 20

    for (let i = 0; i < shapeCount; i++) {
      this.shapes.push({
        x: Math.random() * this.canvas.width,
        y: Math.random() * this.canvas.height,
        z: Math.random() * 500 - 250,
        size: Math.random() * 30 + 10,
        rotationX: Math.random() * Math.PI * 2,
        rotationY: Math.random() * Math.PI * 2,
        rotationZ: Math.random() * Math.PI * 2,
        rotationSpeedX: (Math.random() - 0.5) * 0.02,
        rotationSpeedY: (Math.random() - 0.5) * 0.02,
        rotationSpeedZ: (Math.random() - 0.5) * 0.02,
        color: this.getRandomColor(),
        type: Math.floor(Math.random() * 3), // 0: cube, 1: sphere, 2: pyramid
        opacity: Math.random() * 0.8 + 0.2,
      })
    }
  }

  getRandomColor() {
    const colors = ["#4A90E2", "#FF8C00", "#4CAF50", "#87CEEB", "#9C27B0"]
    return colors[Math.floor(Math.random() * colors.length)]
  }

  project3D(x, y, z) {
    const perspective = 800
    const projectedX = (x * perspective) / (perspective + z) + this.canvas.width / 2
    const projectedY = (y * perspective) / (perspective + z) + this.canvas.height / 2
    const scale = perspective / (perspective + z)
    return { x: projectedX, y: projectedY, scale: scale }
  }

  drawCube(shape) {
    const size = shape.size
    const vertices = [
      [-size / 2, -size / 2, -size / 2],
      [size / 2, -size / 2, -size / 2],
      [size / 2, size / 2, -size / 2],
      [-size / 2, size / 2, -size / 2],
      [-size / 2, -size / 2, size / 2],
      [size / 2, -size / 2, size / 2],
      [size / 2, size / 2, size / 2],
      [-size / 2, size / 2, size / 2],
    ]

    // Apply rotations
    const rotatedVertices = vertices.map((vertex) => {
      let [x, y, z] = vertex

      // Rotate around X axis
      let tempY = y * Math.cos(shape.rotationX) - z * Math.sin(shape.rotationX)
      let tempZ = y * Math.sin(shape.rotationX) + z * Math.cos(shape.rotationX)
      y = tempY
      z = tempZ

      // Rotate around Y axis
      let tempX = x * Math.cos(shape.rotationY) + z * Math.sin(shape.rotationY)
      tempZ = -x * Math.sin(shape.rotationY) + z * Math.cos(shape.rotationY)
      x = tempX
      z = tempZ

      // Rotate around Z axis
      tempX = x * Math.cos(shape.rotationZ) - y * Math.sin(shape.rotationZ)
      tempY = x * Math.sin(shape.rotationZ) + y * Math.cos(shape.rotationZ)
      x = tempX
      y = tempY

      return [x + shape.x - this.canvas.width / 2, y + shape.y - this.canvas.height / 2, z + shape.z]
    })

    // Project to 2D and draw
    const projectedVertices = rotatedVertices.map((vertex) => this.project3D(vertex[0], vertex[1], vertex[2]))

    // Draw cube faces
    const faces = [
      [0, 1, 2, 3],
      [4, 5, 6, 7],
      [0, 1, 5, 4],
      [2, 3, 7, 6],
      [0, 3, 7, 4],
      [1, 2, 6, 5],
    ]

    faces.forEach((face, index) => {
      this.ctx.save()
      this.ctx.globalAlpha = shape.opacity * 0.6
      this.ctx.fillStyle = shape.color
      this.ctx.strokeStyle = shape.color
      this.ctx.lineWidth = 2

      this.ctx.beginPath()
      this.ctx.moveTo(projectedVertices[face[0]].x, projectedVertices[face[0]].y)
      for (let i = 1; i < face.length; i++) {
        this.ctx.lineTo(projectedVertices[face[i]].x, projectedVertices[face[i]].y)
      }
      this.ctx.closePath()

      this.ctx.fill()
      this.ctx.stroke()
      this.ctx.restore()
    })
  }

  drawSphere(shape) {
    const projected = this.project3D(shape.x - this.canvas.width / 2, shape.y - this.canvas.height / 2, shape.z)

    this.ctx.save()
    this.ctx.globalAlpha = shape.opacity
    this.ctx.fillStyle = shape.color
    this.ctx.shadowBlur = 20
    this.ctx.shadowColor = shape.color

    const gradient = this.ctx.createRadialGradient(
      projected.x,
      projected.y,
      0,
      projected.x,
      projected.y,
      shape.size * projected.scale,
    )
    gradient.addColorStop(0, shape.color)
    gradient.addColorStop(1, "transparent")

    this.ctx.fillStyle = gradient
    this.ctx.beginPath()
    this.ctx.arc(projected.x, projected.y, shape.size * projected.scale, 0, Math.PI * 2)
    this.ctx.fill()
    this.ctx.restore()
  }

  drawPyramid(shape) {
    const size = shape.size
    const vertices = [
      [0, -size, 0], // top
      [-size / 2, size / 2, -size / 2], // bottom vertices
      [size / 2, size / 2, -size / 2],
      [size / 2, size / 2, size / 2],
      [-size / 2, size / 2, size / 2],
    ]

    // Apply rotations (same as cube)
    const rotatedVertices = vertices.map((vertex) => {
      let [x, y, z] = vertex

      let tempY = y * Math.cos(shape.rotationX) - z * Math.sin(shape.rotationX)
      let tempZ = y * Math.sin(shape.rotationX) + z * Math.cos(shape.rotationX)
      y = tempY
      z = tempZ

      let tempX = x * Math.cos(shape.rotationY) + z * Math.sin(shape.rotationY)
      tempZ = -x * Math.sin(shape.rotationY) + z * Math.cos(shape.rotationY)
      x = tempX
      z = tempZ

      tempX = x * Math.cos(shape.rotationZ) - y * Math.sin(shape.rotationZ)
      tempY = x * Math.sin(shape.rotationZ) + y * Math.cos(shape.rotationZ)
      x = tempX
      y = tempY

      return [x + shape.x - this.canvas.width / 2, y + shape.y - this.canvas.height / 2, z + shape.z]
    })

    const projectedVertices = rotatedVertices.map((vertex) => this.project3D(vertex[0], vertex[1], vertex[2]))

    // Draw pyramid faces
    const faces = [
      [0, 1, 2],
      [0, 2, 3],
      [0, 3, 4],
      [0, 4, 1],
      [1, 2, 3, 4],
    ]

    faces.forEach((face) => {
      this.ctx.save()
      this.ctx.globalAlpha = shape.opacity * 0.7
      this.ctx.fillStyle = shape.color
      this.ctx.strokeStyle = shape.color
      this.ctx.lineWidth = 1.5

      this.ctx.beginPath()
      this.ctx.moveTo(projectedVertices[face[0]].x, projectedVertices[face[0]].y)
      for (let i = 1; i < face.length; i++) {
        this.ctx.lineTo(projectedVertices[face[i]].x, projectedVertices[face[i]].y)
      }
      this.ctx.closePath()

      this.ctx.fill()
      this.ctx.stroke()
      this.ctx.restore()
    })
  }

  animate() {
    this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height)
    this.time += 0.01

    // Sort shapes by z-depth for proper rendering
    this.shapes.sort((a, b) => b.z - a.z)

    this.shapes.forEach((shape) => {
      // Update rotations
      shape.rotationX += shape.rotationSpeedX
      shape.rotationY += shape.rotationSpeedY
      shape.rotationZ += shape.rotationSpeedZ

      // Gentle floating motion
      shape.y += Math.sin(this.time + shape.x * 0.01) * 0.5
      shape.z += Math.cos(this.time + shape.y * 0.01) * 0.3

      // Mouse interaction
      const dx = this.mouse.x - shape.x
      const dy = this.mouse.y - shape.y
      const distance = Math.sqrt(dx * dx + dy * dy)

      if (distance < 150) {
        const force = (150 - distance) / 150
        shape.rotationSpeedX += (dx / distance) * force * 0.001
        shape.rotationSpeedY += (dy / distance) * force * 0.001
      }

      // Draw based on type
      switch (shape.type) {
        case 0:
          this.drawCube(shape)
          break
        case 1:
          this.drawSphere(shape)
          break
        case 2:
          this.drawPyramid(shape)
          break
      }
    })

    requestAnimationFrame(() => this.animate())
  }
}

class FloatingElements3D {
  constructor() {
    this.container = document.querySelector(".floating-elements")
    this.elements = []
    this.init()
  }

  init() {
    this.createElements()
    setInterval(() => this.createDynamicElement(), 3000)
  }

  createElements() {
    for (let i = 0; i < 15; i++) {
      this.createFloatingElement()
    }
  }

  createFloatingElement() {
    const element = document.createElement("div")
    element.className = "floating-element"

    const size = Math.random() * 30 + 10
    const colors = ["#4A90E2", "#FF8C00", "#4CAF50", "#87CEEB", "#9C27B0"]
    const color = colors[Math.floor(Math.random() * colors.length)]

    element.style.width = size + "px"
    element.style.height = size + "px"
    element.style.left = Math.random() * 100 + "%"
    element.style.top = Math.random() * 100 + "%"
    element.style.background = `linear-gradient(45deg, ${color}, ${color}80)`
    element.style.animationDelay = Math.random() * 8 + "s"
    element.style.animationDuration = Math.random() * 4 + 6 + "s"

    this.container.appendChild(element)
    this.elements.push(element)

    setTimeout(() => {
      if (element.parentNode) {
        element.parentNode.removeChild(element)
        this.elements = this.elements.filter((el) => el !== element)
      }
    }, 15000)
  }

  createDynamicElement() {
    this.createFloatingElement()
  }
}

class ThreeDRoadSystem {
  constructor() {
    this.container = document.querySelector(".three-d-roads")
    this.init()
  }

  init() {
    setInterval(() => this.create3DRoad(), 2500 + Math.random() * 2000)
  }

  create3DRoad() {
    const road = document.createElement("div")
    road.className = "road-3d"

    const colors = ["rgba(74, 144, 226, 0.8)", "rgba(255, 140, 0, 0.8)", "rgba(76, 175, 80, 0.8)"]
    const color = colors[Math.floor(Math.random() * colors.length)]

    road.style.background = `linear-gradient(90deg, transparent, ${color}, transparent)`
    road.style.top = Math.random() * 80 + 10 + "%"
    road.style.boxShadow = `0 0 20px ${color}`
    road.style.animationDuration = Math.random() * 4 + 8 + "s"
    road.style.animationDelay = Math.random() * 2 + "s"

    this.container.appendChild(road)

    setTimeout(() => {
      if (road.parentNode) {
        road.parentNode.removeChild(road)
      }
    }, 15000)
  }
}

// Professional transportation system features
class TransportationDashboard {
  constructor() {
    this.init()
    this.startRealTimeUpdates()
  }

  init() {
    this.animateKPICounters()
    this.initializeFleetTracking()
    this.startRouteOptimization()
  }

  animateKPICounters() {
    const kpiValues = document.querySelectorAll(".kpi-value")
    kpiValues.forEach((kpi) => {
      const target = Number.parseFloat(kpi.textContent)
      const unit = kpi.querySelector(".unit")?.textContent || ""
      let current = 0
      const duration = 2000
      const startTime = performance.now()

      function update(currentTime) {
        const elapsed = currentTime - startTime
        const progress = Math.min(elapsed / duration, 1)
        const easedProgress = 1 - Math.pow(1 - progress, 3)

        current = target * easedProgress

        if (unit === "%") {
          kpi.innerHTML = `${Math.floor(current)}<span class="unit">%</span>`
        } else if (unit === "min") {
          kpi.innerHTML = `${current.toFixed(1)}<span class="unit">min</span>`
        } else {
          kpi.innerHTML = `+${Math.floor(current)}<span class="unit">${unit}</span>`
        }

        if (progress < 1) {
          requestAnimationFrame(update)
        }
      }

      requestAnimationFrame(update)
    })
  }

  initializeFleetTracking() {
    const vehicles = document.querySelectorAll(".vehicle")
    vehicles.forEach((vehicle, index) => {
      setTimeout(() => {
        vehicle.style.opacity = "0"
        vehicle.style.transform = "translateX(-20px)"
        vehicle.style.transition = "all 0.5s ease"

        setTimeout(() => {
          vehicle.style.opacity = "1"
          vehicle.style.transform = "translateX(0)"
        }, 100)
      }, index * 200)
    })
  }

  startRouteOptimization() {
    const routeLines = document.querySelectorAll(".route-line")
    const nodes = document.querySelectorAll(".node")

    // Animate route discovery
    setInterval(() => {
      routeLines.forEach((line) => {
        line.style.animationDuration = Math.random() * 2 + 2 + "s"
      })

      nodes.forEach((node) => {
        node.style.animationDelay = Math.random() * 2 + "s"
      })
    }, 5000)
  }

  startRealTimeUpdates() {
    // Simulate real-time data updates
    setInterval(() => {
      this.updateLiveMetrics()
      this.updateFleetStatus()
      this.updateAnalytics()
    }, 3000)
  }

  updateLiveMetrics() {
    const activeRides = document.getElementById("activeRides")
    const avgWaitTime = document.getElementById("avgWaitTime")

    if (activeRides) {
      const currentRides = Number.parseInt(activeRides.textContent)
      const newRides = currentRides + Math.floor(Math.random() * 10 - 5)
      activeRides.textContent = Math.max(200, newRides)
    }

    if (avgWaitTime) {
      const currentTime = Number.parseFloat(avgWaitTime.textContent)
      const newTime = currentTime + (Math.random() * 0.4 - 0.2)
      avgWaitTime.textContent = Math.max(2.0, Math.min(5.0, newTime)).toFixed(1)
    }
  }

  updateFleetStatus() {
    const vehicles = document.querySelectorAll(".vehicle")
    vehicles.forEach((vehicle) => {
      const signalBars = vehicle.querySelectorAll(".signal-strength .bar")
      signalBars.forEach((bar) => {
        bar.style.animationDelay = Math.random() * 2 + "s"
      })
    })
  }

  updateAnalytics() {
    const heatZones = document.querySelectorAll(".heat-zone")
    heatZones.forEach((zone) => {
      zone.style.animationDelay = Math.random() * 3 + "s"
    })

    const chartValue = document.querySelector(".demand-chart .chart-value")
    if (chartValue) {
      const currentValue = Number.parseInt(chartValue.textContent.replace("%", ""))
      const newValue = currentValue + Math.floor(Math.random() * 6 - 3)
      chartValue.textContent = `+${Math.max(15, Math.min(35, newValue))}%`
    }
  }
}

// Enhanced stats counter with professional easing
function animateStatCounters() {
  const statNumbers = document.querySelectorAll(".stat-number[data-target]")

  statNumbers.forEach((stat) => {
    const target = Number.parseInt(stat.getAttribute("data-target"))
    let current = 0
    const duration = 2500
    const startTime = performance.now()

    function easeOutExpo(t) {
      return t === 1 ? 1 : 1 - Math.pow(2, -10 * t)
    }

    function update(currentTime) {
      const elapsed = currentTime - startTime
      const progress = Math.min(elapsed / duration, 1)
      const easedProgress = easeOutExpo(progress)

      current = target * easedProgress

      if (target === 4.8) {
        stat.textContent = current.toFixed(1)
      } else if (target >= 1000) {
        stat.textContent = (current / 1000).toFixed(1) + "k +"
      } else {
        stat.textContent = Math.floor(current) + " +"
      }

      if (progress < 1) {
        requestAnimationFrame(update)
      }
    }

    requestAnimationFrame(update)
  })
}

// Enhanced intersection observer for professional animations
const professionalObserver = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        if (entry.target.classList.contains("transport-dashboard")) {
          new TransportationDashboard()
        }

        if (entry.target.classList.contains("stats")) {
          setTimeout(() => {
            animateStatCounters()
          }, 500)
        }

        // Enhanced dashboard card animations
        if (entry.target.classList.contains("dashboard-card")) {
          const delay = Array.from(entry.target.parentNode.children).indexOf(entry.target) * 200
          setTimeout(() => {
            entry.target.style.opacity = "1"
            entry.target.style.transform = "translateY(0) rotateX(0deg)"
          }, delay)
        }

        professionalObserver.unobserve(entry.target)
      }
    })
  },
  { threshold: 0.2 },
)

// Main application initialization
document.addEventListener("DOMContentLoaded", () => {
  // Initialize particle system
  const canvas = document.getElementById("particleCanvas")
  if (canvas) {
    new ParticleSystem(canvas)
  }

  // Initialize 3D generative system
  const threeDCanvas = document.getElementById("threeDCanvas")
  if (threeDCanvas) {
    new ThreeDGenerativeSystem(threeDCanvas)
  }

  // Initialize 3D floating elements
  new FloatingElements3D()

  // Initialize 3D road system
  new ThreeDRoadSystem()

  // Initialize generative road system
  new GenerativeRoadSystem()

  // Enhanced button interactions
  const buttons = document.querySelectorAll(".btn")
  buttons.forEach((button) => {
    button.addEventListener("click", function (e) {
      // Create ripple effect
      const ripple = document.createElement("span")
      const rect = this.getBoundingClientRect()
      const size = Math.max(rect.width, rect.height)
      const x = e.clientX - rect.left - size / 2
      const y = e.clientY - rect.top - size / 2

      ripple.style.width = ripple.style.height = size + "px"
      ripple.style.left = x + "px"
      ripple.style.top = y + "px"
      ripple.style.position = "absolute"
      ripple.style.borderRadius = "50%"
      ripple.style.background = "rgba(255, 255, 255, 0.6)"
      ripple.style.transform = "scale(0)"
      ripple.style.animation = "ripple 0.6s linear"
      ripple.style.pointerEvents = "none"

      this.appendChild(ripple)

      setTimeout(() => {
        ripple.remove()
      }, 600)

      // Enhanced click animation
      this.style.transform = "scale(0.95)"
      setTimeout(() => {
        this.style.transform = "scale(1)"
      }, 150)

      // Handle different button actions
      const buttonText = this.textContent.trim()
      console.log(`${buttonText} clicked with enhanced effects`)
    })
  })

  // Add CSS for ripple animation
  const style = document.createElement("style")
  style.textContent = `
    @keyframes ripple {
      to {
        transform: scale(4);
        opacity: 0;
      }
    }
  `
  document.head.appendChild(style)

  // Enhanced scroll animations
  const observerOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -50px 0px",
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = "1"
        entry.target.style.transform = "translateY(0)"

        // Add stagger effect for multiple elements
        if (entry.target.classList.contains("stat-item")) {
          const delay = Array.from(entry.target.parentNode.children).indexOf(entry.target) * 100
          entry.target.style.transitionDelay = delay + "ms"
        }
      }
    })
  }, observerOptions)

  // Observe elements for animation
  const animatedElements = document.querySelectorAll(".stat-item, .service-card")
  animatedElements.forEach((el, index) => {
    el.style.opacity = "0"
    el.style.transform = "translateY(20px)"
    el.style.transition = "opacity 0.6s ease, transform 0.6s ease"
    observer.observe(el)
  })

  // Enhanced hero animations
  const heroTitle = document.querySelector(".hero-title")
  const heroSubtitle = document.querySelector(".hero-subtitle")
  const serviceCards = document.querySelectorAll(".service-card")

  if (heroTitle && heroSubtitle) {
    heroTitle.style.opacity = "0"
    heroTitle.style.transform = "translateY(-30px) scale(0.9)"
    heroSubtitle.style.opacity = "0"
    heroSubtitle.style.transform = "translateY(30px)"

    setTimeout(() => {
      heroTitle.style.transition = "opacity 1s ease, transform 1s ease"
      heroSubtitle.style.transition = "opacity 1s ease 0.5s, transform 1s ease 0.5s"

      heroTitle.style.opacity = "1"
      heroTitle.style.transform = "translateY(0) scale(1)"
      heroSubtitle.style.opacity = "1"
      heroSubtitle.style.transform = "translateY(0)"

      // Animate service cards with stagger
      serviceCards.forEach((card, index) => {
        setTimeout(
          () => {
            card.style.opacity = "1"
            card.style.transform = "translateY(0)"
          },
          1000 + index * 200,
        )
      })
    }, 300)
  }

  // Enhanced CTA animation
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

  // Enhanced dashboard card animations
  const dashboardCards = document.querySelectorAll(".dashboard-card")
  dashboardCards.forEach((card) => {
    card.style.opacity = "0"
    card.style.transform = "translateY(30px) rotateX(-10deg)"
    card.style.transition = "all 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275)"
    professionalObserver.observe(card)
  })

  // Enhanced KPI card interactions
  const kpiCards = document.querySelectorAll(".kpi-card")
  kpiCards.forEach((card) => {
    card.addEventListener("mouseenter", () => {
      const chartBars = card.querySelectorAll(".chart-bar")
      chartBars.forEach((bar, index) => {
        setTimeout(() => {
          bar.style.background = "linear-gradient(180deg, #FF8C00, #e66900)"
        }, index * 50)
      })
    })

    card.addEventListener("mouseleave", () => {
      const chartBars = card.querySelectorAll(".chart-bar")
      chartBars.forEach((bar, index) => {
        setTimeout(() => {
          bar.style.background = "linear-gradient(180deg, #4A90E2, #357abd)"
        }, index * 50)
      })
    })
  })

  // Professional route animation
  const routeMap = document.querySelector(".route-map")
  if (routeMap) {
    routeMap.addEventListener("mouseenter", () => {
      const routeLines = routeMap.querySelectorAll(".route-line")
      routeLines.forEach((line) => {
        line.style.animationDuration = "1s"
      })
    })

    routeMap.addEventListener("mouseleave", () => {
      const routeLines = routeMap.querySelectorAll(".route-line")
      routeLines.forEach((line) => {
        line.style.animationDuration = "3s"
      })
    })
  }

  // Initialize professional features
  const dashboardSection = document.querySelector(".transport-dashboard")
  const statsSection = document.querySelector(".stats")

  if (dashboardSection) {
    professionalObserver.observe(dashboardSection)
  }

  if (statsSection) {
    professionalObserver.observe(statsSection)
  }
})

// Enhanced parallax with performance optimization
let ticking = false

function updateParallax() {
  const scrolled = window.pageYOffset
  const heroBackground = document.querySelector(".hero-background")
  const ctaBackground = document.querySelector(".cta-background")
  const neuralNetwork = document.querySelector(".neural-network")

  if (heroBackground) {
    heroBackground.style.transform = `translateY(${scrolled * 0.5}px) scale(${1 + scrolled * 0.0001})`
  }

  if (ctaBackground) {
    ctaBackground.style.transform = `translateY(${scrolled * 0.2}px)`
  }

  if (neuralNetwork) {
    neuralNetwork.style.transform = `translateY(${scrolled * 0.1}px) rotate(${scrolled * 0.01}deg)`
  }

  ticking = false
}

window.addEventListener("scroll", () => {
  if (!ticking) {
    requestAnimationFrame(updateParallax)
    ticking = true
  }
})
