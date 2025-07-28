<?php
include 'header.php';     // Nhúng header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="assets/css/style_index1.css?v=2">
</head>
<body>

    <div class="wallpaper">
        <a href="products.php" class="button">Let's take a look</a>
    </div>
    <div class="gioithieu3">
        <div class="trai">
            <img src="assets/images/SnapInsta.gg_468709140_18344231689131749_4788683110416245671_n.jpg" alt="">
            <div class="authur">
                <h2>Collection 2025.</h2>
                <p><b>Dimoi Archive.</b></p>
                <p>
                Dimoi Archive 2025 brings a masculine, youthful, and ever-evolving streetwear style. The "Just For Him" collection is the perfect blend of simplicity, sophistication, and standout details, helping men express freedom and individuality. Featuring graphic t-shirts, stylish hoodies, and dynamic jogger pants, Dimoi Archive 2025 is ready to accompany you on every street stroll or vibrant party.
                </p>
            </div>

            <div class="forbutton"><a href="products.php" class="buttonn">Check it out!  &rarr;</a></div>
            
        </div>
        <div class="phai">
            
            <div class="authur">
                <h2>She Need This!</h2>
                <p><b>Dimoi Archive.</b></p>
                <p>
                Capturing the bold trends of women's streetwear, Dimoi Archive 2025 introduces "She Need This" – a collection dedicated to women unafraid to reinvent themselves every day. From stylish crop tops and comfortable oversized dresses to ultra-cool tone-sur-tone sets, everything exudes confidence and stands out in every step. With comfortable materials and bold silhouettes, you'll always be the center of attention.
                </p>
            </div>
            <div class="forbutton"><a href="products.php" class="buttonn">Look at this! ★</a></div>
            
            <img src="assets/images/SnapInsta.gg_468708819_18344245717131749_4676157693420483298_n.jpg" alt="">
        </div>
        <div class="trai">
            <img src="assets/images/SnapInsta.gg_463196057_950213193608007_6213987161618416418_n.jpg" alt="">
            <div class="authur">
                <h2>Just For Him. </h2>
                <p><b>Dimoi Archive.</b></p>
                <p>
                The 2025 collection from Dimoi Archive is not just clothing, but a declaration of personality and creativity. Inspired by the energy of the younger generation, each design carries a modern, unique vibe while maintaining the brand's distinct identity. With premium materials, innovative designs, and the trendiest colors of the year, Dimoi Archive 2025 is the launchpad to help you become the coolest version of yourself! <3
                </p>
            </div>
            <div class="forbutton">
                <a href="products.php" class="buttonn"> Want to see more?  &rarr;</a>
            </div>
            
        </div>
    </div>
    <div class="banner">
        <div class="slider" style="--quantity:10">
            <div class="item" style="--position:1">
                <img src="assets/images/img1.jpg" alt="">
            </div>
            <div class="item" style="--position:2">
                <img src="assets/images/img5.jpg" alt="">
            </div>
            <div class="item" style="--position:3">
                <img src="assets/images/img6.jpg" alt="">
            </div>
            <div class="item" style="--position:4">
                <img src="assets/images/img8.jpg" alt="">
            </div>
            <div class="item" style="--position:5">
                <img src="assets/images/img5.jpg" alt="">
            </div>
            <div class="item" style="--position:6">
                <img src="assets/images/img6.jpg" alt="">
            </div>
            <div class="item" style="--position:7">
                <img src="assets/images/img1.jpg" alt="">
            </div>
            <div class="item" style="--position:8">
                <img src="assets/images/img9.jpg" alt="">
            </div>
            <div class="item" style="--position:9">
                <img src="assets/images/img11.jpg" alt="">
            </div>
            <div class="item" style="--position:10">
                <img src="assets/images/SnapInsta.gg_463196057_950213193608007_6213987161618416418_n.jpg" alt="">
            </div>
        </div>
        <div class="content">
            <h1 data-content=" Dimoi Archive.">
               Dimoi Archive.
            </h1>
            <div class="author">
                <h2>Winter Arc.</h2>
                <p><b>Made By Love</b></p>
                <p>
                    Lai se , sụp rai <3
                </p>
            </div>
            <div class="model"></div>
        </div>
    </div>


    <script>
        const menuLinks = document.querySelectorAll('.menu nav ul li a');
        menuLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                const newPage = link.getAttribute('href');
                window.location.href = newPage;
            });
        });
    </script>
    
</body>
</html>
<?php include 'footer.php'; // Nhúng footer.php ?>