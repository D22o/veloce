<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veloce | Premium Car Rental</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>
    <div class="cursor-glow"></div>
    <div class="noise-overlay"></div>

    <header class="navbar" id="navbar">
        <div class="nav-container">
            <a href="/" class="logo"><i class="fa-solid fa-car-side"></i> VELOCE</a>
            <nav class="nav-menu" id="navMenu">
                <a href="#home" class="nav-link active">Home</a>
                <a href="#features" class="nav-link">Why Us</a>
                <a href="#cars" class="nav-link">Our Fleet</a>
                <a href="#testimonials" class="nav-link">Reviews</a>
            </nav>
            <div class="nav-actions">
                <a href="login" class="btn btn-secondary">Sign In</a>
                <a href="#cars" class="btn btn-primary">Book Now</a>
            </div>
            <button class="menu-toggle" id="menuToggle" aria-label="Toggle Navigation">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </header>

    <section id="home" class="hero">
        <div class="hero-bg-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
        <div class="hero-container">
            <div class="hero-content">
                <span class="badge">Premium Rental Experience</span>
                <h1>Drive the Exception.<br><span class="gradient-text">Rent the Ultimate.</span></h1>
                <p>Uncompromising performance. Chilled luxury. Experience our exclusive fleet of precision-engineered vehicles tailored for your journey.</p>
                <div class="hero-buttons">
                    <a href="#cars" class="btn btn-primary btn-lg">Explore Fleet</a>
                    <a href="#features" class="btn btn-outline btn-lg">Learn More</a>
                </div>
                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number" data-target="250">0</span>+
                        <span class="stat-label">Vehicles</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" data-target="15">0</span>K+
                        <span class="stat-label">Happy Clients</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" data-target="99">0</span>%
                        <span class="stat-label">Satisfaction</span>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <div class="car-glow-effect"></div>
                <div class="image-crop-container">
                    <img src="https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=800&q=80" alt="Luxury Sports Car">
                </div>
                <div class="floating-card card-1">
                    <i class="fa-solid fa-shield-check"></i>
                    <div>
                        <strong>Insured</strong>
                        <span>Full Coverage</span>
                    </div>
                </div>
                <div class="floating-card card-2">
                    <i class="fa-solid fa-bolt"></i>
                    <div>
                        <strong>Instant</strong>
                        <span>Verification</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="features-section">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Advantages</span>
                <h2>Why Choose <span class="gradient-text">Veloce</span></h2>
                <p>We redefine premium car rentals with seamless service and first-class security.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card" data-tilt>
                    <div class="feature-icon"><i class="fa-solid fa-shield-halved"></i></div>
                    <h3>Fully Insured Fleet</h3>
                    <p>Drive with absolute peace of mind. Every vehicle features comprehensive coverage and real-time security configurations.</p>
                    <div class="feature-glow"></div>
                </div>
                <div class="feature-card" data-tilt>
                    <div class="feature-icon"><i class="fa-solid fa-bolt"></i></div>
                    <h3>Instant Verification</h3>
                    <p>No tedious queues. Upload your details, get cleared via our streamlined portal, and unlock your vehicle instantly.</p>
                    <div class="feature-glow"></div>
                </div>
                <div class="feature-card" data-tilt>
                    <div class="feature-icon"><i class="fa-solid fa-headset"></i></div>
                    <h3>24/7 Roadside Support</h3>
                    <p>Wherever you are, our specialized remote tech and logistics assistance teams are standing by to keep you moving.</p>
                    <div class="feature-glow"></div>
                </div>
            </div>
        </div>
    </section>

    <section id="cars" class="fleet-section">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Collection</span>
                <h2>Our Elite <span class="gradient-text">Fleet</span></h2>
                <p>Select from our handpicked lineup of sports, luxury, and electric vehicles.</p>
                <div class="filter-buttons">
                    <button class="filter-btn active" data-filter="all">All</button>
                    <button class="filter-btn" data-filter="luxury">Luxury</button>
                    <button class="filter-btn" data-filter="sports">Sports</button>
                    <button class="filter-btn" data-filter="suv">SUVs</button>
                </div>
            </div>

            <div class="fleet-grid">
                <div class="car-card" data-category="sports" data-tilt>
                    <div class="car-img-wrapper">
                        <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=600&q=80" alt="Porsche 911">
                        <div class="car-overlay">
                            <a href="#" class="btn btn-primary btn-sm">Rent Now</a>
                        </div>
                    </div>
                    <div class="car-details">
                        <span class="car-category">Sports</span>
                        <h3>Apex Touring GT</h3>
                        <div class="car-specs">
                            <span><i class="fa-solid fa-gauge"></i> 310 km/h</span>
                            <span><i class="fa-solid fa-gears"></i> Auto</span>
                            <span><i class="fa-solid fa-user"></i> 2 Seats</span>
                        </div>
                        <hr class="divider">
                        <div class="car-footer">
                            <div class="price"><strong>₱12,500</strong> <span>/ day</span></div>
                            <a href="#" class="btn btn-primary btn-sm">Rent Now</a>
                        </div>
                    </div>
                </div>

                <div class="car-card" data-category="luxury" data-tilt>
                    <div class="car-img-wrapper">
                        <img src="https://images.unsplash.com/photo-1555215695-3004980ad54e?auto=format&fit=crop&w=600&q=80" alt="BMW 5 Series">
                        <div class="car-overlay">
                            <a href="#" class="btn btn-primary btn-sm">Rent Now</a>
                        </div>
                    </div>
                    <div class="car-details">
                        <span class="car-category">Luxury</span>
                        <h3>Executive Cruiser v8</h3>
                        <div class="car-specs">
                            <span><i class="fa-solid fa-gauge"></i> 250 km/h</span>
                            <span><i class="fa-solid fa-gears"></i> Auto</span>
                            <span><i class="fa-solid fa-user"></i> 5 Seats</span>
                        </div>
                        <hr class="divider">
                        <div class="car-footer">
                            <div class="price"><strong>₱9,000</strong> <span>/ day</span></div>
                            <a href="#" class="btn btn-primary btn-sm">Rent Now</a>
                        </div>
                    </div>
                </div>

                <div class="car-card" data-category="suv" data-tilt>
                    <div class="car-img-wrapper">
                        <img src="https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=600&q=80" alt="Range Rover">
                        <div class="car-overlay">
                            <a href="#" class="btn btn-primary btn-sm">Rent Now</a>
                        </div>
                    </div>
                    <div class="car-details">
                        <span class="car-category">SUVs</span>
                        <h3>Stealth Matrix 4x4</h3>
                        <div class="car-specs">
                            <span><i class="fa-solid fa-gauge"></i> 220 km/h</span>
                            <span><i class="fa-solid fa-gears"></i> Auto</span>
                            <span><i class="fa-solid fa-user"></i> 7 Seats</span>
                        </div>
                        <hr class="divider">
                        <div class="car-footer">
                            <div class="price"><strong>₱10,500</strong> <span>/ day</span></div>
                            <a href="#" class="btn btn-primary btn-sm">Rent Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="testimonials" class="testimonials-section">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Testimonials</span>
                <h2>What Our <span class="gradient-text">Drivers Say</span></h2>
                <p>Real experiences from our community of premium clients.</p>
            </div>
            <div class="testimonials-grid">
                <div class="testimonial-card" data-tilt>
                    <div class="quote-icon"><i class="fa-solid fa-quote-left"></i></div>
                    <div class="stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                    <p>"The online booking was completely seamless, and the car was immaculate. Exceptional experience from checkout to drop-off."</p>
                    <div class="client-info">
                        <div class="client-avatar">MA</div>
                        <div class="client-meta">
                            <strong>Marc A.</strong>
                            <span>Manila</span>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card" data-tilt>
                    <div class="quote-icon"><i class="fa-solid fa-quote-left"></i></div>
                    <div class="stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                    <p>"Flawless fleet curation. Renting the Apex GT for our weekend run was worth every single peso. Highly secure booking portal."</p>
                    <div class="client-info">
                        <div class="client-avatar">KE</div>
                        <div class="client-meta">
                            <strong>Kenneth E.</strong>
                            <span>Pampanga</span>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card" data-tilt>
                    <div class="quote-icon"><i class="fa-solid fa-quote-left"></i></div>
                    <div class="stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star-half-stroke"></i></div>
                    <p>"Professional service from start to finish. The vehicle was in pristine condition and the pickup process was lightning fast."</p>
                    <div class="client-info">
                        <div class="client-avatar">SR</div>
                        <div class="client-meta">
                            <strong>Sarah R.</strong>
                            <span>Cebu</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="cta-container">
            <div class="cta-content">
                <h2>Ready to <span class="gradient-text">Hit the Road?</span></h2>
                <p>Join thousands of satisfied drivers who trust Veloce for their premium rental needs. Book now and experience the difference.</p>
                <div class="cta-buttons">
                    <a href="#cars" class="btn btn-primary btn-lg">Browse Fleet</a>
                    <a href="tel:+63123456789" class="btn btn-outline btn-lg"><i class="fa-solid fa-phone"></i> Call Us</a>
                </div>
            </div>
            <div class="cta-visual">
                <div class="cta-circle"></div>
                <div class="cta-circle cta-circle-2"></div>
                <i class="fa-solid fa-car-side cta-icon"></i>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="/" class="logo footer-logo"><i class="fa-solid fa-car-side"></i> VELOCE</a>
                    <p>Premium car rentals engineered for excellence. Drive the future today.</p>
                    <div class="social-links">
                        <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="footer-links">
                    <h4>Quick Links</h4>
                    <a href="#home">Home</a>
                    <a href="#features">Why Us</a>
                    <a href="#cars">Fleet</a>
                    <a href="#testimonials">Reviews</a>
                </div>
                <div class="footer-links">
                    <h4>Services</h4>
                    <a href="#">Daily Rentals</a>
                    <a href="#">Long Term</a>
                    <a href="#">Corporate</a>
                    <a href="#">Chauffeur</a>
                </div>
                <div class="footer-links">
                    <h4>Contact</h4>
                    <a href="tel:+63123456789"><i class="fa-solid fa-phone"></i> +63 123 456 789</a>
                    <a href="mailto:hello@veloce.com"><i class="fa-solid fa-envelope"></i> hello@veloce.com</a>
                    <a href="#"><i class="fa-solid fa-location-dot"></i> Makati City, PH</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 VELOCE Rent A Car. Engineered for excellence.</p>
                <div class="footer-legal">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="assets/js/index.js"></script>
</body>
</html>