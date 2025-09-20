<div>
    <div class="row g-2">
        @foreach ($files as $id => $file)
            <div class="col-auto">
                <div class="position-relative">
                    <div class="position-absolute top-0 end-0 m-2 btn btn-danger" wire:click="remove({{ $id }})" wire:confirm="Are you sure you want to delete this fiile?">
                        <i class="fas fa-trash "></i>
                    </div>
                    <a href="{{ $file }}" target="_blank">
                        <img class="object-fit-none border rounded" height="200" loading="lazy" src="{{ $file }}" width="200">
                    </a>
                </div>
            </div>
        @endforeach
        @if ($uploadEnabled)
            <div class="col-auto">
                <div class="bd-placeholder-img img-thumbnail d-flex justify-content-center flex-nowrap @error('files') border-danger @enderror" height="200" style="height: 200px; width: 200px;" width="200">
                    <div class="my-auto">
                        <label for="files">
                            <i class="fs-1 fas fa-upload @error('files') text-danger @enderror"></i>
                        </label>
                        <input id="files" multiple name="files" style="display:none;visibility:hidden" type="file" wire:model="files" accept="image/*" capture="environment">
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
