<?php
// Database configuration
define('DB_HOST', 'localhost');
define("DB_NAME", "alwaysgr_samfed");
define("DB_USER", "alwaysgr_samfed");
define("DB_PASS", "hyqAud7r2Brbb6xPybJv");
define('DB_CHARSET', 'utf8mb4');

// DB connection
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        return false;
    }
}

// Get approved feedback (recent N)
$feedback_list = [];
$limit = 6; // change as you like for the preview section
try {
    $pdo = getDBConnection();
    if ($pdo) {
        $stmt = $pdo->prepare("
            SELECT id, name, country, feedback, created_at
            FROM feedback
            WHERE status = 'approved'
            ORDER BY created_at DESC
            LIMIT :lim
        ");
        $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        $feedback_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    // fail silently on public page
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="description" content="Samuel Sunday – Professional screenwriter & storyteller. Specializing in novel and script adaptation for film and television.">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Samuel Sunday | Screenwriter</title>

  <!-- Bootstrap CSS -->
  <link href="bootstrap-5.3.6-dist\css\bootstrap.min.css" rel="stylesheet">

<!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="favicon_io/favicon.ico">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display :wght@700&family=Roboto&display=swap" rel="stylesheet">

  

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css ">

  <!-- AOS Animation CSS -->
  <link href="https://unpkg.com/aos @2.3.4/dist/aos.css" rel="stylesheet">

  <!-- Custom CSS -->
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      scroll-behavior: smooth;
    }

    .navbar {
      background-color: #0a0a0a;
    }

    .navbar-brand, .nav-link {
      color: #fff !important;
    }

    .hero {
      background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)),
                  url('images/hero-about.jpg') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      color: white;
      padding: 5rem 2rem;
    }

    .hero h1 {
      font-family: 'Playfair Display', serif;
      font-size: 3rem;
    }

    .btn-custom {
      background-color: #f1c40f;
      border: none;
      color: #000;
      font-weight: bold;
    }

    .btn-custom:hover {
      background-color: #e1b300;
    }

.navbar-brand img {
transition: transform 0.3s ease;
}

.navbar-brand:hover img {
transform: rotate(5deg); /* Subtle hover effect */
}

.hero .img-fluid {
transition: transform 0.3s ease;
}

.hero .img-fluid:hover {
transform: scale(1.05);
}
    .section-title {
      font-size: 2rem;
      margin-bottom: 1.5rem;
      position: relative;
      padding-bottom: 0.5rem;
    }

    .section-title::after {
      content: '';
      width: 60px;
      height: 3px;
      background: #f1c40f;
      position: absolute;
      bottom: 0;
      left: 0;
    }

    .card {
      transition: transform 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .gig-image {
  width: 350px;
  height: 200px;
  object-fit: cover;
  cursor: pointer;
  transition: transform 0.3s ease;
}

.gig-image:hover {
  transform: scale(1.03);
}

/* --- Feedback only styling --- */
.feedback-grid {
display: grid;
grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
gap: 22px;
margin-top: 24px;
}

.feedback-card {
background: #fff;
border-radius: 12px;
box-shadow: 0 2px 10px rgba(0,0,0,.08);
border-left: 4px solid #3498db;
padding: 22px;
display: flex;
flex-direction: column;
min-height: 160px;
}

.feedback-header {
display: flex;
justify-content: space-between;
align-items: baseline;
gap: 16px;
margin-bottom: 10px;
flex-wrap: wrap;
}

.client-name {
font-size: 1.05rem;
font-weight: 700;
color: #666600;
}

.client-country {
color: #7f8c8d;
font-size: .92rem;
}

.feedback-date {
color: #95a5a6;
font-size: .85rem;
white-space: nowrap;
}

.feedback-text {
color: #555;
line-height: 1.65;
margin-top: 8px;
white-space: pre-line; /* keep line breaks */
}

.no-feedback {
text-align: center;
padding: 42px;
color: #7f8c8d;
font-size: 1.05rem;
}

/* See-all button */
.feedback-cta-wrap {
display: flex;
justify-content: center;
margin-top: 24px;
}

.see-all-btn {
background: #27ae60;
color: #fff;
text-decoration: none;
border: none;
border-radius: 8px;
padding: 10px 18px;
font-size: 15px;
cursor: pointer;
}

.see-all-btn:hover {
background: #229954;
}

.feedback-marquee {
  overflow: hidden;
  position: relative;
  width: 100%;
}

.feedback-track {
  display: flex;
  width: max-content;
  animation: scroll-left 30s linear infinite;
}

.feedback-card {
  flex: 0 0 320px; /* card width */
  margin-right: 20px;
  border-radius: 10px;
}

@keyframes scroll-left {
  from { transform: translateX(0); }
  to { transform: translateX(-50%); } /* moves left continuously */
}



    footer {
      background-color: #0a0a0a;
      color: #ccc;
      padding: 2rem 0;
      text-align: center;
    }
  </style>
</head>
<!-- Google Analytics (GA4) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>
    window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-2EGE8E1MJ8');
</script>

<body data-bs-spy="scroll" data-bs-target="#mainNav">

  <!-- Navbar -->
<!-- Navbar -->
<nav class="navbar navbar-expand-lg" id="mainNav">
    <div class="container">
        <!-- Logo + Brand -->
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="nav-samscript.png" alt="Logo" width="100" height="30 class="me-2"> <!-- Adjust path as needed -->
          
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="#portfolio">Portfolio</a></li>
                <?php /* <li class="nav-item"><a class="nav-link" href="#colleagues">Colleagues</a></li> */ ?>
                <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section id="home" class="hero d-flex align-items-center text-white text-center">
    <div class="container">
        <!-- Logo -->
        <div class="mb-4" data-aos="zoom-in">
            <img src="sam-script.png" alt="Samuel Sunday Logo" class="img-fluid" style="max-width: 350px;">
        </div>

        <!-- Name -->
        <!-- <h1 class="display-4 fw-bold mb-3">Samuel Sunday</h1> -->

        <!-- Tagline -->
        <p class="lead mb-4">Screenwriter | Storyteller | Novel & Script Adaptation</p>

        <!-- Description -->
        <p class="mb-4">Turning stories into screenplays and novels into cinematic experiences.</p>

        <!-- CTA Buttons -->
        <a href="#feedback" class="btn btn-custom btn-lg me-3">What People Say</a>
        <a href="https://www.fiverr.com/dsunsamg" target="_blank" class="btn btn-outline-light btn-lg">Contact Me</a>
    </div>
</section>

  <!-- About Section -->
  <section id="about" class="py-5" data-aos="fade-up">
    <div class="container text-center">
      <h2 class="section-title">About Me</h2>
      <p class="lead">I’m a professional screenwriter and storyteller who transforms novels into compelling scripts and scripts into unforgettable stories. With a passion for narrative depth and cinematic vision, I bring characters and worlds to life through the art of writing.</p>
    </div>
  </section>

  <!-- Services Section -->
  <section id="services" class="py-5 bg-light" data-aos="fade-up">
    <div class="container">
      <h2 class="section-title text-center mb-4">Services</h2>
      <div class="row g-4">
        <div class="col-md-6 col-lg-4">
          <div class="card p-4 shadow-sm h-100">
            <i class="fas fa-film fa-2x text-warning mb-3"></i>
            <h5 class="card-title">Movie Script Writing</h5>
            <p class="card-text">Crafting engaging screenplays tailored to your vision and genre, ensuring emotional resonance and cinematic impact.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="card p-4 shadow-sm h-100">
            <i class="fas fa-book fa-2x text-warning mb-3"></i>
            <h5 class="card-title">Novel Adaptation</h5>
            <p class="card-text">Bringing literary works to the screen while preserving their essence and transforming them into visual narratives.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 mx-auto">
          <div class="card p-4 shadow-sm h-100">
            <i class="fas fa-pencil-alt fa-2x text-warning mb-3"></i>
            <h5 class="card-title">Story Development</h5>
            <p class="card-text">Refining ideas into powerful stories, helping writers and creators develop structure, tone, and character arcs.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

<!-- Portfolio Section -->
<section id="portfolio" class="py-5" data-aos="fade-up">
  <div class="container">
    <h2 class="section-title text-center mb-4">Featured Projects</h2>
    <div class="row g-4">
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm">
          <img src="images/novel-to-screenplay.jpg"  alt="Project 1" class="card-img-top">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">Screenplay</h5>
            <p class="card-text">Adapting a bestselling mystery novel into a gripping screenplay.</p>
            <a href="#project1Modal" data-bs-toggle="modal" class="btn btn-custom mt-auto">View Project</a>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm">
          <img src="images/drama-series.jpg"  alt="Project 2" class="card-img-top">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">Drama Series</h5>
            <p class="card-text">Creating a compelling drama series concept and pilot script.</p>
            <a href="#project2Modal" data-bs-toggle="modal" class="btn btn-custom mt-auto">View Project</a>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm">
          <img src="images/novel-writing.jpg"  alt="Project 3" class="card-img-top">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">Novel</h5>
            <p class="card-text">A futuristic world where memories are currency — explored through character-driven storytelling.</p>
            <a href="#project3Modal" data-bs-toggle="modal" class="btn btn-custom mt-auto">View Project</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Modal for Project 1 -->
<div class="modal fade" id="project1Modal" tabindex="-1" aria-labelledby="project1Label" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="project1Label">Sample Screenplay</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="min-height: 600px;">
                <iframe
                    src="https://docs.google.com/gview?url=https%3A%2F%2Fwww.samuelscript.com%2Fsamples%2Fproject1.pdf&embedded=true"
                    style="width:100%; height:600px;" frameborder="0">
                </iframe>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Project 2 -->
<div class="modal fade" id="project2Modal" tabindex="-1" aria-labelledby="project2Label" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="project2Label">Sample Drama Series</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="min-height: 600px;">
                <iframe
                    src="https://docs.google.com/gview?url=https%3A%2F%2Fwww.samuelscript.com%2Fsamples%2Fproject2.pdf&embedded=true"
                    style="width:100%; height:600px;" frameborder="0">
                </iframe>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Project 3 -->
<div class="modal fade" id="project3Modal" tabindex="-1" aria-labelledby="project3Label" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="project3Label">Sample Novel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="min-height: 600px;">
                <iframe
                    src="https://docs.google.com/gview?url=https%3A%2F%2Fwww.samuelscript.com%2Fsamples%2Fproject3.pdf&embedded=true"
                    style="width:100%; height:600px;" frameborder="0">
                </iframe>
            </div>
        </div>
    </div>
</div>


<?php /*
<!-- Colleagues Section -->
<section id="colleagues" class="py-5 bg-light" data-aos="fade-up">
  <div class="container">
    <h2 class="section-title text-center mb-4">My Colleagues</h2>
    <div class="row g-4 justify-content-center">
      <div class="col-md-6 col-lg-4">
        <div class="card text-center shadow-sm h-100">
          <a href="#" data-bs-toggle="modal" data-bs-target="#colleague1Modal">
            <img src="images/be-your-script-writer-do-movie-script-writing-screenplay-short-film-feature.png"  alt="Colleague 1 Gig Image" class="card-img-top gig-image mx-auto">
          </a>
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">Mikel Bride</h5>
            <p class="card-text">Screenwriter & Editor</p>
            <p class="small text-muted">Specializing in character-driven stories and novel adaptations.</p>
            <div class="mt-auto">
              <a href="https://www.fiverr.com/mikelmcbride"  target="_blank" class="text-dark me-3"><i class="fab fa-fiverr fa-lg"> <img src="fiverr.png" height="15px " alt=""></i></a>
              <a href="https://wa.me/44"  target="_blank" class="text-success"><i class="fab fa-whatsapp fa-lg"></i></a>
            </div>
          </div>
        </div>
      </div>

      <!-- <div class="col-md-6 col-lg-4">
        <div class="card text-center shadow-sm h-100">
          <a href="#" data-bs-toggle="modal" data-bs-target="#colleague2Modal">
            <img src="https://via.placeholder.com/512x230"  alt="Colleague 2 Gig Image" class="card-img-top gig-image mx-auto">
          </a>
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">John Smith</h5>
            <p class="card-text">Script Consultant</p>
            <p class="small text-muted">Helping writers develop strong scripts that stand out in Hollywood.</p>
            <div class="mt-auto">
              <a href="https://www.fiverr.com/johnsmith"  target="_blank" class="text-dark me-3"><i class="fab fa-fiverr fa-lg"><img src="fiverr.png" height="15px " alt=""></i></a>
              <a href="https://wa.me/0987654321"  target="_blank" class="text-success"><i class="fab fa-whatsapp fa-lg"></i></a>
            </div>
          </div>
        </div>
      </div> -->

      <div class="col-md-6 col-lg-4">
        <div class="card text-center shadow-sm h-100">
          <a href="#" data-bs-toggle="modal" data-bs-target="#colleague3Modal">
            <img src="images/movie-script-screenplay-tv-script-screenwriting-film-script-short-film-recap.png"  alt="Colleague 3 Gig Image" class="card-img-top gig-image mx-auto">
          </a>
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">Ingrid Cline</h5>
            <p class="card-text">Story Writer</p>
            <p class="small text-muted">Iingrid writes and structure powerful narratives and compelling arcs.</p>
            <div class="mt-auto">
              <a href="https://www.fiverr.com/ingridwriter001"  target="_blank" class="text-dark me-3"><i class="fab fa-fiverr fa-lg"><img src="fiverr.png" height="15px " alt=""></i></a>
              <a href="https://wa.me/+447411247401"  target="_blank" class="text-success"><i class="fab fa-whatsapp fa-lg"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal for Colleague 1 -->
  <div class="modal fade" id="colleague1Modal" tabindex="-1" aria-labelledby="colleague1Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="colleague1Label">Mikel Bride - Screenwriter & Editor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <img src="images/be-your-script-writer-do-movie-script-writing-screenplay-short-film-feature.png"  alt="mikelmcbride Full View" class="img-fluid">
          <p class="mt-3">Mikel specializes in character-driven storytelling and adapting novels into cinematic scripts. He has worked with both indie authors and production houses. <a href="https://www.fiverr.com/mikelmcbride"> Learn more</a></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal for Colleague 2 -->
  <!-- <div class="modal fade" id="colleague2Modal" tabindex="-1" aria-labelledby="colleague2Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="colleague2Label">John Smith - Script Consultant</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <img src="https://via.placeholder.com/800x400"  alt="John Smith Full View" class="img-fluid">
          <p class="mt-3">John helps screenwriters refine their scripts for clarity, pacing, and marketability. He has consulted on over 50 feature scripts and TV pilots.<a href="https://www.fiverr.com/mikelmcbride"> Learn more</a></p>
        </div>
      </div>
    </div>
  </div> -->

  <!-- Modal for Colleague 3 -->
  <div class="modal fade" id="colleague3Modal" tabindex="-1" aria-labelledby="colleague3Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="colleague3Label">Ingrid Cline - Story Writer</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <img src="images/movie-script-screenplay-tv-script-screenwriting-film-script-short-film-recap.png"  alt="Ingrid Full View" class="img-fluid">
          <p class="mt-3">Ingrid write and build strong story structures, create multidimensional characters, and write emotionally resonant scenes.<a href="https://www.fiverr.com/ingridwriter001"> Learn more</a></p>
        </div>
      </div>
    </div>
  </div>
</section>
*/ ?>

<!--<section id="feedback" class="py-5" data-aos="fade-up">
  <div class="container">
    <h2 class="text-center mb-5">What clients say</h2>

    <div class="row g-4">
      <?php if (empty($feedback_list)): ?>
        <div class="col-12">
          <div class="no-feedback text-center p-5">
            <h3>No feedback available yet.</h3>
            <p>Be the first to share your experience!</p>
          </div>
        </div>
      <?php else: ?>
        <?php foreach ($feedback_list as $fb): ?>
          <div class="col-md-4">
            <article class="feedback-card h-100 p-4">
              <div class="feedback-header d-flex justify-content-between align-items-start mb-3 flex-wrap">
                <div>
                  <div class="client-name fw-bold text-success">
                    <?php echo htmlspecialchars($fb['name']); ?>
                  </div>
                  <?php if (!empty($fb['country'])): ?>
                    <div class="client-country text-muted small">
                      <?php echo htmlspecialchars($fb['country']); ?>
                    </div>
                  <?php endif; ?>
                </div>
                <div class="feedback-date text-muted small">
                  <?php
                    $date = $fb['created_at'] ? date('M j, Y', strtotime($fb['created_at'])) : '';
                    echo htmlspecialchars($date);
                  ?>
                </div>
              </div>
              <div class="feedback-text">
                <?php echo nl2br(htmlspecialchars($fb['feedback'])); ?>
              </div>
            </article>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <?php if (!empty($feedback_list)): ?>
      <div class="text-center mt-4">
        <a class="btn btn-custom btn-lg mt-3" href="https://feedback.samuelscript.com/">
          See all feedback
        </a>
      </div>
    <?php endif; ?>
  </div>
</section> -->

<hr>

<section id="feedback" class="py-5 bg-light" data-aos="fade-up">
  <div class="container">
    <h2 class="text-center mb-5">What clients say</h2>

    <?php if (empty($feedback_list)): ?>
      <div class="no-feedback text-center p-5">
        <h3>No feedback available yet.</h3>
        <p>Be the first to share your experience!</p>
      </div>
    <?php else: ?>
      <div class="feedback-marquee">
        <div class="feedback-track">
          <?php foreach ($feedback_list as $fb): ?>
            <div class="feedback-card card shadow-sm">
              <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap">
                  <div>
                    <h6 class="mb-0 fw-bold text-success">
                      <?php echo htmlspecialchars($fb['name']); ?>
                    </h6>
                    <?php if (!empty($fb['country'])): ?>
                      <small class="text-muted"><?php echo htmlspecialchars($fb['country']); ?></small>
                    <?php endif; ?>
                  </div>
                  <small class="text-muted">
                    <?php
                      $date = $fb['created_at'] ? date('M j, Y', strtotime($fb['created_at'])) : '';
                      echo htmlspecialchars($date);
                    ?>
                  </small>
                </div>
                <p class="card-text text-secondary flex-grow-1">
                  <?php echo nl2br(htmlspecialchars($fb['feedback'])); ?>
                </p>
              </div>
            </div>
          <?php endforeach; ?>

          <!-- Duplicate cards for smooth infinite loop -->
          <!--<?php foreach ($feedback_list as $fb): ?>
            <div class="feedback-card card shadow-sm">
              <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap">
                  <div>
                    <h6 class="mb-0 fw-bold text-success">
                      <?php echo htmlspecialchars($fb['name']); ?>
                    </h6>
                    <?php if (!empty($fb['country'])): ?>
                      <small class="text-muted"><?php echo htmlspecialchars($fb['country']); ?></small>
                    <?php endif; ?>
                  </div>
                  <small class="text-muted">
                    <?php
                      $date = $fb['created_at'] ? date('M j, Y', strtotime($fb['created_at'])) : '';
                      echo htmlspecialchars($date);
                    ?>
                  </small>
                </div>
                <p class="card-text text-secondary flex-grow-1">
                  <?php echo nl2br(htmlspecialchars($fb['feedback'])); ?>
                </p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div> -->

    
    <?php endif; ?>

      
  </div>
  <div class="text-center mt-4">
        <a class="btn btn-custom btn-lg" href="https://feedback.samuelscript.com/">
          See all feedback
        </a>
      </div>
</section>


<hr>
<!-- Contact Section -->
<section id="contact" class="py-5" data-aos="fade-up">
    <div class="container text-center">
        <h2 class="section-title mb-4">Get in Touch</h2>
        <p class="lead mb-4">Have a project or idea you'd like to discuss? Reach out and let's create something amazing
            together.</p>

        <!-- Success Message (Initially Hidden) -->
        <div id="successMessage" class="alert alert-success d-none mx-auto mb-4" style="max-width: 600px;">
            Your message has been sent successfully! 🚀
        </div>

        <!-- Error Message -->
        <div id="errorMessage" class="alert alert-danger d-none mx-auto mb-4" style="max-width: 600px;">
            There was a problem sending your message. Please try again.
        </div>

        <!-- Contact Form -->
        <form id="contactForm" class="row g-3 justify-content-center" action="contact.php" method="POST">
            <!-- Anti-Spam Honeypot Field (Hidden from human visitors, caught by automated bots) -->
            <div style="display:none !important;" aria-hidden="true">
                <label for="website">Leave this field blank</label>
                <input type="text" name="website" id="website" autocomplete="off" tabindex="-1">
                <input type="hidden" name="form_time" value="<?php echo time(); ?>">
            </div>

            <div class="col-md-5">
                <input type="text" class="form-control" name="name" placeholder="Your Name" maxlength="70" required>
            </div>
            <div class="col-md-5">
                <input type="email" class="form-control" name="email" placeholder="Your Email" maxlength="100" required>
            </div>
            <div class="col-md-10">
                <textarea class="form-control" name="message" rows="4" placeholder="Your Message" maxlength="3000" required></textarea>
            </div>
            <div class="col-md-10 text-center">
                <button type="submit" class="btn btn-custom btn-lg mt-3">
          <i class="far fa-paper-plane me-2"></i>Send Message
        </button>
            </div>
        </form>
    </div>
</section>

  <!-- Footer -->
  <footer id="footer">
    <p>&copy; <span id='year'></span> Samuel Sunday. All Rights Reserved.</p>
    <div>
      <a href="#" class="text-white me-3"><i class="fab fa-linkedin fa-lg"></i></a>
      <a href="https://x.com/samuel__script" class="text-white me-3"><i class="fab fa-twitter fa-lg"></i></a>
      <a href="#" class="text-white me-3"><i class="fab fa-facebook fa-lg"></i></a>
      <a href="https://wa.me/+2347041899711" target="_blank" class="text-white me-3"><i class="fab fa-whatsapp fa-lg"></i></a>
    </div>
  </footer>

  <!-- Bootstrap JS Bundle -->
  <script src="bootstrap-5.3.6-dist\js\bootstrap.min.js"></script>

  <!-- AOS Animation JS -->
  <script src="https://unpkg.com/aos @2.3.4/dist/aos.js"></script>

  <!-- Initialize AOS -->
  <script>
    AOS.init({
      duration: 1000,
      once: true
    });
  </script>

<script>
  document.getElementById("year").textContent = new Date().getFullYear();
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const successMsg = document.getElementById('successMessage');
    const errorMsg = document.getElementById('errorMessage');

    if (status === 'success' && successMsg) {
      successMsg.classList.remove('d-none');
      setTimeout(() => successMsg.classList.add('d-none'), 5000);
    }

    if (status === 'error' && errorMsg) {
      errorMsg.classList.remove('d-none');
      setTimeout(() => errorMsg.classList.add('d-none'), 5000);
    }
  });
</script>
<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1231479185106878');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=1231479185106878&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->
</body>
</html>