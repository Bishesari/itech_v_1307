<?php
declare(strict_types=1);

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new
#[Layout('layouts::auth')]
#[Title('ثبت نام')]
class extends Component
{
    public string $first_name_fa = '';
    public string $last_name_fa = '';
    public string $nationality_type = '1';
    public string $identity_number = '';
    public string $mobile_number = '';


};
