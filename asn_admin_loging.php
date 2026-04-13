<?php
session_start(); // Ensure session_start() is the first line

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Hardcoded credentials
    $validUsername = 'kavinu17rajapaksha@gmail.com';
    $validPassword = 'kavi123';

    if ($username === $validUsername && $password === $validPassword) {
        // Successful login
        $_SESSION['loggedin'] = true;
        header('Location: admin.php');
        exit;
    } else {
        // Invalid credentials
        $error = 'Invalid username or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  
      <script>
      // Reload only if page is restored from back/forward cache (bfcache)
      window.addEventListener("pageshow", function (event) {
        if (event.persisted) {
          window.location.reload();
        }
      });

      // Block F12, Ctrl+Shift+I/J, Ctrl+U globally
      document.onkeydown = function (e) {
        // Block F12
        if (e.keyCode == 123) {
          return false;
        }
        // Block Ctrl+Shift+I
        if (e.ctrlKey && e.shiftKey && e.keyCode == 73) {
          return false;
        }
        // Block Ctrl+Shift+J
        if (e.ctrlKey && e.shiftKey && e.keyCode == 74) {
          return false;
        }
        // Block Ctrl+U
        if (e.ctrlKey && e.keyCode == 85) {
          return false;
        }
      };

      // Block right-click context menu globally
      document.addEventListener('contextmenu', function (event) {
        event.preventDefault();
        return false;
      });
      </script>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Admin Login • AsanTravels</title>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
  <style>
    :root {
      --bg: #121212;
      --surf: #1E1E1E;
      --text: #E0E0E0;
      --primary: #BB86FC;
      --accent: #03DAC6;
      --grad: linear-gradient(45deg,var(--primary),var(--accent));
      --rad: 8px;
      --dur: .3s;
    }
    html, body {
      height: 100%;
    }
    body {
      font-family:'Roboto',sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      position:relative;
      overflow:hidden;
    }
    /* 3D background shapes */
    .bg-shape {
      position:absolute;
      border-radius:50%;
      filter:blur(40px);
      opacity:0.5;
      z-index:0;
      animation: float 12s infinite linear alternate;
    }
    .bg-shape1 {
      width:400px; height:400px;
      background:linear-gradient(135deg,#BB86FC 60%,#03DAC6 100%);
      top:-80px; left:-120px;
      animation-delay:0s;
    }
    .bg-shape2 {
      width:300px; height:300px;
      background:linear-gradient(135deg,#03DAC6 60%,#BB86FC 100%);
      bottom:-60px; right:-100px;
      animation-delay:2s;
    }
    .bg-shape3 {
      width:200px; height:200px;
      background:linear-gradient(135deg,#ff9800 60%,#03DAC6 100%);
      top:60%; left:60%;
      animation-delay:4s;
    }
    @keyframes float {
      0% { transform:translateY(0) scale(1); }
      100% { transform:translateY(40px) scale(1.1); }
    }
    /* new split-card design */
    .login-card {
      width:720px;
      max-width:92vw;
      background:linear-gradient(135deg, rgba(255,255,255,0.03), rgba(255,255,255,0.02));
      backdrop-filter: blur(14px) saturate(120%);
      border-radius:16px;
      box-shadow: 0 10px 40px rgba(2,6,23,0.6), inset 0 1px 0 rgba(255,255,255,0.03);
      display:flex;
      overflow:hidden;
      z-index:1;
      transform-style: preserve-3d;
      /* entrance initial state */
      opacity: 0;
      transform: translateY(18px) scale(0.99);
      transition: transform 560ms cubic-bezier(.16,.84,.34,1), opacity 480ms ease;
    }
    .login-card.enter {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
    /* left image float (CSS-driven) */
    .card-left img.floaty { animation: floaty 6s ease-in-out infinite alternate; }
    @keyframes floaty { from { transform: translateY(-6px) } to { transform: translateY(8px) } }

    /* spinner overlay when submitting */
    .login-spinner { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; background:linear-gradient(rgba(0,0,0,0.35), rgba(0,0,0,0.35)); z-index:6; }
    .login-spinner .dots { display:flex; gap:8px; }
    .login-spinner .dot { width:12px; height:12px; border-radius:50%; background:var(--primary); opacity:0.95; animation: bounce 900ms infinite ease-in-out; }
    .login-spinner .dot:nth-child(2){ animation-delay:120ms } .login-spinner .dot:nth-child(3){ animation-delay:240ms }
    @keyframes bounce { 0%{ transform: translateY(0); opacity:0.6 } 50%{ transform: translateY(-8px); opacity:1 } 100%{ transform: translateY(0); opacity:0.6 } }

    /* shake (integrated) */
    .login-card.shake { animation: shakeit 560ms cubic-bezier(.36,.07,.19,.97); }
    @keyframes shakeit { 10%, 90% { transform: translateX(-2px) } 20%, 80% { transform: translateX(4px) } 30%,50%,70% { transform: translateX(-6px) } 40%,60% { transform: translateX(6px) } }
    .login-card .card-left {
      flex:1 1 40%;
      min-width:220px;
      background: linear-gradient(180deg, rgba(187,134,252,0.16), rgba(3,218,198,0.08));
      display:flex;
      align-items:center;
      justify-content:center;
      padding:28px;
      position:relative;
    }
    .login-card .card-left img { width:180px; height:180px; object-fit:cover; border-radius:16px; box-shadow:0 8px 30px rgba(3,218,198,0.08); border:3px solid rgba(255,255,255,0.06); }
    .login-card .card-right {
      flex:1 1 60%;
      padding:28px 36px;
      display:flex;
      flex-direction:column;
      justify-content:center;
      gap:8px;
      min-width:300px;
    }
    .login-card .card-right h2{
      margin:0 0 6px 0; font-size:1.3rem; color:var(--primary); letter-spacing:1px;
    }
    .login-card .card-right p { margin:0 0 18px 0; color:rgba(224,224,224,0.8); font-size:0.95rem }
    .md-input {
      width:80% !important;
      max-width:320px !important;
      padding:12px;
      border:none;
      border-radius:12px;
      background:rgba(255,255,255,0.1);
      color:#fff;
      margin:8px 0 8px 0 !important;
      text-align: left !important; /* left-align for the form layout */
      font-size:1rem;
      transition:background var(--dur), transform var(--dur);
      display:block;
      box-sizing:border-box;
    }
    /* center placeholder text */
    .md-input::placeholder {
      color: rgba(255,255,255,0.6);
      opacity: 0.9;
    }
    .md-input:focus {
      background:rgba(255,255,255,0.2);
      outline:none;
    }
    .btn {
      width:100%;
      padding:12px 14px;
      background:var(--grad);
      color:var(--bg);
      font-weight:600;
      border:none;
      border-radius:10px;
      cursor:pointer;
      font-size:1rem;
      transition:transform var(--dur),box-shadow var(--dur);
      display:inline-block;
      align-self:stretch;
    }
    .btn:hover {
      transform:scale(1.05);
      box-shadow:0 6px 20px rgba(0,0,0,0.6);
    }
    .login-logo {
        
      width:100px;
      height:100px;
      object-fit:contain;
      margin-bottom:16px;
      border-radius:12px;
      box-shadow:0 2px 8px rgba(0,0,0,0.2);
      background:#fff;
      padding:8px;
    }
    .error-msg {
      color: #ff5252;
      margin-bottom: 12px;
      text-align: center;
    }
    .login-container {
      perspective: 1000px;
      display: flex;
      align-items: center;
      justify-content: center;
      position: fixed; /* fill viewport and stay centered */
      inset: 0; /* top:0;right:0;bottom:0;left:0 */
      z-index: 2; /* above the blurred background shapes */
      pointer-events: none; /* allow card to receive events only */
    }
    .login-container > .login-card {
      pointer-events: auto; /* re-enable interactions for the card */
    }
  </style>
</head>
<body>
  <div class="bg-shape bg-shape1"></div>
  <div class="bg-shape bg-shape2"></div>
  <div class="bg-shape bg-shape3"></div>

  <div class="login-container">
    <form class="login-card" method="post" action="">
      <div class="card-left">
        <img src="img/asntravel logo.jpg" alt="AsanTravels Logo" />
      </div>
      <div class="card-right">
        <h2>Welcome back</h2>
        <p>Sign in to manage AsanTravels dashboard and bookings.</p>
        <!-- <div class="error-msg">Invalid username or password</div> -->
        <input type="text" name="username" class="md-input" placeholder="Username" required />
        <input type="password" name="password" class="md-input" placeholder="Password" required />
        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:8px;gap:10px;">
         
          <span id="showPwdToggle" style="cursor:pointer;user-select:none;display:flex;align-items:center;gap:4px;font-size:0.95rem;color:rgba(224,224,224,0.8);">
            <span class="material-icons" style="font-size:1.1rem;">visibility_off</span> Show Password
          </span>
        </div>
        <button type="submit" class="btn" style="margin-top:14px;background:linear-gradient(90deg,var(--primary),var(--accent));">Login</button>
      </div>
    </form>
  </div>

  <script>
    (function(){
      const card = document.querySelector('.login-card');
      const leftImg = document.querySelector('.card-left img');
      const username = document.querySelector('input[name="username"]');
      const password = document.querySelector('input[name="password"]');
      const form = document.querySelector('.login-card');
      if(!card) return;

      // enter
      requestAnimationFrame(()=> setTimeout(()=> card.classList.add('enter'), 80));

      // add floaty class to left image
      if(leftImg) leftImg.classList.add('floaty');

      // input focus glow
      [username, password].forEach(input => {
        if(!input) return;
        input.addEventListener('focus', ()=> input.style.boxShadow = '0 6px 20px rgba(187,134,252,0.08)');
        input.addEventListener('blur', ()=> input.style.boxShadow = 'none');
      });

      // Show/hide password toggle
      const showPwdToggle = document.getElementById('showPwdToggle');
      if(showPwdToggle && password){
        showPwdToggle.addEventListener('click', function(){
          if(password.type === 'password'){
            password.type = 'text';
            showPwdToggle.querySelector('.material-icons').textContent = 'visibility';
            showPwdToggle.style.color = '#03DAC6';
          }else{
            password.type = 'password';
            showPwdToggle.querySelector('.material-icons').textContent = 'visibility_off';
            showPwdToggle.style.color = '';
          }
        });
      }

      // spinner helper
      function showSpinner(){
        const s = document.createElement('div');
        s.className = 'login-spinner';
        s.innerHTML = '<div class="dots"><div class="dot"></div><div class="dot"></div><div class="dot"></div></div>';
        card.appendChild(s);
        return s;
      }

      // validation and spinner on submit
      const error = document.createElement('div');
      error.className = 'error-msg';
      error.style.display = 'none';
      error.style.marginTop = '8px';
      const right = document.querySelector('.card-right');
      if(right) right.appendChild(error);

      form.addEventListener('submit', (ev) => {
        const u = username && username.value.trim();
        const p = password && password.value.trim();
        if(!u || !p){
          ev.preventDefault();
          error.textContent = 'Both fields are required';
          error.style.display = 'block';
          card.classList.add('shake');
          setTimeout(()=> card.classList.remove('shake'), 700);
          return;
        }
        // show spinner overlay while submitting
        const spinner = showSpinner();
        // simulate network delay for demo; remove setTimeout in production
        setTimeout(()=>{ if(spinner && spinner.parentNode) spinner.parentNode.removeChild(spinner); }, 1200);
      });
    })();

    // Disable browser back button and redirect to admin panel
    // Disable browser back button (prevent navigation)
    (function(){
      history.pushState(null, '', location.href);
      window.addEventListener('popstate', function(e) {
        history.pushState(null, '', location.href);
      });
    })();
  </script>
  <script>
  // Reload only if page is restored from back/forward cache (bfcache)
  window.addEventListener("pageshow", function (event) {
    if (event.persisted) {
      window.location.reload();
    }
  });

  // Prevent forward navigation to protected pages
  (function(){
    // Push a new state so forward navigation is blocked
    history.pushState(null, '', location.href);
    window.addEventListener('popstate', function(e) {
      // If user tries to go forward, stay on login page
      if (history.forward) {
        location.replace(location.href);
      }
    });
  })();
</script>

</body>
</html> 