@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @php 
                        $client  = DB::table('oauth_clients')->where('user_id', Auth::user()->id)->first();
                    @endphp
                     @if ($client != null && $client->revoked == 0) 
                        @if (DB::table('oauth_access_tokens')->where('user_id', Auth::user()->id)->first() == null) 
                            <a href="gettoken" class="btn btn-primary">Get Token</a>
                        @else
                            <p>User access token: Bearer {{Auth::user()->access_token}}</p>
                            <p>Expiers: {{DB::table('oauth_access_tokens')->where('user_id', Auth::user()->id)
                              ->where('revoked', 0)
                              ->first()->expires_at}}</p>
                            <a href="refreshtoken" class="btn btn-success">Refresh Token</a>
                        @endif 
                     @else
                        <button class="btn btn-success" onclick="registerAsClient()">Register for API</button>

                        <script type="text/javascript">
                            function registerAsClient() {
                                var xmlhttp = new XMLHttpRequest();

                                xmlhttp.onreadystatechange = function() {
                                    if (xmlhttp.readyState == XMLHttpRequest.DONE) {
                                       if (xmlhttp.status != 400) {
                                           location.reload(true);
                                       }
                                       else {
                                          alert('There was an error 400');
                                       }
                                    }
                                };
                                var data = new FormData();
                                data.append('name', '{{ Auth::user()->email }}');
                                data.append('redirect', 'http://restapi.test/authtoken');
                                xmlhttp.open("POST", "/oauth/clients", true);
                                xmlhttp.setRequestHeader('X-CSRF-TOKEN', '{!! csrf_token() !!}');
                                xmlhttp.send(data);
                            }
                        </script> 
                     @endif
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
