<x-form::form wire:submit="save">
<div class="row">
    <div class="col-6">
        <x-form::input type="text" wire:model="test"/>
        <x-form::textarea wire:model="textarea"/>
        @php
        $options = [
        1 => 'one',
        2 => 'two',
        3 => 'three',
        ]
        @endphp
        <x-form::select wire:model.live="select" group-class="mb-3" label="Select" :options="$options" placeholder="Select value..."/>
        <x-form::button class="btn-primary" type="submit">submit</x-form::button>
        
        
        <x-form::quill wire:model.live="quill" label="quill" group-class="mb-3"/>
        
        <br>
    </div>
    <div class="col-6">
        Test = {{ $test }} <br>
        Textarea = {{ $textarea }} <br>
        Select = {{ $select }} <br>
        <hr>
        <h5>Quill render</h5>
        <hr>
        <div class="quill-render">
            {!! $quill !!}
        </div>
    </div>
</div>

Demo:
<hr>
<h1>Head 1</h1>
<h2>Head 2</h2>
<h3>Head 3</h3>
<h4>Head 4</h4>
<h5>Head 5</h5>
<h6>Head 6</h6>
<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Optio libero, eos quos porro numquam ipsum magnam voluptatibus eum repudiandae ratione?</p>
<hr>
</x-form::form>   
