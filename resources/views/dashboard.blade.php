@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
  <h1>Dashboard</h1>
@stop

@section('content')
  <p>Welcome to this beautiful admin panel.</p>
@stop

@section('css')
  <link rel="stylesheet" href="/css/admin_custom.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/solid.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/fontawesome.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
@stop

@section('js')
  <script>
    console.log('Hi!');
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/fontawesome.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@stop
