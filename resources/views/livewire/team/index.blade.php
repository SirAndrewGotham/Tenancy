<?php

use App\Models\Tenant;
use App\Models\User;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {

    use WithPagination;

    public int $perPage = 5;
    public string $sortField = 'name';
    public bool $sortAsc = true;
    public string $search = '';
    public bool $super;
    public array $tenants;
    public $selectedTenant;

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }

    public function showUsers()
    {
        $query = User::search($this->search)
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc');
        if ($this->super && $this->selectedTenant) {
            $query->where('tenant_id', $this->selectedTenant);
        }

        return $query->paginate($this->perPage);
    }

    public function impersonate($userId)
    {
        if (!auth()->user()->super) {
            return;
        }

        auth()->user()->impersonate($userId);
        $originalId = auth()->user()->id;
        session()->put('impersonate', $originalId);
        auth()->loginUsingId($userId);

        return redirect('/team');
    }

    public function mount(): void
    {
        $this->super = false;
        if (auth()->user()->super) {
            $this->super = true;
            $this->tenants = Tenant::all()->pluck('name', 'id')->toArray();
        }
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function render(): mixed
    {
//        $query = User::search($this->search)
//            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc');
//        if ($this->super && $this->selectedTenant) {
//            $query->where('tenant_id', $this->selectedTenant);
//        }

        return view('livewire.team.index', [
//            'users' => $query->with('documents')->paginate($this->perPage),
//            'users' => $query->paginate($this->perPage),
            'users' => $this->showUsers(),
        ]);
    }

}; ?>

<section class="w-full">
    @include('partials.team-heading')

    {{-- Team section index heading --}}
    <div class="grid grid-cols-6 mb-4">
        <div class="col-span-6 sm:col-span-1 pr-2">
{{--            <flux:dropdown>--}}
{{--                <flux:button icon-trailing="chevron-down">{{ __('Per Page') }}</flux:button>--}}

{{--                <flux:menu>--}}
{{--                    <flux:menu.radio.group>--}}
{{--                        <flux:menu.radio checked>10</flux:menu.radio>--}}
{{--                        <flux:menu.radio>15</flux:menu.radio>--}}
{{--                        <flux:menu.radio>20</flux:menu.radio>--}}
{{--                    </flux:menu.radio.group>--}}
{{--                </flux:menu>--}}
{{--            </flux:dropdown>--}}

            <flux:select
                wire:model.live="perPage"
                placeholder="{{ __('Per page') }}"
            >
                <flux:select.option value="5">5</flux:select.option>
                <flux:select.option value="10">10</flux:select.option>
                <flux:select.option value="15">15</flux:select.option>
                <flux:select.option value="20">20</flux:select.option>
                <flux:select.option value="50">50</flux:select.option>
            </flux:select>



{{--            <flux:select variant="listbox">--}}
{{--                <x-slot name="button">--}}
{{--                    <flux:select.button class="rounded-full!" placeholder="{{ __('Per page') }}" :invalid="$errors->has('...')" />--}}
{{--                </x-slot>--}}

{{--                <flux:select.option>5</flux:select.option>--}}
{{--                <flux:select.option>10</flux:select.option>--}}
{{--                <flux:select.option>15</flux:select.option>--}}
{{--                <flux:select.option>20</flux:select.option>--}}
{{--                <flux:select.option>25</flux:select.option>--}}
{{--                <flux:select.option>50</flux:select.option>--}}
{{--            </flux:select>--}}
        </div>
        {{--        <div class="col-span-6 sm:col-span-1 pr-2">--}}
        {{--            <label for="location" class="block text-sm leading-5 font-medium text-gray-700">Per Page</label>--}}
        {{--            <select wire:model="perPage" id="location"--}}
        {{--                    class="mt-1 form-select block w-full pl-3 pr-10 py-2 text-base leading-6 border-gray-300 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 sm:text-sm sm:leading-5">--}}
        {{--                <option>10</option>--}}
        {{--                <option>15</option>--}}
        {{--                <option>20</option>--}}
        {{--            </select>--}}
        {{--        </div>--}}

        @if($super)
            <div class="col-span-6 sm:col-span-2 pr-2">
                <flux:dropdown>
                    <flux:button icon-trailing="chevron-down">{{ __('Choose a Tenant') }}</flux:button>

                    <flux:menu>
                        @foreach($tenants as $key => $tenant)
                            <flux:menu.item icon="user" kbd="⌘S">
                                {{ $tenant }}
                            </flux:menu.item>
                        @endforeach
                    </flux:menu>
                </flux:dropdown>
            </div>
            {{--            <flux:dropdown>--}}
            {{--                <flux:button icon-trailing="chevron-down">Choose a Tenant</flux:button>--}}

            {{--                <flux:menu>--}}
            {{--                    <flux:menu.radio.group>--}}
            {{--                        @foreach($tenants as $key => $tenant)--}}
            {{--                            <flux:menu.radio>{{ $tenant }}</flux:menu.radio>--}}
            {{--                        @endforeach--}}
            {{--                    </flux:menu.radio.group>--}}
            {{--                </flux:menu>--}}
            {{--            </flux:dropdown>--}}
            {{--            <div class="col-span-6 sm:col-span-2 pr-2">--}}
            {{--                <label for="tenant" class="block text-sm leading-5 font-medium text-gray-700">Tenant</label>--}}
            {{--                <select wire:model="selectedTenant" id="tenant"--}}
            {{--                        class="mt-1 form-select block w-full pl-3 pr-10 py-2 text-base leading-6 border-gray-300 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 sm:text-sm sm:leading-5">--}}
            {{--                    <option value="">Choose a Tenant</option>--}}
            {{--                    @foreach($tenants as $key => $tenant)--}}
            {{--                        <option value="{{$key}}">{{$tenant}}</option>--}}
            {{--                    @endforeach--}}
            {{--                </select>--}}
            {{--            </div>--}}
        @endif


        <div class="col-span-6 {{$super == true ? 'sm:col-span-3' : 'sm:col-span-5'}}">
            <flux:field>
                <flux:input wire:model.live="search" placeholder="{{ __('Search team members...') }}"/>
            </flux:field>
        </div>

        {{--        <div class="col-span-6 {{$super == true ? 'sm:col-span-3' : 'sm:col-span-5'}}">--}}
        {{--            <x-text-input wire:model="search" label="Search" placeholder="{{ __('Search team members...') }}"/>--}}
        {{--        </div>--}}
    </div>
    {{--// Team section index heading --}}

    <div class="py-4">
        <div class="flex flex-col">
            <div class="flex flex-col">
                <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                    <div
                        class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                        <table class="min-w-full">
                            <thead>
                            <tr>
                                <x-th label="Name" value="name" :canSort="true" :sortField="$sortField"
                                      :sortAsc="$sortAsc"/>
                                <x-th label="Title" value="title" :canSort="true" :sortField="$sortField"
                                      :sortAsc="$sortAsc"/>
                                <x-th label="Status" value="status" :canSort="false" :sortField="$sortField"
                                      :sortAsc="$sortAsc"/>
                                <x-th label="Role" value="role" :canSort="true" :sortField="$sortField"
                                      :sortAsc="$sortAsc"/>
                                <x-th label="Application" value="application" :canSort="false" :sortField="$sortField"
                                      :sortAsc="$sortAsc"/>
                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50">
                            <span class="flex rounded-md justify-end">
                                <a href="{{route('users.create')}}" type="button"
                                   class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs leading-4 font-medium rounded text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition ease-in-out duration-150">
                                    Add Team Member
                                </a>
                            </span>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white">
                            @foreach($users as $user)
                                <tr wire:key="{{$user->id}}">
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full"
                                                     src="{{$user->avatarUrl()}}"
                                                     alt="">
                                            </div>
                                            <div class="ml-4">
                                                <div>
                                                    <span class="text-sm leading-5 font-medium text-gray-900">
                                                        {{ $user->name }}
                                                    </span>
                                                    @if($super && $user->id != auth()->id())
                                                        <a wire:click="impersonate({{  $user->id }})" href="#"
                                                           class="text-xs text-indigo-600 ml-1">
                                                            Impersonate
                                                        </a>
                                                    @endif
                                                </div>
                                                <div class="text-sm leading-5 text-gray-500">
                                                    {{$user->email}}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <div class="text-sm leading-5 text-gray-900">{{$user->title}}</div>
                                        <div class="text-sm leading-5 text-gray-500">{{$user->department}}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        @if($user->status)
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                        @else
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Inactive
                                    </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500">
                                        {{$user->role}}
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500">
                                        <div class="flex justify-center">
                                            @if($application = $user->documents->where('type', 'application')->first())
                                                <a href="{{$application->privateUrl()}}"
                                                   target="_blank">{{--open new window svg--}}
                                                    <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z"></path>
                                                        <path
                                                            d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z"></path>
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">
                                        <a href="#" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div>
                {{ $users->links() }}
            </div>
        </div>

    </div>
</section>
