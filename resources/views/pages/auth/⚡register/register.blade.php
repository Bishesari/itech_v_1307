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
            <flux:label badge="کد تأیید به این شماره پیامک می‌شود." class="text-xs">{{ __('شماره موبایل') }}</flux:label>
            <flux:input wire:model.blur="mobile_number" type="text"
                        input:class="text-center pt-6.5 pb-5.5 tracking-widest font-semibold text-lg!"
                        required dir="ltr" maxlength="11" />
            <flux:error name="mobile_number" class="-mt-2! text-xs"/>
        </flux:field>

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" color="teal"
                         class="w-full cursor-pointer"
                         data-test="register-user-button">
            <span wire:loading.remove wire:target="continueRegister">
                {{ __('ادامه ثبت نام') }}
            </span>
                <span wire:loading wire:target="continueRegister">
                {{ __('در حال ارسال کد...') }}
            </span>
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




































    <flux:modal
        name="otp-verify"
        class="md:w-96"
        :dismissible="false"
    >
        <form wire:submit="verifyOtp"
              x-data="{
              remaining: 0,
              timer: null,
              start(s) {
                  clearInterval(this.timer);
                  this.remaining = s;
                  this.timer = setInterval(() => {
                      if (--this.remaining <= 0) clearInterval(this.timer);
                  }, 1000);
              },
              get label() {
                  const m = String(Math.floor(this.remaining / 60)).padStart(2, '0');
                  const s = String(this.remaining % 60).padStart(2, '0');
                  return `${m}:${s}`;
              }
          }"
              x-on:otp-sent.window="start($event.detail.seconds)"
              x-on:otp-cooldown.window="start($event.detail.seconds)"
              x-init="start($wire.resendCooldown)"
              class="space-y-6"
        >
            <div>
                <flux:heading size="lg">{{ __('تأیید شماره موبایل') }}</flux:heading>
                <flux:text class="mt-2">
                    {{ __('کد ۵ رقمی ارسال‌شده به شماره') }}
                    <span class="font-semibold" dir="ltr">{{ $mobile_number }}</span>
                    {{ __('را وارد کنید.') }}
                </flux:text>
            </div>

            <flux:field>
                <flux:input
                    wire:model="otp_code"
                    type="text"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    maxlength="5"
                    dir="ltr"
                    input:class="text-center py-6 tracking-[0.6em] font-bold text-2xl!"
                    autofocus
                />
                <flux:error name="otp_code" class="-mt-2! text-xs"/>
            </flux:field>

            {{-- تایمر / ارسال مجدد --}}
            <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
                <template x-if="remaining > 0">
                <span>
                    {{ __('ارسال مجدد کد تا') }}
                    <span class="font-semibold tabular-nums" dir="ltr" x-text="label"></span>
                </span>
                </template>

                <template x-if="remaining <= 0">
                    <flux:link
                        href="#"
                        x-on:click.prevent="$wire.requestOtp()"
                        class="text-xs"
                    >
                        {{ __('ارسال مجدد کد') }}
                    </flux:link>
                </template>

                <flux:link href="#" x-on:click.prevent="$wire.editMobile()" class="text-xs">
                    {{ __('اصلاح شماره موبایل') }}
                </flux:link>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button type="submit" variant="primary" color="teal" class="cursor-pointer">
                    <span wire:loading.remove wire:target="verifyOtp">{{ __('تأیید و ادامه') }}</span>
                    <span wire:loading wire:target="verifyOtp">{{ __('در حال بررسی...') }}</span>
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>


