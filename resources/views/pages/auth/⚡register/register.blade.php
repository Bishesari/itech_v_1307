<div class="flex flex-col gap-6">
    <x-auth-header :title="__('ثبت نام')" :description="__('اطلاعات خواسته شده را کامل کنید.')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="" class="flex flex-col gap-6" autocomplete="off">


        <flux:field>
            <flux:label badge="فارسی" class="font-light! text-xs">{{__('نام')}}</flux:label>
            <flux:input type="text" input:class="text-center py-6 font-semibold text-base!" required autofocus/>
            <flux:error name="f_name_fa" />
        </flux:field>

        <flux:field>
            <flux:label badge="فارسی" class="font-light! text-xs">{{__('نام خانوادگی')}}</flux:label>
            <flux:input type="text" input:class="text-center py-6 font-semibold text-base!" required />
            <flux:error name="l_name_fa" />
        </flux:field>

        <flux:radio.group wire:model="shipping" variant="cards" class="max-sm:flex-col">
            <flux:radio value="standard" label="ایرانی" checked class="cursor-pointer"/>
            <flux:radio value="fast" label="اتباع خارجی" class="cursor-pointer"/>
        </flux:radio.group>

        <flux:field>
            <flux:label class="font-light! text-xs">{{__('کد ملی')}}</flux:label>
            <flux:input type="text" input:class="text-center py-6 tracking-widest font-semibold text-base!" required dir="ltr" maxlength="10"/>
            <flux:error name="email" />
        </flux:field>

        <flux:field>
            <flux:label class="font-light! text-xs">{{__('شماره موبایل')}}</flux:label>
            <flux:input type="text" input:class="text-center py-6 tracking-widest font-semibold text-base!" required dir="ltr" maxlength="11"/>
            <flux:error name="email" />
        </flux:field>


        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" color="teal" class="w-full" data-test="register-user-button">
                {{ __('ادامه ثبت نام') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        <span>{{ __('حساب کاربری داشته اید؟') }}</span>
        <flux:link :href="route('login')" wire:navigate>{{ __('وارد شوید.') }}</flux:link>
    </div>
</div>
