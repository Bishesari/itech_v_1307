<div class="flex flex-col gap-6"
     x-data="{nationalityType: $wire.entangle('nationality_type'), get identityLabel() {
            return this.nationalityType == 1
                ? 'کد ملی'
                : 'شناسه اختصاصی';
        },
        get identityMaxLength() {
            return this.nationalityType == 1 ? 10 : 20;
        }
    }"
>
    <x-auth-header :title="__('ثبت نام')" :description="__('اطلاعات خواسته شده را کامل کنید.')"/>

    <x-auth-session-status class="text-center" :status="session('status')"/>
    <form wire:submit="continueRegister" class="flex flex-col gap-6" autocomplete="off">

        {{-- نام --}}
        <flux:field>
            <flux:label badge="فارسی" class="text-xs">{{ __('نام') }}</flux:label>
            <flux:input wire:model="first_name_fa" type="text"
                        input:class="text-center py-6 font-semibold text-base!" required autofocus />
            <flux:error name="first_name_fa" class="-mt-2! text-xs"/>
        </flux:field>

        {{-- نام خانوادگی --}}
        <flux:field>
            <flux:label badge="فارسی" class="text-xs">{{ __('نام خانوادگی') }}</flux:label>
            <flux:input wire:model="last_name_fa" type="text"
                        input:class="text-center py-6 font-semibold text-base!" required />
            <flux:error name="last_name_fa" class="-mt-2! text-xs"/>
        </flux:field>

        {{-- نوع تابعیت --}}
        <flux:radio.group x-model="nationalityType" variant="cards" class="max-sm:flex-col" >
            @foreach (\App\Enums\NationalityType::cases() as $type)
                <flux:radio value="{{ $type->value }}" label="{{ $type->label() }}" class="cursor-pointer"/>
            @endforeach
        </flux:radio.group>
        <flux:error name="nationality_type" class="-mt-5!"/>

        {{-- شناسه --}}
        <flux:field>
            <flux:label class="text-xs"><span x-text="identityLabel"></span></flux:label>
            <flux:input wire:model="identity_number" x-bind:maxlength="identityMaxLength" type="text"
                input:class="text-center pt-6.5 pb-5.5 tracking-widest font-semibold text-lg!"
                required dir="ltr"/>
            <flux:error name="identity_number" class="-mt-2! text-xs"/>
        </flux:field>

        {{-- موبایل --}}
        <flux:field>
            <flux:label class="text-xs">{{ __('شماره موبایل') }}</flux:label>
            <flux:input wire:model="mobile_number" type="text"
                input:class="text-center pt-6.5 pb-5.5 tracking-widest font-semibold text-lg!"
                required dir="ltr" maxlength="11" />
            <flux:error name="mobile_number" class="-mt-2! text-xs"/>
        </flux:field>








        {{-- Submit --}}
        <div class="flex items-center justify-end">
            <flux:button
                type="submit"
                variant="primary"
                color="teal"
                class="w-full cursor-pointer"
                data-test="register-user-button"
            >
                {{ __('ادامه ثبت نام') }}
            </flux:button>
        </div>

    </form>


    {{-- Login navigation --}}
    <div
        x-data="{ navigating: false }"
        x-on:livewire:navigate.window="navigating = true"
        x-on:livewire:navigated.window="navigating = false"
        x-on:livewire:navigate-error.window="navigating = false"
        class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400"
    >
        <span>{{ __('حساب کاربری داشته اید؟') }}</span>

        &nbsp;&nbsp;

        <flux:link
            :href="route('login')"
            wire:navigate
            x-bind:class="{ 'pointer-events-none opacity-60': navigating }"
            x-bind:aria-busy="navigating"
        >
            <span x-show="!navigating">
                {{ __('وارد شوید.') }}
            </span>

            <span x-show="navigating" x-cloak>
                {{ __('در حال انتقال...') }}
            </span>
        </flux:link>




    </div>

    <button
        wire:click="prepareSmsTest"
    >
        ارسال پیامک تستی
    </button>

    <div wire:loading wire:target="prepareSmsTest">
        در حال آماده‌سازی درخواست...
    </div>



    <flux:modal name="edit-profile" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Update profile</flux:heading>
                <flux:text class="mt-2">Make changes to your personal details.</flux:text>
            </div>
            <flux:input label="Name" placeholder="Your name" />
            <flux:input label="Date of birth" type="date" />
            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" variant="primary">Save changes</flux:button>
            </div>
        </div>
    </flux:modal>

    @script
    <script>
        $wire.on('send-normal-sms-test', async (event) => {
            try {
                const response = await fetch('{{ route('test.send-normal-sms') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        mobile: event.mobile,
                    }),
                });

                const data = await response.json();

                if (response.ok && data.ok) {
                    await $wire.smsTestSent(data.message ?? 'پیامک ارسال شد.');
                } else {
                    await $wire.smsTestFailed(data.message ?? 'ارسال پیامک ناموفق بود.');
                }
            } catch (error) {
                await $wire.smsTestFailed('خطا در ارسال درخواست پیامک.');
            }
        });
    </script>
    @endscript




</div>

