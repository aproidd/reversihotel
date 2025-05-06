<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxury Resort & Spa - Hotel Reservation System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <!-- Navbar -->
    <?php include 'components/navbar.php'; ?>
    
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Experience Luxury Like Never Before</h1>
            <p>Book your stay with us and enjoy premium services, comfortable rooms, and unforgettable experiences.</p>
            <a href="views/reservasi.php" class="btn">Book Now</a>
        </div>
    </section>
    
    <!-- Search Container -->
    <div class="container">
        <div class="search-container">
            <form action="views/kamar.php" method="GET">
                <input type="text" name="search" placeholder="Search for rooms, amenities...">
                <button type="submit"><i class="fas fa-search"></i> Search</button>
            </form>
        </div>
        
        <!-- Featured Rooms -->
        <h2>Our Featured Rooms</h2>
        <div class="room-list">
            <?php
            // Sample room data - in a real application, this would come from the database
            $rooms = [
                [
                    'id' => 1,
                    'name' => 'Deluxe Suite',
                    'price' => 150,
                    'capacity' => 2,
                    'description' => 'Spacious room with king-sized bed and ocean view.',
                    'image' => 'assets/images/placeholder-room1.jpg',
                    'facilities' => 'Wi-Fi, AC, TV, Mini Bar'
                ],
                [
                    'id' => 2,
                    'name' => 'Family Room',
                    'price' => 200,
                    'capacity' => 4,
                    'description' => 'Perfect for families with two queen beds and extra space.',
                    'image' => 'assets/images/placeholder-room2.jpg',
                    'facilities' => 'Wi-Fi, AC, TV, Mini Bar, Balcony'
                ],
                [
                    'id' => 3,
                    'name' => 'Presidential Suite',
                    'price' => 350,
                    'capacity' => 2,
                    'description' => 'Our most luxurious suite with panoramic views.',
                    'image' => 'assets/images/placeholder-room3.jpg',
                    'facilities' => 'Wi-Fi, AC, TV, Mini Bar, Jacuzzi, Kitchen'
                ]
            ];
            
            // Loop through featured rooms
            foreach ($rooms as $room) {
                include 'components/room-card.php';
            }
            ?>
        </div>
        
        <!-- About Hotel Section -->
        <section class="about-hotel">
            <h2>About Our Hotel</h2>
            <div class="row">
                <div class="col-6">
                    <p>Welcome to our Luxury Resort & Spa, where comfort meets elegance. Nestled in a prime location, our hotel offers the perfect blend of modern amenities and classic hospitality.</p>
                    <p>With a history dating back to 1995, we have been providing exceptional service to our guests from around the world. Our dedicated staff ensures that your stay is nothing short of perfect.</p>
                    <p>Whether you're here for business or leisure, our hotel provides the ideal setting for a memorable stay. From our well-appointed rooms to our world-class dining options, every aspect of our hotel is designed with your comfort in mind.</p>
                </div>
                <div class="col-6">
                    <img src="/api/placeholder/600/400" alt="Hotel exterior" class="img-fluid">
                </div>
            </div>
        </section>
        
        <!-- Featured Amenities -->
        <h2>Our Amenities</h2>
        <div class="features">
            <div class="feature">
                <div class="feature-icon">
                    <i class="fas fa-swimmer"></i>
                </div>
                <h3>Swimming Pool</h3>
                <p>Relax and unwind in our temperature-controlled swimming pool.</p>
            </div>
            <div class="feature">
                <div class="feature-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <h3>Fine Dining</h3>
                <p>Experience culinary excellence at our in-house restaurant.</p>
            </div>
            <div class="feature">
                <div class="feature-icon">
                    <i class="fas fa-spa"></i>
                </div>
                <h3>Spa & Wellness</h3>
                <p>Rejuvenate your body and mind at our premium spa.</p>
            </div>
            <div class="feature">
                <div class="feature-icon">
                    <i class="fas fa-dumbbell"></i>
                </div>
                <h3>Fitness Center</h3>
                <p>Stay fit during your vacation in our fully-equipped gym.</p>
            </div>
        </div>
        
        <!-- Testimonials -->
        <section class="testimonials">
            <h2>What Our Guests Say</h2>
            <div class="testimonial-container">
                <div class="testimonial">
                    <div class="testimonial-text">
                        "The staff was incredibly attentive and the room was spacious and clean. The ocean view was breathtaking. I'll definitely be coming back!"
                    </div>
                    <div class="testimonial-author">- Sarah J., New York</div>
                </div>
                <div class="testimonial">
                    <div class="testimonial-text">
                        "From check-in to check-out, everything was perfect. The spa services were exceptional and the food at the restaurant was divine."
                    </div>
                    <div class="testimonial-author">- Michael T., London</div>
                </div>
                <div class="testimonial">
                    <div class="testimonial-text">
                        "Our family had an amazing time. The kids loved the pool and we enjoyed the proximity to local attractions. Highly recommended for families!"
                    </div>
                    <div class="testimonial-author">- Lisa R., Sydney</div>
                </div>
            </div>
        </section>
        
        <!-- Call to Action -->
        <section class="cta-section">
            <h2>Ready to Experience Luxury?</h2>
            <p>Book your stay now and get special discounts on spa services and dining.</p>
            <a href="views/reservasi.php" class="btn">Reserve Your Room</a>
        </section>
    </div>
    
    <!-- Footer -->
    <?php include 'components/footer.php'; ?>
    
    <script src="assets/js/main.js"></script>
</body>
</html>
