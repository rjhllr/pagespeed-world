<x-mail::message>
# New Contact Form Submission

**From:** {{ $name }} ({{ $email }})

**Subject:** {{ $subject }}

---

{{ $body }}

---

<x-mail::button :url="'mailto:' . $email">
Reply to {{ $name }}
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
