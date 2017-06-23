@extends('emails.layouts.default')

@section('message')
	Welcome! {{ $firstName }} {{ $lastName }}
@stop