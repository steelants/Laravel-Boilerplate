<x-dynamic-component :component="$layout">
    <div class="container-xl">
        <div class="page-header">
            <h1 class="hide-mobile">{{ __('Backup') }}</h1>
            <div>
                <x-form::button class="btn-primary" :disabled="$running" onclick="Livewire.dispatch('openModal', {livewireComponents: 'backup.run', title: '{{ __('Backup') }}'})">
                    @if ($running)
                        <span class="spinner-border spinner-border-sm me-1" role="status"></span>{{ __('Backup is running...') }}
                    @else
                        <i class="fas fa-robot me-1"></i>{{ __('Start Backup') }}
                    @endif
                </x-form::button>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">{{ __('Name') }}</th>
                    <th scope="col">{{ __('Size') }}</th>
                    <th scope="col" class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($backups))
                    @foreach ($backups as $backup)
                        <tr>
                            <th scope="row">
                                @foreach ($backup['fileName'] as $key => $fileName)
                                    {{ ($key != 0 ? ", " : "") }}<a href="{{ route('system.backup.download', ['file_name' => $fileName]) }}">{{ $fileName }}</a>
                                @endforeach
                            </th>
                            <td>
                                {{ $backup['fileSize'] }}
                            </td>
                            <td class="text-end">
                            @php($backups_slug = explode('_', $backup['fileName'][0])[0])

                            @if(in_array($backups_slug . '_database.zip', $backup['fileName']) || in_array($backups_slug . '_storage.zip', $backup['fileName']))
                                <button type="button" class="btn btn-sm btn-warning" title="{{ __('Restore') }}"
                                        onclick="Livewire.dispatch('openModal', {livewireComponents: 'backup.restore', title: @js(__('Restore Backup') . ' — ' . $backups_slug), parameters: {backup_date: @js($backups_slug)}})">
                                    <i class="fas fa-undo"></i>
                                </button>
                            @endif

                                <x-boilerplate::form-button :action="route('system.backup.delete', ['backup_date' => $backups_slug])" :confirm="__('Are you sure?')" :title="__('Remove')" class="btn-sm" />
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</x-dynamic-component>
