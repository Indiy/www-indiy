<?php
include "header.php";
?>

<style type="text/css">

#feedback {
    width:940px; 
    float:left; 
    background:url("../images/borderdesign.gif") repeat-x bottom; 
    padding-bottom:40px; 
    margin-bottom:88px;
}
#feedback h2 {
    width:940px;
    font-size:50px;
    float:left;
    background:url("../images/borderdesign.gif") repeat-x bottom left;
    padding:0 0 25px 0; 
    margin-bottom:0;
}
#feedback .content_inner { 
    width:940px;
    float:left;
    background: url("../images/bg_support.gif") repeat-y; 
    margin:33px 0 0 0;
}
#feedback .questions {
    width:670px;
    float:left;
}
#feedback .questions h3 {
    font-size:30px;
    color:#556270;
    text-transform:uppercase;
}
#feedback .questions ul {
    width:670px;
    float:left;
    padding:20px 0 0 0;
}
#feedback .questions ul li {
    width:670px;
    float:left; 
    padding-bottom:25px;
}
#feedback .questions ul li span.number { 
    width:27px; 
    float:left; 
    background:#000000; 
    color:#4ecdc4; 
    text-align:center;
    font-size:20px;
    font-weight:normal;
}
#feedback .questions ul li span.details { 
    font-size: 14px; 
    font-family: "Neuzeit Grotesk",Arial,Helvetica,sans-serif;
    color: #556270; 
    font-weight: bold;
    padding-top: 0px;
    padding-bottom: 20px;
    padding-right: 10px;
    padding-left: 10px;
}

#feedback .textarea {
    width: 550px;
    height: 50px;
    margin-left: 10px;
    margin-top: 10px;
    border: 1px solid #CCC;
    border-radius: 4px;
    padding: 5px;
}

#feedback .radio_group {
    padding-left: 50px;
}

#feedback .radio {
    padding-top: 10px;
    padding-left: 20px;
}

#feedback .radio_text {
    font-size: 12px;
    font-family: sans-serif;
    padding-left: 10px;
    padding-top: 10px;
}

</style>

<section id="wrapper">
<section id="content">

<form id='feedback_form'>
<div id="feedback">
    <h2>User Feedback</h2>
    <div class="content_inner">
        <div class="questions">
            <h3>Our most frequently asked questions</h3>
             <ul>
                <li>
                    <span class="number">1</span>
                    <span class="details">
                        What do you think?
                    </span><br/>
                    <textarea class='textarea' name='what_think'></textarea>
                </li>
                <li>
                    <span class="number">2</span>
                    <span class="details">
                        Anything you would change?
                    </span><br/>
                    <textarea class='textarea' name='what_change'></textarea>
                </li>
                <li>
                    <span class="number">3</span>
                    <span class="details">
                        Who is the best band ever?
                    </span><br/>
                    <div class='radio_group'>
                        <input type='radio' class='radio' name='best_band' value='led zepplin'>
                        <span class='radio_text'>Led Zepplin</span>
                    </div>
                    <div class='radio_group'>
                        <input type='radio' class='radio' name='best_band' value='pink floyd'>
                        <span class='radio_text'>Pink Floyd</span>
                    </div>
                    <div class='radio_group'>
                        <input type='radio' class='radio' name='best_band' value='U2'>
                        <span class='radio_text'>U2</span>
                    </div>
                    <div class='radio_group'>
                        <input type='radio' class='radio' name='best_band' value='nirvana'>
                        <span class='radio_text'>Nirvana</span>
                    </div>
                </li>
             </ul>
        </div>
    </div>
</div><!-- faq -->
</form>	

</section><!-- content -->
</section><!-- wrapper -->

<?php
include "footer.php";
?>