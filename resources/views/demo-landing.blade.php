<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Hire Workers - Service Landing Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">

<!-- Header -->
<header class="bg-white shadow sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-blue-600">WorkerHire</h1>
        <nav class="hidden md:flex space-x-6">
            <a href="#services" class="hover:text-blue-600">Services</a>
            <a href="#about" class="hover:text-blue-600">About</a>
            <a href="#contact" class="hover:text-blue-600">Contact</a>
        </nav>
        <a href="#hire" class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">Hire Now</a>
    </div>
</header>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
    <div class="max-w-7xl mx-auto px-6 py-20 text-center">
        <h2 class="text-4xl md:text-5xl font-bold mb-6">Under Development</h2>
        <h2 class="text-4xl md:text-5xl font-bold mb-6">Hire Skilled Workers Anytime, Anywhere</h2>
        <p class="text-lg mb-8">From plumbing to cleaning, electricians to movers – we connect you with trusted workers instantly.</p>
        <a href="#services" class="px-6 py-3 bg-white text-blue-600 font-semibold rounded-lg shadow hover:bg-gray-100">Explore Services</a>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="py-16 bg-gray-100">
    <div class="max-w-7xl mx-auto px-6 text-center">
        <h3 class="text-3xl font-bold mb-10">Our Services</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <!-- Service Card -->
            <div class="bg-white p-6 rounded-2xl shadow hover:shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 mx-auto text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 9.172a4 4 0 010 5.656l-5.656 5.656a4 4 0 01-5.656-5.656l5.656-5.656a4 4 0 015.656 0z" />
                </svg>                <h4 class="text-xl font-semibold mb-2">Plumbing</h4>
                <p class="text-gray-600 mb-4">Fix leaks, install pipes, and all your plumbing needs covered.</p>
                <a href="#hire" class="text-blue-600 font-semibold hover:underline">Hire Now</a>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow hover:shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 mx-auto text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>                <h4 class="text-xl font-semibold mb-2">Electrical</h4>
                <p class="text-gray-600 mb-4">Certified electricians for installations, repairs, and wiring.</p>
                <a href="#hire" class="text-blue-600 font-semibold hover:underline">Hire Now</a>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow hover:shadow-lg">
                <img src="https://img.icons8.com/fluency/96/vacuum-cleaner.png" alt="Cleaning" class="mx-auto mb-4" />
                <h4 class="text-xl font-semibold mb-2">Cleaning</h4>
                <p class="text-gray-600 mb-4">Professional cleaners for your home, office, or construction site.</p>
                <a href="#hire" class="text-blue-600 font-semibold hover:underline">Hire Now</a>
            </div>

        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-16">
    <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-10 items-center">
        <img src="https://img.freepik.com/free-vector/people-doing-various-jobs_23-2148824073.jpg" alt="Workers" class="rounded-2xl shadow-lg" />
        <div>
            <h3 class="text-3xl font-bold mb-4">Why Choose WorkerHire?</h3>
            <p class="text-gray-700 mb-4">We connect you with skilled and verified workers across multiple categories, ensuring reliability, safety, and affordability.</p>
            <ul class="space-y-3 text-gray-700">
                <li>✅ Trusted & Verified Workers</li>
                <li>✅ Affordable Pricing</li>
                <li>✅ 24/7 Availability</li>
                <li>✅ Easy Booking Process</li>
            </ul>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-16 bg-gray-100">
    <div class="max-w-3xl mx-auto px-6 text-center">
        <h3 class="text-3xl font-bold mb-6">Get in Touch</h3>
        <p class="mb-6 text-gray-600">Have questions? Reach out and we’ll be happy to assist you.</p>
        <form class="space-y-4">
            <input type="text" placeholder="Your Name" class="w-full px-4 py-3 border rounded-lg" required />
            <input type="email" placeholder="Your Email" class="w-full px-4 py-3 border rounded-lg" required />
            <textarea placeholder="Your Message" class="w-full px-4 py-3 border rounded-lg h-32"></textarea>
            <button class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700">Send Message</button>
        </form>
    </div>
</section>

<!-- Footer -->
<footer class="bg-white shadow mt-10">
    <div class="max-w-7xl mx-auto px-6 py-6 text-center text-gray-600">
        © 2025 WorkerHire. All rights reserved.
    </div>
</footer>

</body>
</html>
