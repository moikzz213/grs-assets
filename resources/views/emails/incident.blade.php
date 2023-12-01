@component('mail::message')
# GRANDIOSE ASSET SYSTEM - NEW INCIDENT

{!! $data['message']; !!}
<br/> 
Incident details click <a href="{{$data['link']}}">here</a>
<br/>

Regards,

<div>Grandiose - Asset System</div> 
<div>Ghassan Aboud Group</div>
<div style="font-size:11px;">IT Department</div>
<div>https://assets.grandiose.ae</div> 
@endcomponent