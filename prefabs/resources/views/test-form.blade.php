<x-layout-app>
    <div class="container-xl">
        <div class="page-header">
            <h1>Test Form package</h1>
        </div>

        <h3>Livewire</h3>
        <livewire:test-form />

        <div class="row">
            <div class="col-md-12">
            </div>

            <div class="col-md-6">
                <h3>Basic</h3>
                <x-form::form>
                    <x-form::input
                        group-class="mb-3"
                        type="text"
                        name="test"
                        id="xxxx"
                        label="Test label"
                        placeholder="This is placeholder"
                        help="Help text is here"
                    />
                </x-form::form>

                <x-form::form method="POST">
                    <x-form::input
                        group-class="mb-3"
                        type="text"
                        name="test"
                        label="test label"
                        placeholder="This is placeholder"
                    /></x-form::form>
                <x-form::form method="DELETE" action="action-url">
                    <x-form::input
                        group-class="mb-3"
                        type="text"
                        name="test"
                        placeholder="This is placeholder"
                    />
                    <x-form::quill
                        group-class="mb-3"
                        type="text"
                        name="testq"
                        value="This is init value from html"
                    />
                </x-form::form>

                <x-form::form method="DELETE" action="action-here">
                    @php
                        $options = [
                            1 => 'one',
                            2 => 'two',
                            3 => 'three',
                        ];
                    @endphp
                    <x-form::select
                        name="select"
                        group-class="mb-3"
                        label="Select"
                        value="2"
                        :options="$options"
                        placeholder="Select value..."
                    >xxxx</x-form::select>
                </x-form::form>
            </div>


        </div>

    </div>
</x-layout-app>
