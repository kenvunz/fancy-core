@extends('fancy::layouts.auto')

@section('body')
<?php if (have_posts()): ?>
    <?php while(have_posts()): the_post(); ?>
    <h1><?php the_title(); ?></h1>
    <div><?php the_content(); ?></div>
    <?php endwhile; ?>
<?php endif ?>
@stop