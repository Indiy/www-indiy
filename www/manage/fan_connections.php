<?php

    $artist_id = $_REQUEST['artist_id'];

?>

<script type="text/javascript">
    $(document).ready(setupQuestionTolltips);
</script>

<div id="popup">
    <div class='top_bar'>
    <h2>Add Label</h2>
    <button onclick='$.facebox.close();'>CLOSE</button>
    </div>

    <div class='top_blue_bar'></div>
    <div class='top_sep'></div>
    
    <div class='input_container' style='height: 55px;'>
        <div class='left_label'>Email Signups <span id='tip_email_signups' class='tooltip'>(?)</span></div>
        <div class='right_box'>
            <a class='submit' href="download_newsletter.php?artist_id=<?=$artist_id;?>">DOWNLOAD NOW</a>
        </div>
    </div>

    <div id='status'></div>
    <div class='bottom_sep'></div>
    <div class='bottom_blue_bar'></div>
</div>

