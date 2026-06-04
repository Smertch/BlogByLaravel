<div>
    @if($errors->any())
        <strong>Whoops!</strong> There were some problems with your input.
        <ul>
            @foreach($errors->all() as $error)
                <li><strong style="color: red;">{{ $error }}</strong></li>
            @endforeach
        </ul>
    @endif
</div>

@if(session()->has('success'))
    <strong style="color: green;">{{session()->get('success')}}</strong>
@endif

@if(session()->has('status'))
    <div class="alert alert-success">
        {{session()->get('status')}}
    </div>
@endif
