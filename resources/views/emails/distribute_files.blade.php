<x-mail::message>
# Introduction

The body of your message.

<x-mail::button :url="''">
Button Text
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>


{{--@component('mail::message')
    # File Distribution Request

    **Secretary:** {{ $secretaryName }}
    **Requested by:** {{ $requesterName }}
    **Project:** {{ $projectName }}

    **Files:**
    @foreach($fileNames as $name)
        - {{ $name }}
    @endforeach

    @component('mail::button', ['url' => $downloadLink])
        Download Files
    @endcomponent

    Thanks,
    {{ config('app.name') }}
@endcomponent
--}}
