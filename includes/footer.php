            </div> <!-- End container-fluid -->
        </div> <!-- End main-content -->
    </div> <!-- End wrapper -->

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Intro.js for guided tour -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/6.0.0/intro.min.js"></script>
    
    <!-- Custom scripts -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle sidebar on mobile
        const sidebarToggle = document.getElementById('sidebarToggleTop');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                document.body.classList.toggle('sidebar-toggled');
                const sidebar = document.querySelector('.sidebar');
                if (sidebar) {
                    sidebar.classList.toggle('toggled');
                }
                
                const mainContent = document.querySelector('.main-content');
                if (mainContent) {
                    if (sidebar && sidebar.classList.contains('toggled')) {
                        mainContent.style.marginLeft = '0';
                    } else {
                        mainContent.style.marginLeft = '225px';
                    }
                }
            });
        }
        
        // Close sidebar when window resizes to mobile size
        window.addEventListener('resize', function() {
            if (window.innerWidth < 768) {
                const sidebar = document.querySelector('.sidebar');
                if (sidebar) {
                    sidebar.classList.add('toggled');
                }
                
                const mainContent = document.querySelector('.main-content');
                if (mainContent) {
                    mainContent.style.marginLeft = '0';
                }
            } else {
                const sidebar = document.querySelector('.sidebar');
                if (sidebar) {
                    sidebar.classList.remove('toggled');
                }
                
                const mainContent = document.querySelector('.main-content');
                if (mainContent) {
                    mainContent.style.marginLeft = '225px';
                }
            }
        });
        
        // Activate Bootstrap tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Theme toggle functionality
        const themeToggle = document.getElementById('themeToggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', function(e) {
                e.preventDefault();
                document.body.classList.toggle('light-theme');
                document.body.classList.toggle('dark-theme');
                
                const icon = this.querySelector('i');
                if (document.body.classList.contains('light-theme')) {
                    icon.classList.replace('fa-sun', 'fa-moon');
                    localStorage.setItem('theme', 'light');
                } else {
                    icon.classList.replace('fa-moon', 'fa-sun');
                    localStorage.setItem('theme', 'dark');
                }
            });
            
            // Set initial icon based on theme
            const currentTheme = localStorage.getItem('theme') || 'dark';
            const icon = themeToggle.querySelector('i');
            if (currentTheme === 'light') {
                document.body.classList.add('light-theme');
                document.body.classList.remove('dark-theme');
                icon.classList.replace('fa-moon', 'fa-sun');
            } else {
                document.body.classList.add('dark-theme');
                document.body.classList.remove('light-theme');
                icon.classList.replace('fa-sun', 'fa-moon');
            }
        }
        
        // Tutorial functionality
        const startTutorial = document.getElementById('startTutorial');
        const startTutorialFromModal = document.getElementById('startTutorialFromModal');
        const tutorialModal = document.getElementById('tutorialModal');
        
        // Initialize IntroJS
        function initTutorial() {
            const intro = introJs();
            intro.setOptions({
                steps: [
                    {
                        element: document.querySelector('.navbar'),
                        intro: 'This is the navigation bar. You can access important features and notifications here.'
                    },
                    {
                        element: document.querySelector('.sidebar'),
                        intro: 'This sidebar contains all the main navigation options for your role.'
                    },
                    {
                        element: document.querySelector('.main-content'),
                        intro: 'This is where all your content will be displayed.'
                    },
                    {
                        element: document.querySelector('#themeToggle'),
                        intro: 'Click here to switch between dark and light themes.'
                    },
                    {
                        element: document.querySelector('#userDropdown'),
                        intro: 'Access your profile settings and logout from here.'
                    }
                ],
                showProgress: true,
                showBullets: false,
                disableInteraction: false,
                doneLabel: 'Finish',
                nextLabel: 'Next',
                prevLabel: 'Back'
            });
            
            intro.start();
        }
        
        // Show tutorial modal on first login
        if (tutorialModal) {
            const modal = new bootstrap.Modal(tutorialModal);
            modal.show();
            
            if (startTutorialFromModal) {
                startTutorialFromModal.addEventListener('click', function() {
                    modal.hide();
                    setTimeout(initTutorial, 500); // Small delay to allow modal to close
                });
            }
        }
        
        // Manual tutorial start
        if (startTutorial) {
            startTutorial.addEventListener('click', function(e) {
                e.preventDefault();
                initTutorial();
            });
        }
    });
    </script>
</body>
</html>
