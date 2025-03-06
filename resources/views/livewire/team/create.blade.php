<?php

use App\Models\User;
use Livewire\Volt\Component;

new class extends Component
{
    use \Livewire\WithFileUploads;

    public $name = 'New user name';
    public $email = 'Email address';
    public $department = 'Department user belongs to';
    public $title = 'User title';
    public $photo;
    public $status = 1;
    public $role = 'User role';

    public function save()
    {
        $this->photo->store(path: 'photos');
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'department' => 'required|string',
            'title' => 'required|string',
            'status' => 'required|boolean',
            'role' => 'required|string',
            'photo' => 'image|max:1024', // 1MB Max
        ]);

        if($this->photo)
        {
            $filename = $this->photo->store('photos', 's3-public');
        }

        User::create(['name' => $this->name,
        'email' => $this->email,
        'department' => $this->department,
        'title' => $this->title,
        'status' => $this->status,
        'role' => $this->role,
        'photo' => $filename,
        'password' => bcrypt(Str::random(16)),]);

        session()->flash('success', 'We Did It');
    }
}; ?>

<section class="w-full">
    @include('partials.team-heading')
    <div class="mt-6 bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
        <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Personal Information</h3>
                <p class="mt-1 pr-3 text-sm leading-5 text-gray-500">
                    The more the merrier! Create a new team member.
                </p>
            </div>
            <div class="mt-5 md:mt-0 md:col-span-2">
                <form wire:submit.prevent="submit">
                    <div class="grid grid-cols-6 gap-6">
                        <flux:field class="col-span-6 sm:col-span-3">
                            <flux:label>Name</flux:label>
                            <flux:input wire:model="name" type="text" />
                            <flux:error name="name" />
                        </flux:field>
{{--                        <x-text-input--}}
{{--                            wire:model="name"--}}
{{--                            label="Name"--}}
{{--                            :required="true"--}}
{{--                            placeholder="Jeffrey Way"--}}
{{--                            class="col-span-6 sm:col-span-3"/>--}}

                        <flux:field class="col-span-6 sm:col-span-3">
                            <flux:label>Email</flux:label>
                            <flux:input wire:model="email" type="text" />
                            <flux:error name="email" />
                        </flux:field>
{{--                        <x-text-input--}}
{{--                            wire:model="email"--}}
{{--                            type="email"--}}
{{--                            label="Email"--}}
{{--                            :required="true"--}}
{{--                            placeholder="jeffrey@laracasts.com"--}}
{{--                            class="col-span-6 sm:col-span-3"/>--}}

                        <div class="col-span-6 sm:col-span-2">
                            <label for="department" class="block text-sm font-medium leading-5 text-gray-700">Department</label>
                            <select wire:model="department"
                                    id="department"
                                    class="mt-1 block form-select w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                                <option value="human_resources">Human Resources</option>
                                <option value="marketing">Marketing</option>
                                <option value="information_technology">Information Technology</option>
                            </select>
                        </div>

                        <flux:field class="col-span-6 sm:col-span-3">
                            <flux:label>Title</flux:label>
                            <flux:input wire:model="title" type="text" />
                            <flux:error name="title" />
                        </flux:field>
{{--                        <x-text-input--}}
{{--                            wire:model="title"--}}
{{--                            label="Title"--}}
{{--                            :required="true"--}}
{{--                            placeholder="Instructor"--}}
{{--                            class="col-span-6 sm:col-span-3"/>--}}

                        <div class="col-span-6">
                            <label class="block text-sm leading-5 font-medium text-gray-700 mb-2">
                                Photo
                            </label>
                            <div class="flex flex-items-center">
                                <div class="flex-shrink-0 h-10 w-10 mr-4">
                                    @if($photo)
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full"
                                                 src="{{$photo->temporaryUrl()}}"
                                                 alt="">
                                        </div>
                                    @else
                                        <svg class="h-10 w-10 text-gray-300 rounded-full" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <input type="file" wire:model="photo">
                                    @error('photo')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                    <button wire:click="save">Save Photo</button>
                                </div>
                            </div>
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="status" class="block text-sm font-medium leading-5 text-gray-700">Status</label>
                            <select wire:model="status"
                                    id="status"
                                    class="mt-1 block form-select w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <div class="col-span-6 sm:col-span-2">
                            <label for="role" class="block text-sm font-medium leading-5 text-gray-700">Role</label>
                            <select wire:model="role"
                                    id="role"
                                    class="mt-1 block form-select w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                                <option value="admin">Admin</option>
                                <option value="manager">Manager</option>
                                <option value="member">Team Member</option>
                            </select>
                        </div>

                        @if(session()->has('success'))
                            {{session('success')}}
                        @endif
                    </div>

                    <div class="mt-8 border-t border-gray-200 pt-5">
                        <div class="flex justify-end">
                            <span class="inline-flex rounded-md shadow-sm">
                                <button type="submit"
                                        class="inline-flex justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out">
                                    Add Team Member
                                </button>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

