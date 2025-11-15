<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta name="robots" content="noindex, nofollow">

  <title></title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans|Roboto|Poppins" rel="stylesheet">
  <!-- Vendor CSS Files -->
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
  <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #141414;
        margin: 0;
        padding: 0;
        height: 100vh;
        color: #fff;
    }
    .btn-buy {
        background: linear-gradient(42deg, #5846f9 0%, #7b27d8 100%);
        display: inline-block;
        padding: 10px 35px;
        border-radius: 4px;
        color: #fff;
        transition: none;
        font-size: 15px;
        font-weight: 400;
        font-family: "Roboto", sans-serif;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn-buy:hover {
        background: linear-gradient(to right, #ff416c, #ff4b2b);
        color: #fff;
    }
    /* Custom scrollbar for WebKit browsers (Chrome, Safari, etc.) */
#support::-webkit-scrollbar {
    width: 5px; /* Set the scrollbar width (you can make it thinner if needed) */
}

#support::-webkit-scrollbar-thumb {
  background-color: #f79489;
  border-radius: 2px;
  border: 2px solid #f79489;
}

#support::-webkit-scrollbar-thumb:hover {
    background-color: #f79489; /* Color when hovering over the scrollbar */
}

#support::-webkit-scrollbar-track {
   background: #ebcac7;
}

iframe[title*="chat"] a[class*="tawk-branding"],
iframe[title*="chat"] a[class*="tawk-button-small"] {
    display: none !important;
}

.loading:before {
    content: "";
    display: inline-block;
    border-radius: 50%;
    width: 44px;
    height: 44px;
    margin: 0 10px -6px 0;
    border: 3px solid #18d26e;
    border-top-color: #eee;
    animation: animate-loading 1s linear infinite;
}

/* For Firefox */
#support{
    scrollbar-width: thin; /* Thin scrollbar */
    scrollbar-color: #888 #f1f1f1; /* Scrollbar color (thumb and track) */
}
    .hidden {
        display: none;
    }
    @media (max-width: 576px) {
        .signin_mobile {
            background: linear-gradient(90deg, #ff416c, #ff4b2b);
        }
    }
    @media (min-width: 950px) {
        section {
            padding: 100px 450px;
        }
    }

    .iframe-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
  </style>
</head>
<body>
  <header id="header" class="fixed-top">
    <div class="container d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo"><img src="assets/img/scoobytv_logo.png" alt="logo" class="img-fluid"></a>
      <nav id="navbar" class="navbar">
        <ul>
          <li><a class="nav-link scrollto active" href="index.php">Home</a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav>
    </div>
  </header>
  <main id="main" class="iframe-container" style="">
  <h2 class="loading">Loading... </h2>
  </main>
  <script>
    // Wait for the entire page to finish loading
    window.addEventListener('load', function() {
        // Set a timeout to remove the element after 1 second
        setTimeout(function() {
            // Find the element with the 'loading' class
            var loadingElement = document.querySelector('.loading');

            // Check if the element exists, then remove it
            if (loadingElement) {
                loadingElement.remove();
            }
        }, 2000); // 1000 ms = 1 second
    });
</script>
  <!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/667e18ceeaf3bd8d4d153167/1i1e91qar';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();


Tawk_API = Tawk_API || {};
Tawk_API.onLoad = function() {
    // Hide the default widget at the corner
    Tawk_API.hideWidget();

    // Create a custom button that will open the chat
    const chatButton = document.createElement("button");
    chatButton.innerText = "Chat with us!";
    chatButton.style.position = "fixed";
    chatButton.style.top = "50%";
    chatButton.style.left = "50%";
    chatButton.style.transform = "translate(-50%, -50%)";
    chatButton.style.padding = "15px 30px";
    chatButton.style.fontSize = "18px";
    chatButton.style.backgroundColor = "#0a7cff";
    chatButton.style.color = "#fff";
    chatButton.style.border = "none";
    chatButton.style.borderRadius = "8px";
    chatButton.style.cursor = "pointer";
    document.body.appendChild(chatButton);

    // Open the chat when the button is clicked
    chatButton.addEventListener("click", function() {
        Tawk_API.maximize();
    });
};

</script>
<!--End of Tawk.to Script-->


     <script>
var removeBranding = function() {
    try {
        var iframe = document.querySelector("iframe[title*=chat]:nth-child(2)");
        if (iframe) {
            var iframeDocument = iframe.contentDocument || iframe.contentWindow.document;
            var element = iframeDocument.querySelector("a[class*=tawk-branding]");
            var elementt = iframeDocument.querySelector("a[class*=tawk-button-small]");
            if (element) {
                element.remove();
            }
            if (elementt) {
                elementt.remove();
            }
        }
    } catch (e) {
        console.error(e);
    }
}
var tick = 200;
setInterval(removeBranding, tick);
  </script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>
