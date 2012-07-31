
function orderSavePassword()
{
    fanRegisterAccount(false,onOrderSaveEmailSuccess);
}

function onOrderSaveEmailSuccess(data)
{
    $('#save_password_form').hide();
    $('#save_password_success').show();
}

