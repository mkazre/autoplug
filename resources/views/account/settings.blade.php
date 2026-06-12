<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Account settings') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if (session('status'))
                    <div class="mb-4 text-sm text-green-600">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('account.settings.update') }}">
                    @csrf
                    @method('PUT')

                    <h3 class="font-medium text-gray-900 mb-3">Notifications</h3>
                    <label class="flex items-center gap-2 mb-2">
                        <input type="hidden" name="notify_email" value="0">
                        <input type="checkbox" name="notify_email" value="1" class="rounded border-gray-300 text-violet-600" @checked($user->prefersEmail())>
                        <span class="text-sm text-gray-700">Email notifications</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="hidden" name="notify_sms" value="0">
                        <input type="checkbox" name="notify_sms" value="1" class="rounded border-gray-300 text-violet-600" @checked($user->prefersSms())>
                        <span class="text-sm text-gray-700">SMS notifications (requires a phone number on your profile)</span>
                    </label>

                    @if ($user->hasRole('car_owner'))
                        <h3 class="font-medium text-gray-900 mt-6 mb-3">Search</h3>
                        <label class="block text-sm text-gray-700">Default search radius (km)</label>
                        <input name="default_radius" type="number" min="1" max="200" value="{{ old('default_radius', $user->pref('default_radius')) }}" placeholder="Use platform default" class="mt-1 w-40 border-gray-300 rounded-md shadow-sm text-sm">
                    @endif

                    <div class="mt-6">
                        <x-primary-button>Save preferences</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
