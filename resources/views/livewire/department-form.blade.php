<?php

use Livewire\Volt\Component;

new class extends Component {

    public $name = 'Andy';
    public $success = false;

    public function submit()
    {
        \App\Models\Department::create([
            'name' => $this->name,
            'slug' => \Illuminate\Support\Str::slug($this->name),
        ]);
        $this->success = true;
    }

    public function mount($departmentId = null): void
    {
        if($departmentId)
        {
            $this->name = \App\Models\Department::findOrFail($departmentId)->name;
        }
    }
}; ?>

<div>
    <input wire:model="name" type="text" />
    <button wire:click="submit">Submit</button>
    @if($success)
        <div>Saved</div>
    @endif
</div>
