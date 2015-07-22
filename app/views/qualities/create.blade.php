@extends ('layouts.split')

@section('content_top')

		{{ HTML::linkRoute('quality.index', 'qualitys') }}

@stop

@section('content_left')

	<h2>Create quality</h2>	

	{{ Form::open(array('route' => array('quality.store', 0), 'method' => 'POST')) }}

		@include('qualities.form', array ('quality' => null))
	
	{{ Form::submit('Create') }}
	{{ Form::close() }}

@stop