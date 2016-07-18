@if (Session::has('message'))
    <div class="callout {{ Session::get('alert-class', 'alert') }}" data-closable>
        @if (Session::has('title'))
            <h4>{!! Session::get('title') !!}</h4>
        @endif
        <p>{!! Session::get('message') !!}</p>
        <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
