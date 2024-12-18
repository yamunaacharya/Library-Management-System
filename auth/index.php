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
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link text-gray-600 hover:text-indigo-600" href="#">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-gray-600 hover:text-indigo-600" href="#">Catalog</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-gray-600 hover:text-indigo-600" href="#">Services</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-gray-600 hover:text-indigo-600" href="#">About</a>
          </li>
        </ul>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>