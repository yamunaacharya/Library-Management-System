<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bootstrap Carousel</title>
  <script 
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js">
  </script>
  <link 
    href="https://cdn.jsdelivr.net/npm/lucide@0.216.0/dist/umd/lucide.min.css" 
    rel="stylesheet"
  >
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >
   <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" 
    rel="stylesheet"
  >
  <style>
       .text-gray-600 {
      color: #6b7280;
    }
    .hover\:text-indigo-600:hover {
      color: #4f46e5;
    }
    .navbar-brand span {
      color: #1f2937;
    }
    .shadow-md {
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .carousel-item {
      height: 600px;
    }
    .carousel-item img {
      object-fit: cover;
      height: 100%;
      width: 100%;
    }
    .carousel-overlay {
      position: absolute;
      inset: 0;
      background: rgba(0, 0, 0, 0.4);
      z-index: 1;
    }
    .carousel-caption {
      z-index: 2;
    }
    .carousel-indicators [data-bs-target] {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background-color: rgba(255, 255, 255, 0.5);
    }
    .carousel-indicators .active {
      background-color: white;
      width: 12px;
    }
    /* Footer Styles */
.footer {
  background-color: #080808;
  color: #f9fafb;
  padding: 32px 16px;
  font-size: 14px;
}

.container {
  max-width: 1120px;
  margin: 0 auto;
  padding: 0 16px;
}

.footer-columns {
  display: flex;
  flex-wrap: wrap;
  gap: 32px;
  justify-content: space-between;
}

.footer-column {
  flex: 1;
  min-width: 200px;
}

.footer-heading {
  font-size: 18px;
  margin-bottom: 16px;
  color: #fbbf24;
}

.footer-column p {
  margin: 0 0 8px;
  line-height: 1.6;
}

.footer-links {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-links li {
  margin-bottom: 8px;
}

.footer-links a {
  color: #9ca3af;
  text-decoration: none;
  transition: color 0.2s ease;
}

.footer-links a:hover {
  color: #fbbf24;
}

.footer-bottom {
  text-align: center;
  margin-top: 32px;
  border-top: 1px solid #080808;
  padding-top: 16px;
}

.footer-bottom p {
  margin: 0;
  color: #9ca3af;
}
.navbar .auth-buttons a {
    color: white;
    text-decoration: none;
    background-color: #007BFF;
    padding: 8px 10px;
    border-radius: 5px;
    margin: 0 5px;
    font-size: 16px;
}

.navbar .auth-buttons a:hover {
    background-color: #0056b3;
}
.features {
    padding: 3rem 2rem;
    text-align: center;
    background-color: #fff;
}

.features h2 {
    font-size: 2.5rem;
    margin-bottom: 2rem;
}

.feature-item {
    display: inline-block;
    width: 30%;
    padding: 1rem;
    margin: 1rem;
    background-color: #f8f8f8;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.feature-item img {
    max-width: 50px;
    margin-bottom: 1rem;
}

.feature-item h3 {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.feature-item p {
    font-size: 1rem;
}

/* Footer Section */
footer {
    background-color: #333;
    color: white;
    padding: 2rem 0;
    text-align: center;
}

footer .footer-links a {
    color: white;
    margin: 0 1rem;
    text-decoration: none;
}

footer .footer-links a:hover {
    text-decoration: underline;
}

footer p {
    margin-top: 1rem;
}


  </style>
</head>
<body>
     <nav class="navbar navbar-expand-lg bg-white shadow-md fixed-top">
    <div class="container">
      <!-- Logo Section -->
      <a class="navbar-brand d-flex align-items-center" href="#">
        <i class="lucide lucide-book text-indigo-600" style="font-size: 24px;"></i>
        <span class="ms-2 fs-4 fw-bold text-gray-800">LibraryHub</span>
      </a>

      <!-- Hamburger Toggle for Mobile -->
      <button 
        class="navbar-toggler" 
        type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#navbarNav" 
        aria-controls="navbarNav" 
        aria-expanded="false" 
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Navbar Links -->
     
        <!-- Search and User Buttons -->
        <div class="d-flex align-items-center ms-lg-3">
          <button class="btn text-gray-600 hover:text-indigo-600 p-2">
            <i class="lucide lucide-search" style="font-size: 20px;"></i>
          </button>
          <button class="btn text-gray-600 hover:text-indigo-600 p-2">
            <i class="lucide lucide-user" style="font-size: 20px;"></i>
          </button>
        </div>
      </div>
    </div>
    <div class="auth-buttons">
      <a href="login.php" class="login-btn">Login</a>
      <a href="signup.php" class="signup-btn">Sign Up</a>
  </div>
  </nav>

  <div id="carouselExample" class="carousel slide position-relative">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <div class="carousel-overlay"></div>
        <img
          src="https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&q=80&w=2000"
          class="d-block w-100"
          alt="Discover Endless Stories"
        >
        <div class="carousel-caption text-center text-white">
          <h2 class="display-4 fw-bold mb-3">Discover Endless Stories</h2>
          <p class="fs-5">Explore our vast collection of books across multiple genres</p>
        </div>
      </div>
      <div class="carousel-item">
        <div class="carousel-overlay"></div>
        <img
          src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?auto=format&fit=crop&q=80&w=2000"
          class="d-block w-100"
          alt="Modern Learning Spaces"
        >
        <div class="carousel-caption text-center text-white">
          <h2 class="display-4 fw-bold mb-3">Modern Learning Spaces</h2>
          <p class="fs-5">Comfortable reading areas and study rooms for everyone</p>
        </div>
      </div>
      <div class="carousel-item">
        <div class="carousel-overlay"></div>
        <img
          src="https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&q=80&w=2000"
          class="d-block w-100"
          alt="Digital Resources"
        >
        <div class="carousel-caption text-center text-white">
          <h2 class="display-4 fw-bold mb-3">Digital Resources</h2>
          <p class="fs-5">Access our extensive digital library anywhere, anytime</p>
        </div>
      </div>
    </div>
    <button
      class="carousel-control-prev"
      type="button"
      data-bs-target="#carouselExample"
      data-bs-slide="prev"
    >
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button
      class="carousel-control-next"
      type="button"
      data-bs-target="#carouselExample"
      data-bs-slide="next"
    >
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
    <div class="carousel-indicators">
      <button
        type="button"
        data-bs-target="#carouselExample"
        data-bs-slide-to="0"
        class="active"
        aria-current="true"
        aria-label="Slide 1"
      ></button>
      <button
        type="button"
        data-bs-target="#carouselExample"
        data-bs-slide-to="1"
        aria-label="Slide 2"
      ></button>
      <button
        type="button"
        data-bs-target="#carouselExample"
        data-bs-slide-to="2"
        aria-label="Slide 3"
      ></button>
    </div>
  </div>

  <section class="features" id="features">
    <h2>Key Features</h2>
  
    <div class="feature-item">
        <img src="kk.jpg" alt="User Management">
        <h3>User Management</h3>
        <p>Track users, manage book loans, and handle fines.</p>
    </div>
    <div class="feature-item">
        <img src="ll.jpg" alt="Reports & Analytics">
        <h3>Reports & Analytics</h3>
        <p>Generate detailed reports and track activity.</p>
    </div>
    <div class="feature-item">
        <img src="images.png" alt="Book Reservation">
        <h3>Book Borrow</h3>
        <p>Allow users to borrow book.</p>
    </div> 
</section>

  <footer class="footer">
    <div class="container">
      <div class="footer-columns">
        <div class="footer-column">
          <h3 class="footer-heading">About Us</h3>
          <p>
            We are a modern library management system providing seamless access to books, journals, and more.
          </p>
        </div>
        
        <div class="footer-column">
          <h3 class="footer-heading">Contact</h3>
          <p>Itahari,Sunsari</p>
          <p>Email: lms222@ gmail.com</p>
          <p>Phone: 9811330200</p>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2024 Library Management System. All Rights Reserved.</p>
      </div>
    </div>
  </footer>
  

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>