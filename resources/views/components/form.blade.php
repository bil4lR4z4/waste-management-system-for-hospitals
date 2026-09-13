<form action="{{ $action }}" method="{{ strtolower($method) === 'get' ? 'GET' : 'POST' }}" enctype="multipart/form-data">
   
    @csrf

    @if(!in_array(strtoupper($method), ['GET','POST']))
        @method($method)
    @endif


    {{ $slot }}
</form>

