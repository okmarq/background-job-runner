<x-mail::message>
    Hello {{ $assignment->user->firstname }}
    Your Assignment submission has been received

    Keep working on your problem solving skills.

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
