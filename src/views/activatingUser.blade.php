@extends('layouts.default')

@section('seo_tags')
  <title>Активация пользователя</title>
@stop

@section('main')
    <h1>Активация пользователя</h1>
    <p style="padding: 20px 0;  text-align: center">
     {{$result}}
   </p>
     <!--section!-->

   @if(isset($status) && $status == "success")
       <script>
         setTimeout("location.href = '/'", 2000);
       </script>
   @endif
@stop






