@extends('template.main')

@section('title', $title)

@section('content_title',"User Profile")
@section('content_description',"Personalize Your Account")
@section('breadcrumbs')

<ol class="breadcrumb">
<li><a href="{{route('dash')}}"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
    <li class="active">Here</li>
</ol>
@endsection

@section('main_content')
<?php $user = Auth::user();
$name = $user->name;
$user_type =$user->user_type;
$image_path =$user->img_path;
$outlet = 'Shree Pradhan Hospital'?>

<section class="content">

    <div class="row">
<h3>hiiiiiiiiiiiiiiiiiiiiiiiii</h3>

    </div>
    <!-- /.row -->

</section>

@endsection
