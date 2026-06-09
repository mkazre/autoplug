<x-guest-layout>
    <style>[x-cloak]{display:none!important}</style>

    <form method="POST" action="{{ route('register') }}"
          x-data="{ accountType: '{{ old('account_type', 'car_owner') }}' }">
        @csrf

        <!-- Account Type -->
        <div>
            <x-input-label :value="__('I am registering as a')" />
            <div class="mt-2 grid grid-cols-2 gap-3">
                <label class="flex items-center p-3 border rounded-md cursor-pointer"
                       :class="accountType === 'car_owner' ? 'border-indigo-500 ring-1 ring-indigo-500' : 'border-gray-300'">
                    <input type="radio" name="account_type" value="car_owner" x-model="accountType" class="text-indigo-600">
                    <span class="ms-2 text-sm text-gray-700">{{ __('Car Owner') }}</span>
                </label>
                <label class="flex items-center p-3 border rounded-md cursor-pointer"
                       :class="accountType === 'garage_owner' ? 'border-indigo-500 ring-1 ring-indigo-500' : 'border-gray-300'">
                    <input type="radio" name="account_type" value="garage_owner" x-model="accountType" class="text-indigo-600">
                    <span class="ms-2 text-sm text-gray-700">{{ __('Garage Owner') }}</span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('account_type')" class="mt-2" />
        </div>

        <!-- Name -->
        <div class="mt-4">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Garage / Business Name (garage owners only) -->
        <div class="mt-4" x-show="accountType === 'garage_owner'" x-cloak>
            <x-input-label for="garage_name" :value="__('Garage / Business Name')" />
            <x-text-input id="garage_name" class="block mt-1 w-full" type="text" name="garage_name"
                          :value="old('garage_name')" autocomplete="organization"
                          x-bind:required="accountType === 'garage_owner'" />
            <x-input-error :messages="$errors->get('garage_name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username"/>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Phone -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('Phone (optional)')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" autocomplete="tel" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
