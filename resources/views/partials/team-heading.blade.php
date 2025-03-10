<div class="relative mb-6 w-full">
    <flux:heading size="xl" level="1">Team</flux:heading>
    <flux:subheading size="lg" class="mb-6">
        {{ __('Manage your team') }}
    </flux:subheading>

    <flux:separator variant="subtle" />

    @if (session('success'))
        <div class="alert alert-success mt-2 text-green-500 dark:text-green-400 text-xs">
            {{ session('success') }}
        </div>
    @endif
</div>
