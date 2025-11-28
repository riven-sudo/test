<?php
include('partials-front/menu.php');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>About Us — Blackstar Snackbar</title>
  <link rel="stylesheet" href="css/About-us.css"> <!-- Separate CSS -->
</head>
<body>

<!-- Hero -->
<section class="hero">
  <div class="hero-inner">
    <h1>About Blackstar Snackbar</h1>
    <p class="lead">We craft tasty burgers, snacks, and good vibes — local flavors inspired by space (and hunger!).</p>
  </div>

  <!-- Image Carousel Card -->
  <div class="hero-card">
    <button class="carousel-btn prev">&#10094;</button>
    <div class="carousel-window">
      <div class="carousel-inner">
        <div></div>
        <div></div>
        <div></div>
      </div>
    </div>
    <button class="carousel-btn next">&#10095;</button>

    <!-- Progress Dots -->
    <div class="carousel-dots">
      <span class="active"></span>
      <span></span>
      <span></span>
    </div>
  </div>

  <!-- Explore button under the carousel -->
  <a href="index.php" class="btn">Explore Menu</a>
</section>

<!-- Main -->
<main class="container" role="main">
  <section class="card">
    <h2 class="section-title">Our Story</h2>
    <p class="muted">Blackstar started as a small burger stand with one clear goal: make good food that makes people smile. Over the years we’ve grown into a neighborhood favorite while keeping the same do-it-right attitude — fresh ingredients, bold flavors, and friendly service.</p>
  </section>

  <section class="card" style="margin-top:18px;">
    <h2 class="section-title">Mission & Values</h2>
    <div class="card-row" style="margin-top:12px;">
      <div>
        <h3>Quality First</h3>
        <p class="muted">We source fresh ingredients and prepare each meal with care.</p>
      </div>
      <div>
        <h3>Community</h3>
        <p class="muted">We support local suppliers and give back via events and promos.</p>
      </div>
      <div>
        <h3>Fun</h3>
        <p class="muted">Food should bring people together — we make eating fun and memorable.</p>
      </div>
    </div>
  </section>
</main>

<?php include('partials-front/footer.php'); ?>


<script>
const inner = document.querySelector('.carousel-inner');
const slides = document.querySelectorAll('.carousel-inner div');
const prevBtn = document.querySelector('.carousel-btn.prev');
const nextBtn = document.querySelector('.carousel-btn.next');
const dots = document.querySelectorAll('.carousel-dots span');

let index = 0;
let autoSlide; // interval reference

function showSlide(i) {
  index = (i + slides.length) % slides.length;
  inner.style.transform = `translateX(${-index * 100}%)`;
  updateDots();
}

function updateDots() {
  dots.forEach((dot, i) => {
    dot.classList.toggle('active', i === index);
  });
}

prevBtn.addEventListener('click', () => {
  showSlide(index - 1);
  resetAutoSlide();
});

nextBtn.addEventListener('click', () => {
  showSlide(index + 1);
  resetAutoSlide();
});

dots.forEach((dot, i) => {
  dot.addEventListener('click', () => {
    showSlide(i);
    resetAutoSlide();
  });
});

// --- Auto Slide ---
function startAutoSlide() {
  autoSlide = setInterval(() => {
    showSlide(index + 1);
  }, 2000); // 4 seconds per slide
}

function resetAutoSlide() {
  clearInterval(autoSlide);
  startAutoSlide();
}

// Pause on hover (optional)
const carousel = document.querySelector('.hero-card');
carousel.addEventListener('mouseenter', () => clearInterval(autoSlide));
carousel.addEventListener('mouseleave', startAutoSlide);

// start autoplay on load
startAutoSlide();
</script>


</body>
</html>
