@extends('layouts.app')
@php $breadcrumbs = [["link" => "/", "label" => "Home"]] @endphp
@section('title', 'Companies')
@section('sub_title', "All")
@section('content')

    @include('companies._table')

@endsection
