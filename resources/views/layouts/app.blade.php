<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MealIt — AI-Powered Recipe & Custom Meal Planner')</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700;800&family=JetBrains+Mono&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome 6 Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- AOS Scroll Animations -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    
    <!-- Custom Style System -->
    <style>
        :root {
            --primary: #E85D04;
            --primary-light: #FF7A1A;
            --secondary: #1B4332;
            --accent: #F4A261;
            --success: #2D9D5E;
            --warning: #FFC107;
            --danger: #DC3545;
            --bg: #FFFFFF;
            --bg-card: #FFFFFF;
            --bg-page: #F8F9FA;
            --text: #212529;
            --text-muted: #6C757D;
            --border: #E9ECEF;
            --glass-bg: rgba(255, 255, 255, 0.45);
            --glass-border: rgba(255, 255, 255, 0.4);
            --glass-blur: 16px;
            --radius: 16px;
            --radius-sm: 8px;
            --radius-lg: 24px;
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05);
            --shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 20px 60px rgba(0, 0, 0, 0.1);
            --shadow-primary: 0 10px 30px rgba(232, 93, 4, 0.15);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-fast: all 0.15s ease;
        }

        [data-theme="dark"] {
            --bg: #0F0F1A;
            --bg-card: #1A1A2E;
            --bg-page: #131326;
            --text: #E8E8F0;
            --text-muted: #9090B0;
            --border: #2D2D4A;
            --glass-bg: rgba(26, 26, 46, 0.65);
            --glass-border: rgba(255, 255, 255, 0.08);
            --glass-blur: 20px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-page);
            color: var(--text);
            transition: var(--transition);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Inter', sans-serif;
            font-weight: 700;
        }
        
        .playfair {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
        }

        /* Glassmorphism Classes */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(var(--glass-blur));
            -webkit-backdrop-filter: blur(var(--glass-blur));
            border: 1px solid var(--glass-border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(232, 93, 4, 0.25);
        }

        .glass-nav {
            background: rgba(var(--bg) === '#FFFFFF' ? '255,255,255' : '15,15,26', 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: var(--transition);
        }

        /* Buttons styling */
        .btn-custom {
            background-color: var(--primary);
            color: #FFFFFF;
            border-radius: 30px;
            padding: 10px 24px;
            font-weight: 600;
            border: none;
            box-shadow: var(--shadow-primary);
            transition: var(--transition);
        }

        .btn-custom:hover {
            background-color: var(--primary-light);
            color: #FFFFFF;
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(232, 93, 4, 0.25);
        }

        .btn-outline-custom {
            background-color: transparent;
            color: var(--text);
            border: 2px solid var(--border);
            border-radius: 30px;
            padding: 8px 22px;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-outline-custom:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-2px);
        }

        /* Badge Customization */
        .badge-beginner { background-color: #2D9D5E; color: white; }
        .badge-easy { background-color: #F4A261; color: white; }
        .badge-medium { background-color: var(--primary); color: white; }
        .badge-advanced { background-color: #DC3545; color: white; }
        .badge-pro { background-color: #8338EC; color: white; }

        /* Floating AI Chatbot Bubble styling */
        #chatbot-bubble {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #FF7A1A);
            box-shadow: 0 8px 32px rgba(232, 93, 4, 0.4);
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            cursor: pointer;
            z-index: 1050;
            transition: var(--transition);
        }

        #chatbot-bubble:hover {
            transform: scale(1.1) rotate(15deg);
        }

        #chatbot-window {
            position: fixed;
            bottom: 105px;
            right: 30px;
            width: 380px;
            height: 520px;
            border-radius: 20px;
            z-index: 1050;
            display: none;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid var(--glass-border);
            box-shadow: var(--shadow-lg);
        }

        .chatbot-header {
            background: linear-gradient(135deg, var(--primary), #FF7A1A);
            color: white;
            padding: 16px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chatbot-messages {
            flex: 1;
            padding: 16px;
            overflow-y: auto;
            background: var(--bg-card);
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .chat-msg {
            max-width: 80%;
            padding: 10px 14px;
            border-radius: 14px;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .chat-msg.user {
            background-color: var(--primary);
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 2px;
        }

        .chat-msg.bot {
            background-color: var(--bg-page);
            color: var(--text);
            align-self: flex-start;
            border-bottom-left-radius: 2px;
            border: 1px solid var(--border);
        }

        .chatbot-input {
            padding: 12px;
            background: var(--bg-card);
            border-top: 1px solid var(--border);
            display: flex;
            gap: 8px;
        }

        .chatbot-input input {
            flex: 1;
            border: 1px solid var(--border);
            background: var(--bg-page);
            color: var(--text);
            border-radius: 20px;
            padding: 8px 16px;
            outline: none;
        }

        /* Dark Mode Switcher button */
        .theme-toggle-btn {
            background: none;
            border: none;
            color: var(--text);
            font-size: 1.25rem;
            cursor: pointer;
            padding: 8px;
            transition: var(--transition);
        }

        .theme-toggle-btn:hover {
            color: var(--primary);
            transform: rotate(30deg);
        }
        
        .footer {
            background-color: var(--bg-card);
            border-top: 1px solid var(--border);
            margin-top: auto;
            padding: 40px 0;
            transition: var(--transition);
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Transparent Navbar -->
    <nav class="navbar navbar-expand-lg glass-nav">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <span class="fs-3 fw-bold text-gradient" style="background: linear-gradient(135deg, var(--primary), #FF7A1A); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    <i class="fa-solid fa-utensils me-2"></i>MealIt
                </span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link fw-semibold px-3" href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold px-3" href="{{ route('recipes.index') }}">Explore</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold px-3" href="{{ route('generate') }}">AI Generator</a></li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold px-3 d-flex align-items-center gap-1" href="{{ route('web-recipes.index') }}">
                            <i class="fa-solid fa-globe fa-sm text-primary"></i> Web Recipes
                        </a>
                    </li>
                    <li class="nav-item"><a class="nav-link fw-semibold px-3" href="{{ route('compare') }}">Compare</a></li>
                </ul>
                
                <div class="d-flex align-items-center gap-3">
                    <button class="theme-toggle-btn" id="theme-toggle" aria-label="Toggle dark mode">
                        <i class="fa-solid fa-moon"></i>
                    </button>

                    @auth
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" id="userMenu" data-bs-toggle="dropdown">
                                <img src="{{ auth()->user()->avatar_url }}" width="38" height="38" class="rounded-circle me-2 border border-2 border-primary" alt="Avatar">
                                <span class="fw-semibold d-none d-sm-inline" style="color: var(--text);">{{ auth()->user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2 mt-2" style="background: var(--bg-card); border-radius: 12px;">
                                <li><a class="dropdown-item rounded-3 py-2 fw-semibold" style="color: var(--text);" href="{{ route('dashboard') }}"><i class="fa-solid fa-gauge me-2 text-primary"></i>Dashboard</a></li>
                                <li><a class="dropdown-item rounded-3 py-2 fw-semibold" style="color: var(--text);" href="{{ route('bookmarks') }}"><i class="fa-solid fa-bookmark me-2 text-primary"></i>Saved Recipes</a></li>
                                <li><a class="dropdown-item rounded-3 py-2 fw-semibold" style="color: var(--text);" href="{{ route('meal-planner') }}"><i class="fa-solid fa-calendar me-2 text-primary"></i>Meal Planner</a></li>
                                <li><a class="dropdown-item rounded-3 py-2 fw-semibold" style="color: var(--text);" href="{{ route('shopping-list') }}"><i class="fa-solid fa-list-check me-2 text-primary"></i>Shopping List</a></li>
                                <li><a class="dropdown-item rounded-3 py-2 fw-semibold" style="color: var(--text);" href="{{ route('profile.edit') }}"><i class="fa-solid fa-user-gear me-2 text-primary"></i>Profile</a></li>
                                <li><hr class="dropdown-divider my-2 border-secondary" style="opacity: 0.1;"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button class="dropdown-item text-danger rounded-3 py-2 fw-semibold" type="submit">
                                            <i class="fa-solid fa-right-from-bracket me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-custom">Log In</a>
                        <a href="{{ route('register') }}" class="btn btn-custom">Get Started</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <span class="fs-4 fw-bold text-primary"><i class="fa-solid fa-utensils me-2"></i>MealIt</span>
                    <p class="mt-3 text-muted" style="max-width: 320px;">
                        The ultimate AI-powered custom recipe recommendation engine and interactive meal planner. Zero waste, optimized macros.
                    </p>
                </div>
                <div class="col-md-2 col-6">
                    <h6 class="fw-bold mb-3">Discover</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2 text-muted">
                        <li><a href="{{ route('recipes.index') }}" class="text-decoration-none text-muted">Browse Recipes</a></li>
                        <li><a href="{{ route('generate') }}" class="text-decoration-none text-muted">AI Recipe Finder</a></li>
                        <li><a href="{{ route('compare') }}" class="text-decoration-none text-muted">Recipe Compare</a></li>
                    </ul>
                </div>
                <div class="col-md-2 col-6">
                    <h6 class="fw-bold mb-3">Platform</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2 text-muted">
                        <li><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li><a href="{{ route('meal-planner') }}" class="text-decoration-none text-muted">Meal Planner</a></li>
                        <li><a href="{{ route('shopping-list') }}" class="text-decoration-none text-muted">Shopping List</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold mb-3">Newsletter</h6>
                    <p class="text-muted">Get weekly personalized recipe recommendations direct to your inbox.</p>
                    <div class="input-group">
                        <input type="email" class="form-control rounded-start-pill border-0 px-3 bg-light" placeholder="Your email address" style="outline: none;">
                        <button class="btn btn-custom rounded-end-pill px-4" type="button">Join</button>
                    </div>
                </div>
            </div>
            <hr class="my-4 text-muted" style="opacity: 0.15;">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center text-muted fs-6">
                <span>&copy; 2026 MealIt AI Inc. All rights reserved.</span>
                <div class="d-flex gap-3 mt-3 mt-sm-0">
                    <a href="#" class="text-muted"><i class="fa-brands fa-facebook fs-5"></i></a>
                    <a href="#" class="text-muted"><i class="fa-brands fa-twitter fs-5"></i></a>
                    <a href="#" class="text-muted"><i class="fa-brands fa-instagram fs-5"></i></a>
                    <a href="#" class="text-muted"><i class="fa-brands fa-github fs-5"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Chatbot Floating UI Widget -->
    <div id="chatbot-bubble" aria-label="Open AI Chef Assistant" title="Open AI Chef Assistant">
        <i class="fa-solid fa-comment-dots fs-3"></i>
    </div>

    <div id="chatbot-window" class="glass-card flex-column">
        <div class="chatbot-header">
            <span><i class="fa-solid fa-robot me-2"></i>ChefAI Assistant</span>
            <button class="btn btn-sm btn-link text-white p-0" id="close-chat" style="outline: none; box-shadow: none;"><i class="fa-solid fa-xmark fs-5"></i></button>
        </div>
        <div class="chatbot-messages" id="chat-messages">
            <div class="chat-msg bot">
                Hello! I am **ChefAI**, your expert digital culinary companion. 🍳
                <br><br>
                How can I assist you today? You can ask me to:
                <ul>
                    <li>Explain cooking steps</li>
                    <li>Provide ingredient substitutions</li>
                    <li>Suggest custom recipe ideas</li>
                    <li>Fix common cooking mistakes</li>
                </ul>
            </div>
        </div>
        <div class="chatbot-input">
            <input type="text" id="chat-input-text" placeholder="Ask ChefAI anything..." aria-label="Type message">
            <button class="btn btn-custom p-0 d-flex justify-content-center align-items-center" id="btn-send-chat" style="width: 38px; height: 38px; border-radius: 50%;">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AOS animations -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    
    <!-- Core Application Script -->
    <script>
        // Initialize AOS
        document.addEventListener('DOMContentLoaded', () => {
            AOS.init({ once: true, offset: 50, duration: 600, easing: 'ease-out-cubic' });
        });

        // Dark/Light Theme Switching
        const themeBtn = document.getElementById('theme-toggle');
        const rootHtml = document.documentElement;
        
        // Load initial theme
        let currentTheme = localStorage.getItem('theme') || 'light';
        rootHtml.setAttribute('data-theme', currentTheme);
        updateThemeIcon(currentTheme);

        themeBtn.addEventListener('click', () => {
            currentTheme = rootHtml.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            rootHtml.setAttribute('data-theme', currentTheme);
            localStorage.setItem('theme', currentTheme);
            updateThemeIcon(currentTheme);
            
            // Optional patch to user preference in background
            @auth
                fetch("{{ route('profile.dark-mode') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ dark_mode: currentTheme === 'dark' })
                });
            @endauth
        });

        function updateThemeIcon(theme) {
            const icon = themeBtn.querySelector('i');
            if (theme === 'dark') {
                icon.className = 'fa-solid fa-sun';
            } else {
                icon.className = 'fa-solid fa-moon';
            }
        }

        // Chatbot Widget logic
        const bubble = document.getElementById('chatbot-bubble');
        const win = document.getElementById('chatbot-window');
        const closeBtn = document.getElementById('close-chat');
        const sendBtn = document.getElementById('btn-send-chat');
        const textInput = document.getElementById('chat-input-text');
        const msgContainer = document.getElementById('chat-messages');

        let chatHistory = [
            { sender: 'bot', text: 'Hello! I am ChefAI, your expert digital culinary companion. How can I assist you today?' }
        ];

        bubble.addEventListener('click', () => {
            win.style.display = win.style.display === 'flex' ? 'none' : 'flex';
            msgContainer.scrollTop = msgContainer.scrollHeight;
        });

        closeBtn.addEventListener('click', () => {
            win.style.display = 'none';
        });

        sendBtn.addEventListener('click', sendMessage);
        textInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendMessage();
        });

        function appendMessage(sender, text) {
            const div = document.createElement('div');
            div.className = `chat-msg ${sender}`;
            
            // Basic markdown handling for stars or bullet points
            let cleanText = text
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/\n/g, '<br>');
                
            div.innerHTML = cleanText;
            msgContainer.appendChild(div);
            msgContainer.scrollTop = msgContainer.scrollHeight;
        }

        function sendMessage() {
            const val = textInput.value.trim();
            if (!val) return;

            appendMessage('user', val);
            textInput.value = '';
            chatHistory.push({ sender: 'user', text: val });

            // Show temporary thinking bubble
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'chat-msg bot loading-dots';
            loadingDiv.innerHTML = '<i class="fa-solid fa-ellipsis fa-bounce"></i> ChefAI is thinking...';
            msgContainer.appendChild(loadingDiv);
            msgContainer.scrollTop = msgContainer.scrollHeight;

            // Call Laravel AI Chat endpoint
            fetch('{{ url("/api/ai/chat") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    messages: chatHistory
                })
            })
            .then(res => res.json())
            .then(data => {
                loadingDiv.remove();
                if (data.success) {
                    appendMessage('bot', data.reply);
                    chatHistory.push({ sender: 'bot', text: data.reply });
                } else {
                    appendMessage('bot', 'Sorry, I couldn\'t process that message. Can you try again?');
                }
            })
            .catch(() => {
                loadingDiv.remove();
                appendMessage('bot', 'Network error. Please make sure XAMPP and the AI service are running.');
            });
        }
    </script>
    @yield('scripts')
</body>
</html>
