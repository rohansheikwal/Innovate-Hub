<?php
require_once __DIR__ . '/backend/config.php';
$isAuthenticated = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Innocascade - Unleash Your Creativity</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div id="particles-js" class="absolute inset-0 z-0"></div>
    <div class="gradient-overlay"></div>

    <header>
        <nav>
            <div class="logo">
                <div class="logo-text pulse-effect">Innocascade</div>
            </div>
            <div class="nav-links">
                <a href="index.php" class="text-orange-500">Home</a>
                <a href="about.html">About</a>
                <a href="share_ideas.html">Share Ideas</a>
                <a href="chat.html">Chat</a>
                <a href="explore_ideas.html">Explore Ideas</a>
                <a href="leaderboard.html">Leaderboard</a>
                <a href="profile.html">Profile</a>
                <a href="admin.html" id="admin-link" class="hidden">Admin</a>
                <a href="auth.html#login" id="auth-link" <?php if ($isAuthenticated) echo 'class="hidden"'; ?>>Login</a>
                <a href="backend/auth/logout.php" id="logout-link" <?php if (!$isAuthenticated) echo 'class="hidden"'; ?>>Logout</a>
            </div>
            <div class="hamburger">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </nav>
        <div class="mobile-menu" id="mobile-menu">
            <a href="index.php" class="text-orange-500">Home</a>
            <a href="about.html">About</a>
            <a href="share_ideas.html">Share Ideas</a>
            <a href="chat.html">Chat</a>
            <a href="explore_ideas.html">Explore Ideas</a>
            <a href="leaderboard.html">Leaderboard</a>
            <a href="profile.html">Profile</a>
            <a href="admin.html" id="mobile-admin-link" class="hidden">Admin</a>
            <a href="auth.html#login" id="mobile-auth-link" <?php if ($isAuthenticated) echo 'class="hidden"'; ?>>Login</a>
            <a href="backend/auth/logout.php" id="mobile-logout-link" <?php if (!$isAuthenticated) echo 'class="hidden"'; ?>>Logout</a>
        </div>
    </header>

    <section class="relative min-h-screen flex items-center z-10">
        <div class="container">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold gradient-text animate__animated animate__fadeInDown">
                    Unleash Your Creativity
                </h1>
                <p class="text-lg md:text-xl text-gray-300 mt-4 animate__animated animate__fadeInUp animate__delay-1s">
                    Join Innocascade to share, explore, and collaborate on innovative ideas.
                </p>
                <div class="mt-8 space-x-4 animate__animated animate__fadeInUp animate__delay-2s">
                    <a href="share_ideas.html" class="gradient-button text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transition">
                        Share Your Idea
                    </a>
                    <a href="explore_ideas.html" class="border border-orange-500 text-orange-500 px-6 py-3 rounded-lg font-semibold hover:bg-orange-500 hover:text-white transition">
                        Explore Ideas
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-gray-900">
        <div class="container">
            <h2 class="text-3xl md:text-4xl font-bold text-center gradient-text mb-12 animate__animated animate__fadeIn">
                Why Innocascade?
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="feature-card p-6 bg-gray-800 rounded-lg shadow-lg animate__animated animate__fadeInUp">
                    <i class="fas fa-lightbulb text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-white mb-2">Share Ideas</h3>
                    <p class="text-gray-300">Post your innovative ideas and get feedback from a vibrant community.</p>
                </div>
                <div class="feature-card p-6 bg-gray-800 rounded-lg shadow-lg animate__animated animate__fadeInUp animate__delay-1s">
                    <i class="fas fa-users text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-white mb-2">Collaborate</h3>
                    <p class="text-gray-300">Connect with like-minded individuals to bring your ideas to life.</p>
                </div>
                <div class="feature-card p-6 bg-gray-800 rounded-lg shadow-lg animate__animated animate__fadeInUp animate__delay-2s">
                    <i class="fas fa-trophy text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-white mb-2">Get Recognized</h3>
                    <p class="text-gray-300">Earn points, badges, and climb the leaderboard with your contributions.</p>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-content">
            <p>© 2025 Innocascade. All rights reserved.</p>
            <div class="footer-links">
                <a href="index.php">Home</a>
                <a href="about.html">About</a>
                <a href="contact.html">Contact</a>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
            </div>
            <div class="social-icons">
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-github"></i></a>
            </div>
        </div>
    </footer>

    <div id="notification" class="notification hidden"></div>

    <script src="js/scripts.js"></script>
    <script>
        particlesJS('particles-js', {
            particles: {
                number: { value: 80, density: { enable: true, value_area: 800 } },
                color: { value: '#f97316' },
                shape: { type: 'circle' },
                opacity: { value: 0.5, random: false },
                size: { value: 3, random: true },
                line_linked: { enable: true, distance: 150, color: '#f97316', opacity: 0.4, width: 1 },
                move: { enable: true, speed: 6, direction: 'none', random: false, straight: false, out_mode: 'out', bounce: false }
            },
            interactivity: {
                detect_on: 'canvas',
                events: { onhover: { enable: true, mode: 'repulse' }, onclick: { enable: true, mode: 'push' }, resize: true },
                modes: { repulse: { distance: 100, duration: 0.4 }, push: { particles_nb: 4 } }
            },
            retina_detect: true
        });

        document.addEventListener('DOMContentLoaded', async () => {
            const isAuthenticated = <?php echo json_encode($isAuthenticated); ?>;
            if (isAuthenticated) {
                const response = await fetch('backend/auth/check_auth.php');
                const data = await response.json();
                if (data.success && data.user.role === 'admin') {
                    document.getElementById('admin-link').classList.remove('hidden');
                    document.getElementById('mobile-admin-link').classList.remove('hidden');
                }
            }
        });
    </script>
</body>
</html>