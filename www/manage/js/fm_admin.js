
function fmAddStream()
{
    var name = $('#fm_streams .add_stream input').val();
    
    if( !name || name.length == 0 )
    {
        window.alert("Please name your new stream.");
        return;
    }
    
    
}
