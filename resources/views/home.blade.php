{{--
  Template Name: Blog
--}}

@extends('layouts.app')

@section('content')
  @php
    $blogId = (int) get_option('page_for_posts');
    $blogContent = $blogId > 0 ? (string) get_post_field('post_content', $blogId) : '';
  @endphp
  @if (str_contains($blogContent, '<!-- wp:'))
    @include('partials.breadcrumbs')
    {!! apply_filters('the_content', $blogContent) !!}
  @else
    @include('partials.blog-index')
  @endif
@endsection
