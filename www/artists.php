<?php
include "header.php";
?>

<section id="wrapper">
<section id="content">
	
	<div id="artists">
	<div class="heading">
    <h2>ALL ARTISTS</h2>
    
    <div class="sort">
        <ul class="splitter">
        <li><a class="sub" href="#">SORT PROJECTS BY CATEGORY</a>
        	<ul class="sublist">
            <li class="segment-0"><a href="#" data-value="all">All</a></li>
            <li class="segment-1"><a href="#" data-value="rock">Rock</a></li>
            <li class="segment-2"><a href="#" data-value="hiphop">Hip Hop</a></li>
            <li class="segment-2"><a href="#" data-value="pop">Pop</a></li>
            <li class="segment-2"><a href="#" data-value="dance">Dance</a></li>
            <li class="segment-1"><a href="#" data-value="country">Country</a></li>
            </ul>
        </li>
        </ul>
      </div><!-- dropdown -->
    </div>
	
    <div id="artistshome">
    	<ul id="list" class="image-grid">
        <li data-id="id-1" class="rock"><a href="#"><img src="images/artists_01.jpg" alt="">
        <span>Navegante</span></a></li>
        
        <li data-id="id-2" class="pop"><a href="#"><img src="images/artists_02.jpg" alt="">
        <span>Young Chris</span></a></li>
        
        <li data-id="id-3" class="hiphop"><a href="#"><img src="images/artists_03.jpg" alt="">
        <span>Hierosonic</span></a></li>
        
        <li data-id="id-4" class="dance"><a href="#"><img src="images/artists_04.jpg" alt="">
        <span>Kithe Brewster</span></a></li>
        
        <li data-id="id-5" class="country"><a href="#"><img src="images/artists_05.jpg" alt="">
        <span>Vanessa Cantave</span></a></li>
        
        <li data-id="id-6" class="pop"><a href="#"><img src="images/artists_06.jpg" alt="">
        <span>New Jersey State of Mind</span></a></li>
		
		<li data-id="id-7" class="hiphop"><a href="#"><img src="images/artists_13.jpg" alt="">
        <span>Florence & The Machine</span></a></li>
        
        <li data-id="id-8" class="pop"><a href="#"><img src="images/artists_14.jpg" alt="">
        <span>LCD Soundsystem</span></a></li>
        
        <li data-id="id-9" class="rock"><a href="#"><img src="images/artists_15.jpg" alt="">
        <span>Toro y Moi</span></a></li>
        
          <li data-id="id-1" class="rock"><a href="#"><img src="images/artists_01.jpg" alt="">
        <span>Navegante</span></a></li>
        
        <li data-id="id-2" class="pop"><a href="#"><img src="images/artists_02.jpg" alt="">
        <span>Young Chris</span></a></li>
        
        <li data-id="id-3" class="hiphop"><a href="#"><img src="images/artists_03.jpg" alt="">
        <span>Hierosonic</span></a></li>
        
        </ul>
       
	</div>	
    </div><!-- artists -->
    
    <div class="signup">
    <h3>Be heard, be seen, be independent.</h3>
    <div class="button"><a href="#" onclick="showSignup();">SIGN UP NOW</a></div>
    </div><!-- signup -->

</section><!-- content -->
</section><!-- wrapper -->

<?php
include "footer.php";
?>