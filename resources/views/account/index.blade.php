
<h2>Welcome{{Auth::user()->name}}</h2>
<br>
<form method="post" action="{{route('logout')}}">
    @csrf
    <button type="submit">Logout</button>
</form>
