<?php
$thisPage .= "art";
//$title = $pageName .$thisPage;
include 'header.php';
?>

<section id="wrapper">
<section id="content">
	
	
	
	<div id="artists">
	<div class="heading">
    <h2>ART</h2>
    </div>
	
	<div id="slider">
    <div id="slide-scroll" class="scrollable">
        <div class="items">
        <div class="item">
    	<article>
        <figure><img src="images/photo_art_slide1.jpg" alt=""></figure>
        <aside>
        <p>Kithe Brewster - Stylist of the Stars</p>
        <div class="go"><a href="#">GO</a></div>
        </aside>
        </article>

    	<article class="right">
        <figure><img src="images/photo_art_slide2.jpg" alt=""></figure>
        <aside>
        <p>Vanessa Cantave - Executive Chef / Entrepeneur</p>
        <div class="go"><a href="#">GO</a></div>
        </aside>
        </article>
        </div>
      
        </div>
    </div>
    
       </div>
    	
    <div class="signup">
    <h3>Be heard, be seen, Get started now!</h3>
    <div class="button"><a href="#" onclick="showSignup();">SIGN UP NOW</a></div>
    </div><!-- signup -->

    <div id="artistshome">
	<h2>Who uses MY<span>ARTIST</span>DNA</h2>
    	<ul>
        <li data-id="id-7" class="hiphop"><a href="#"><img src="images/marc.jpg" alt="">
        <span>Marc Ecko</span></a></li>
        
        <li data-id="id-8" class="pop"><a href="#"><img src="images/basil.jpg" alt="">
        <span>Basil</span></a></li>
        
        <li data-id="id-9" class="rock"><a href="#"><img src="images/onionflats.jpg" alt="">
        <span>Onion Flats</span></a></li>        
        </ul>
	</div>	
    </div><!-- artists -->
    

</section><!-- content -->
</section><!-- wrapper -->


<?php
include 'footer.php';
?>