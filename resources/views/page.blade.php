{{--
  Template Name: Simple page
--}}

@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    @include('partials.breadcrumbs')
    @php(the_content())
  @endwhile
@endsection
