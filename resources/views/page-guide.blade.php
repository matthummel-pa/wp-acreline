{{--
  Template Name: Guide
--}}

@extends('layouts.app')

@section('content')
@include('partials.breadcrumbs')
@php(the_content())
@endsection
