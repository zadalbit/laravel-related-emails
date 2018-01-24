@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
			@if (session('status'))
				<div class="alert alert-success">
					{{ session('status') }}
				</div>
			@endif
			@if (session('warning'))
				<div class="alert alert-warning">
					{{ session('warning') }}
				</div>
			@endif
            <h2>{{ $user->email }}</h2>
			<div class="form-group">
				<span>Related emails</span>
				<related-emails></related-emails>
			</div>
        </div>
    </div>
</div>
@endsection