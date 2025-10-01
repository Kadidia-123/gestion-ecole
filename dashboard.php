<?php
require_once './config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #7209b7;
            --success: #2ecc71;
            --info: #00b4d8;
            --warning: #f39c12;
            --danger: #e74c3c;
            --dark: #2d3748;
            --light: #f8f9fa;
            --gray: #718096;
            --light-gray: #e2e8f0;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body { 
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); 
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif; 
            margin: 0; 
            color: var(--dark);
            min-height: 100vh;
            line-height: 1.6;
        }
        
        .fullscreen-container {
            width: 100%;
            min-height: 100vh;
        }
        
        header { 
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 1.2rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 20px rgba(67, 97, 238, 0.15);
            backdrop-filter: blur(10px);
        }
        
        .header-title {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .logo {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        h1 { 
            margin: 0; 
            font-weight: 700;
            font-size: 1.8rem;
            letter-spacing: -0.5px;
        }
        
        nav ul { 
            list-style: none; 
            padding: 0; 
            display: flex; 
            gap: 0.5rem; 
            margin: 0;
            flex-wrap: wrap;
        }
        
        nav a { 
            text-decoration: none; 
            color: white; 
            font-weight: 500; 
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.7rem 1rem;
            border-radius: 8px;
            white-space: nowrap;
            font-size: 0.9rem;
            position: relative;
            overflow: hidden;
        }
        
        nav a::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: white;
            transition: width 0.3s ease;
        }
        
        nav a:hover::before {
            width: 100%;
        }
        
        nav a:hover { 
            background: rgba(255,255,255,0.1); 
            transform: translateY(-2px);
        }
        
        nav a i {
            font-size: 1rem;
        }
        
        .logout-btn {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .logout-btn:hover {
            background: rgba(255,255,255,0.25);
        }
        
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 6px;
            transition: background 0.3s;
        }
        
        .menu-toggle:hover {
            background: rgba(255,255,255,0.1);
        }
        
        main {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }
        
        .welcome-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border-left: 6px solid var(--primary);
            position: relative;
            overflow: hidden;
        }
        
        .welcome-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 150px;
            height: 150px;
            background: linear-gradient(135deg, rgba(67, 97, 238, 0.05) 0%, rgba(114, 9, 183, 0.05) 100%);
            border-radius: 0 0 0 100%;
        }
        
        .welcome-card h2 {
            margin-top: 0;
            color: var(--primary);
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }
        
        .welcome-card p {
            color: var(--gray);
            font-size: 1.1rem;
            max-width: 600px;
        }
        
        .stats { 
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem; 
            margin-bottom: 2.5rem;
        }
        
        .stat-card { 
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 1.8rem;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border-top: 4px solid var(--primary);
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover::before {
            transform: scaleX(1);
        }
        
        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        }
        
        .stat-card h3 { 
            margin: 0 0 1rem 0; 
            font-size: 1rem; 
            color: var(--gray);
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .stat-card p { 
            font-size: 2.8rem; 
            margin: 0; 
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stat-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 1rem;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(67, 97, 238, 0.1) 0%, rgba(114, 9, 183, 0.1) 100%);
            color: var(--primary);
            font-size: 1.8rem;
        }
        
        .quick-actions {
            margin-top: 2.5rem;
        }
        
        .quick-actions h2 {
            color: var(--dark);
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        
        .quick-actions h2::after {
            content: '';
            flex: 1;
            height: 2px;
            background: linear-gradient(90deg, var(--primary), transparent);
            margin-left: 1rem;
        }
        
        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
        }
        
        .action-btn {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            padding: 1.2rem 1.5rem;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 0.8rem;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .action-btn:hover::before {
            left: 100%;
        }
        
        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.4);
        }
        
        .action-btn i {
            font-size: 1.2rem;
        }
        
        footer {
            background: var(--dark);
            color: white;
            text-align: center;
            padding: 1.5rem;
            margin-top: 3rem;
        }
        
        /* Animation pour le chargement */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .stat-card, .welcome-card, .action-btn {
            animation: fadeIn 0.6s ease-out;
        }
        
        /* Responsive Design */
        @media (max-width: 1200px) {
            main {
                padding: 1.5rem;
            }
            
            .stats {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
        }
        
        @media (max-width: 992px) {
            header {
                padding: 1rem 1.5rem;
            }
            
            nav ul {
                gap: 0.3rem;
            }
            
            nav a {
                padding: 0.6rem 0.8rem;
                font-size: 0.85rem;
            }
        }
        
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
                position: relative;
                padding: 1rem;
            }
            
            .menu-toggle {
                display: block;
                position: absolute;
                top: 1rem;
                right: 1rem;
            }
            
            nav {
                width: 100%;
                display: none;
            }
            
            nav.active {
                display: block;
            }
            
            nav ul {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            nav a {
                justify-content: flex-start;
                padding: 0.8rem 1rem;
                border-radius: 8px;
                background: rgba(255,255,255,0.05);
            }
            
            .stats {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .action-buttons {
                grid-template-columns: 1fr;
            }
            
            main {
                padding: 1rem;
            }
            
            .welcome-card {
                padding: 1.5rem;
            }
            
            .stat-card {
                padding: 1.5rem;
            }
        }
        
        @media (max-width: 480px) {
            h1 {
                font-size: 1.5rem;
            }
            
            .welcome-card h2 {
                font-size: 1.5rem;
            }
            
            .stat-card p {
                font-size: 2.2rem;
            }
            
            .action-btn {
                padding: 1rem 1.2rem;
            }
        }
    </style>
</head>
<body>
    <div class="fullscreen-container">
        <header>
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="header-title">
                <div class="logo">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1>Tableau de Bord Admin</h1>
            </div>
            <nav id="mainNav">
                <ul>
                    <li><a href="eleves.php"><i class="fas fa-users"></i> Élèves</a></li>
                    <li><a href="enseignants.php"><i class="fas fa-chalkboard-teacher"></i> Enseignants</a></li>
                    <li><a href="classes.php"><i class="fas fa-school"></i> Classes</a></li>
                    <li><a href="matieres.php"><i class="fas fa-book"></i> Matières</a></li>
                    <li><a href="notes.php"><i class="fas fa-edit"></i> Notes</a></li>
                    <li><a href="bulletin.php"><i class="fas fa-file-alt"></i> Bulletin</a></li>
                    <li><a href="paiements.php"><i class="fas fa-credit-card"></i> Paiements</a></li>
                    <li><a href="attribution_cours.php"><i class="fas fa-tasks"></i> Attribution cours</a></li>
                    <li><a href="emplois_temps.php"><i class="fas fa-calendar-alt"></i> Emplois du temps</a></li>
                    <li><a href="absences.php"><i class="fas fa-user-clock"></i> Absences</a></li>
                    <li><a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                </ul>
            </nav>
        </header>
        <main>
            <div class="welcome-card">
                <h2>Bienvenue, Administrateur</h2>
                <p>Gérez facilement votre établissement scolaire à partir de ce tableau de bord intuitif et moderne.</p>
            </div>
            
            <section class="stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Élèves inscrits</h3>
                    <?php
                    $sql = "SELECT COUNT(*) FROM eleves";
                    $count = $pdo->query($sql)->fetchColumn();
                    echo "<p>$count</p>";
                    ?>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h3>Enseignants</h3>
                    <?php
                    $sql = "SELECT COUNT(*) FROM enseignants";
                    $count = $pdo->query($sql)->fetchColumn();
                    echo "<p>$count</p>";
                    ?>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-school"></i>
                    </div>
                    <h3>Classes</h3>
                    <?php
                    $sql = "SELECT COUNT(*) FROM classes";
                    $count = $pdo->query($sql)->fetchColumn();
                    echo "<p>$count</p>";
                    ?>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h3>Matières</h3>
                    <?php
                    $sql = "SELECT COUNT(*) FROM matieres";
                    $count = $pdo->query($sql)->fetchColumn();
                    echo "<p>$count</p>";
                    ?>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h3>Notes</h3>
                    <?php
                    $sql = "SELECT COUNT(*) FROM notes";
                    $count = $pdo->query($sql)->fetchColumn();
                    echo "<p>$count</p>";
                    ?>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h3>Paiements</h3>
                    <?php
                    $sql = "SELECT COUNT(*) FROM paiements";
                    $count = $pdo->query($sql)->fetchColumn();
                    echo "<p>$count</p>";
                    ?>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h3>Attributions cours</h3>
                    <?php
                    $sql = "SELECT COUNT(*) FROM attribution_cours";
                    $count = $pdo->query($sql)->fetchColumn();
                    echo "<p>$count</p>";
                    ?>
                </div>
            </section>
            
            <section class="quick-actions">
                <h2><i class="fas fa-bolt"></i> Actions rapides</h2>
                <div class="action-buttons">
                    <a href="eleves.php?action=add" class="action-btn">
                        <i class="fas fa-user-plus"></i> Ajouter un élève
                    </a>
                    <a href="enseignants.php?action=add" class="action-btn">
                        <i class="fas fa-plus-circle"></i> Ajouter un enseignant
                    </a>
                    <a href="classes.php?action=add" class="action-btn">
                        <i class="fas fa-plus"></i> Créer une classe
                    </a>
                    <a href="matieres.php?action=add" class="action-btn">
                        <i class="fas fa-book-medical"></i> Ajouter une matière
                    </a>
                    <a href="notes.php?action=add" class="action-btn">
                        <i class="fas fa-edit"></i> Saisir des notes
                    </a>
                    <a href="bulletin.php" class="action-btn">
                        <i class="fas fa-file-alt"></i> Générer un bulletin
                    </a>
                    <a href="paiements.php?action=add" class="action-btn">
                        <i class="fas fa-money-bill-wave"></i> Enregistrer un paiement
                    </a>
                    <a href="attribution_cours.php" class="action-btn">
                        <i class="fas fa-tasks"></i> Attribuer des cours
                    </a>
                    <a href="emplois_temps.php" class="action-btn">
                        <i class="fas fa-calendar-alt"></i> Emplois du temps
                    </a>
                    <a href="absences.php" class="action-btn">
                        <i class="fas fa-user-clock"></i> Gérer les absences
                    </a>
                </div>
            </section>
        </main>
        
        <footer>
            <p>&copy; <?php echo date('Y'); ?> - Système de Gestion Scolaire | Tous droits réservés</p>
        </footer>
    </div>

    <script>
        // Menu toggle for mobile
        document.getElementById('menuToggle').addEventListener('click', function() {
            const nav = document.getElementById('mainNav');
            nav.classList.toggle('active');
            
            const icon = this.querySelector('i');
            if (nav.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const nav = document.getElementById('mainNav');
            const menuToggle = document.getElementById('menuToggle');
            
            if (!nav.contains(event.target) && !menuToggle.contains(event.target) && window.innerWidth <= 768) {
                nav.classList.remove('active');
                const icon = menuToggle.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
        
        // Adjust menu on window resize
        window.addEventListener('resize', function() {
            const nav = document.getElementById('mainNav');
            const menuToggle = document.getElementById('menuToggle');
            const icon = menuToggle.querySelector('i');
            
            if (window.innerWidth > 768) {
                nav.style.display = 'block';
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            } else {
                nav.style.display = 'none';
            }
        });
        
        // Animation on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const statCards = document.querySelectorAll('.stat-card');
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animation = 'fadeIn 0.6s ease-out forwards';
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);
            
            statCards.forEach(card => {
                observer.observe(card);
            });
        });
    </script>
</body>
</html>