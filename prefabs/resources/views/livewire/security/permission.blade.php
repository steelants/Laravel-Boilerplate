<div>
    @if($usesRoles)
        {{-- Role-based mode --}}
        <h5 class="page-title mt-3">{{ __('Roles') }}</h5>

        <div class="mb-3">
            @forelse($allRoles as $role)
                <div class="form-check mb-2">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="role_{{ $role->id }}"
                        value="{{ $role->id }}"
                        wire:click="toggleRole({{ $role->id }})"
                        @checked(in_array($role->id, $assignedRoleIds))
                    />
                    <label class="form-check-label" for="role_{{ $role->id }}">
                        {{ $role->name }}
                        @if($role->scopes->isNotEmpty())
                            <small class="text-muted ms-1">({{ $role->scopes->pluck('ability')->join(', ') }})</small>
                        @endif
                    </label>
                </div>
            @empty
                <p class="text-muted">{{ __('No roles defined.') }}</p>
            @endforelse
        </div>

        @if(!empty($user->scope_applied))
            <p class="text-muted small mb-1">{{ __('Applied scopes') }}:</p>
            <div class="d-flex flex-wrap gap-1">
                @foreach($user->scope_applied as $scope)
                    <span class="badge bg-secondary">{{ $scope }}</span>
                @endforeach
            </div>
        @endif

    @else
        {{-- Direct scopes mode --}}
        <h5 class="page-title mt-3">{{ __('Scopes') }}</h5>

        <div class="d-flex flex-wrap gap-2 mb-3">
            @forelse($user->scope_applied ?? [] as $scope)
                <span class="badge bg-secondary d-flex align-items-center gap-1">
                    {{ $scope }}
                    <button
                        wire:click="removeScope('{{ $scope }}')"
                        type="button"
                        class="btn-close btn-close-white btn-sm ms-1"
                        aria-label="{{ __('Remove') }}"
                    ></button>
                </span>
            @empty
                <span class="text-muted small">{{ __('No scopes assigned.') }}</span>
            @endforelse
        </div>

        <div class="input-group">
            <input
                type="text"
                class="form-control"
                wire:model="newScope"
                wire:keydown.enter="addScope"
                placeholder="{{ __('e.g. edit-posts') }}"
            />
            <button wire:click="addScope" type="button" class="btn btn-primary">
                {{ __('Add') }}
            </button>
        </div>
    @endif
</div>
