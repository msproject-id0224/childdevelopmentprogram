@component('mail::message')
# Profile Photo Status Update

Hello {{ \->user->name }},

Your profile photo request has been **{{ \ }}**.

@if(\ === 'approved')
Your new profile photo is now active.
@elseif(\ === 'rejected')
Reason: {{ \ }}
Please upload a new photo that follows our guidelines.
@elseif(\ === 're-upload requested')
Reason: {{ \ }}
Please upload a new photo as requested by the administrator.
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent
