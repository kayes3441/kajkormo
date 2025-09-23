<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WorkerHire</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
<!-- Header -->
<header class="bg-white shadow sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-3 flex justify-between items-center space-x-6">
        <!-- Logo -->
        <div class="flex items-center space-x-2">
            <img src="{{ asset('assets/front-end/images/LL1.png') }}" alt="logo" class="h-10 w-auto">
        </div>

        <!-- Desktop Navbar -->
        <div class="hidden lg:flex items-center space-x-6">
            <img src="{{ asset('assets/front-end/images/LL2.png') }}" alt="logo" class="h-10 w-auto">
        </div>

        <!-- Mobile Menu Button -->
        <div class="lg:hidden flex items-center">
            <button id="menuToggle" class="text-gray-700 focus:outline-none">
                <!-- icon changes via JS -->
                <svg id="menuIcon" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>
    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden lg:hidden bg-white shadow-lg">
        <nav class="flex flex-col px-6 py-4 space-y-3">
            <a href="#home" class="text-gray-700 hover:text-blue-600 font-medium">হোম</a>
            <a href="#about" class="text-gray-700 hover:text-blue-600 font-medium">সম্পর্কে</a>
            <a href="#services" class="text-gray-700 hover:text-blue-600 font-medium">সার্ভিস</a>
            <a href="#contact" class="text-gray-700 hover:text-blue-600 font-medium">যোগাযোগ</a>
        </nav>
    </div>
</header>

<!-- Hero -->
<section class="bg-black text-white py-16 text-center">
    <div class="max-w-3xl mx-auto px-6 relative z-10">
        <img src="{{ asset('assets/front-end/images/LL3.png') }}" alt="Service Button" class="w-full h-auto mr-2">
        <button class="bg-white text-black px-6 py-3 mt-8 rounded flex items-center justify-center mx-auto hover:bg-gray-200 transition">
            <img src="{{ asset('assets/front-end/images/LL12.png') }}" alt="Service Button" class="w-28 h-6 mr-2">
        </button>
    </div>
</section>

<!-- Services -->
<section id="services" class="py-16 bg-gray-100">
    <div class="max-w-3xl mx-auto px-6 text-center">
        <img src="{{ asset('assets/front-end/images/LL11.png') }}" alt="Services" class="w-40 h-8 mb-6 mx-auto">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach (['LL4.png','LL5.png','LL6.png','LL7.png','LL8.png','LL9.png'] as $img)
                <div class="bg-white p-5 sm:p-6 rounded-lg shadow hover:shadow-lg transition-all duration-300 text-center">
                    <img src="{{ asset('assets/front-end/images/'.$img) }}" alt="service icon" class="w-24 h-24 sm:w-32 sm:h-32 mx-auto mb-4 object-contain">
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- About -->
<section id="about" class="py-16">
    <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-10 items-center">
        <img src="https://img.freepik.com/free-vector/people-doing-various-jobs_23-2148824073.jpg" alt="Workers" class="rounded-2xl shadow-lg">
        <div>
            <h3 class="text-3xl font-bold mb-4">কেন WorkerHire বেছে নেবেন?</h3>
            <p class="text-gray-700 mb-4">
                আমরা আপনাকে বিভিন্ন ক্যাটাগরির দক্ষ এবং যাচাই করা কর্মীদের সাথে সংযুক্ত করি, যা নিশ্চিত করে বিশ্বাসযোগ্যতা, নিরাপত্তা এবং সাশ্রয়ী মূল্য।
            </p>
            <ul class="space-y-3 text-gray-700">
                <li>✅ বিশ্বাসযোগ্য ও যাচাই করা কর্মী</li>
                <li>✅ সাশ্রয়ী মূল্য</li>
                <li>✅ ২৪/৭ সুবিধা</li>
                <li>✅ সহজ বুকিং প্রক্রিয়া</li>
            </ul>
        </div>
    </div>
</section>

<!-- Contact -->
<section id="contact" class="py-20 bg-gradient-to-br from-gray-900 via-black to-gray-900 text-white relative">
    <div class="absolute inset-0 opacity-10" style="background-image: url('{{ asset('assets/front-end/images/pattern.svg') }}');"></div>
    <div class="max-w-3xl mx-auto px-6 text-center relative z-10">
        <h3 class="text-4xl font-extrabold mb-4">যোগাযোগ করুন</h3>
        <p class="mb-10 text-gray-300 text-lg">
            কোনো প্রশ্ন বা মতামত আছে? নিচের ফর্মটি পূরণ করুন, আমাদের টিম দ্রুতই উত্তর দেবে।
        </p>
        <form class="space-y-5 bg-white/10 backdrop-blur-md p-8 rounded-lg shadow-xl border border-white/20">
            <input type="text" placeholder="আপনার নাম" class="w-full px-4 py-3 border-2 border-dotted border-gray-400 rounded-md text-black focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
            <input type="email" placeholder="আপনার ইমেইল" class="w-full px-4 py-3 border-2 border-dotted border-gray-400 rounded-md text-black focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
            <textarea placeholder="আপনার বার্তা লিখুন..." class="w-full px-4 py-3 border-2 border-dotted border-gray-400 rounded-md text-black h-32 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
            <button class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-md font-semibold shadow-lg hover:opacity-90 transition">বার্তা পাঠান</button>
        </form>
    </div>
</section>

<!-- Footer -->
<footer class="bg-white shadow mt-10">
    <div class="max-w-7xl mx-auto px-6 py-6 text-center text-gray-600">
        © 2025 WorkerHire. All rights reserved.
    </div>
</footer>

<!-- JS for mobile menu -->
<script>
    const menuToggle = document.getElementById('menuToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    const menuIcon = document.getElementById('menuIcon');

    menuToggle.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
        // toggle icon
        if (mobileMenu.classList.contains('hidden')) {
            menuIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />`;
        } else {
            menuIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />`;
        }
    });
</script>
</body>
</html>
