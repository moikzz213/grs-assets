@component('mail::message')
# GRANDIOSE ASSET SYSTEM - Reset Password
<b>Hello!</b>
 
{!! $data['message'] !!} 

<div style="margin-top:40px; margin-bottom:40px; text-align:center;margin-left:auto; margin-right:auto;">
    <a href="{{$data['link']}}" target="_blank" rel="noopener noreferrer">
        <img src="https://assets.grandiose.ae/assets/images/reset.png">
    </a>
</div>

{!! $data['message2'] !!}

Regards,

<div>Grandiose - Asset System</div> 
<div>Ghassan Aboud Group</div>
<div>https://assets.grandiose.ae</div>
@endcomponent