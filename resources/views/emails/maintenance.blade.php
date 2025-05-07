@component('mail::message')
# GRANDIOSE ASSET SYSTEM - MAINTENANCE

{!! $data['message'] !!}
<br/> 
For more details click <a href="{{$data['link']}}">here</a>  
<br/>
Regards,

<div>Grandiose - Asset System</div> 
<div>Ghassan Aboud Group</div>
<div>https://assets.grandiose.ae</div> 

<small>This is auto generated email, will stop only once the status is completed.</small>

@endcomponent