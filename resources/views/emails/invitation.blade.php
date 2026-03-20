<x-mail::message>
    Вы приглашены в {{ config('app.name')  }}
    <x-mail::button :url="$acceptUrl">
        {{ __('Создать аккаунт')  }}
    </x-mail::button>
</x-mail::message>