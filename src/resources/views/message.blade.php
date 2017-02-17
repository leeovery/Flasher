@if (session()->has('flasher'))

    @foreach(session()->get('flasher') as $flasher)

        @php
            $alertType = config('flasher.class_names.' . $flasher['level']);
        @endphp

        <alert v-cloak
               header="{{ $flasher['title'] or null }}"
               type="{{ $alertType }}"
               {{--timeout="10" --}}{{--SECONDS--}}
               :timeout="null"
               :dismissable="true"
        >
            {!! $flasher['message'] !!}
        </alert>

    @endforeach

@endif