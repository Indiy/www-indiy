
$(document).ready(function()
    {
        playerSetup();
    });


function playerReady()
{
    var media = {
        mp3: "http://www.myartistdna.fm/artists/audio/53_46900_11-paparazzi.mp3",
        oga: "http://www.myartistdna.fm/artists/audio/53_46900_11-paparazzi.ogg"
    };

    $('#jquery_jplayer').jPlayer("setMedia",media);
    $('#jquery_jplayer').jPlayer("play"); 
}

function playerSetup()
{
    $('#jquery_jplayer').jPlayer( 
        {
            ready: function () { playerReady(); },
            solution: "html, flash",
            supplied: "oga, mp3",
            swfPath: "/js"
        });
}

