@component('mail::message')
# ASSET SYSTEM - APPROVAL REQUESTED 
 
{!! $data['message']; !!}
@if ($data['link'])
Click <a href="{{$data['link']}}">here</a>
@endif
<br/><br/>
<div>This email is intended to you only. Please do not share.</div>
<br/>
 
<div>Regards,</div> 

<div>Asset System</div> 
<div>Ghassan Aboud Group</div>
<div>https://assets.grandiose.ae</div>
@endcomponent 