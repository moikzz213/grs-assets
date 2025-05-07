@component('mail::message')
# GRANDIOSE ASSET SYSTEM
 
{!! $data['message'] !!}
@if ($data['link'])
For more details click <a href="{{$data['link']}}">here</a>
@endif
<br/><br/>
<div>This email is intended to you only. Please do not share.</div>
<br/>
 
<div>Regards,</div> 

<div>Grandiose - Asset System</div> 
<div>Ghassan Aboud Group</div>
<div>https://assets.grandiose.ae</div>
@endcomponent 