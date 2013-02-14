@extends('fancy::layouts.auto')

@section('body')
	@loop()
		<h1><?php the_title(); ?></h1>
		<div><?php the_content(); ?></div>
	@endloop
@stop