@extends('layouts.app')
@section('content')
    <div class="card-deck mb-3">
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h4 class="my-0 font-weight-normal">{{@$page_title}}</h4>
            </div>
            <div class="card-body">
                <div class="col-md-10 offset-1">
                    @if (Session::has('message'))
                        <div class="alert bg-{{explode("|",Session::get('message'))[0]}} text-white alert-dismissible fade show" role="alert">{!! explode("|",Session::get('message'))[1] !!}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert text-white alert-dismissible fade show" role="alert">
                            <ul>{!!implode('', $errors->all('<li class="text-danger">:message</li>'))!!}</ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                        </div>
                    @endif
                    <form method="POST" action="{{@($formData->id ? route('editUser',$formData->id) : route('addUser'))}}" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="text-md-end">{{ __('Full Name') }}</label>
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" 
                                        value="{{@($formData->name ? $formData->name : old('name'))}}" required autocomplete="new-name">
                                @error('name')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="text-md-end">{{ __('Email Address') }}</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" 
                                        value="{{@($formData->email ? $formData->email : old('email'))}}" required autocomplete="new-email">
                                @error('email')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="text-md-end">{{ __('Phone Number') }}</label>
                                <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" 
                                        value="{{@($formData->phone ? $formData->phone : old('phone'))}}" required autocomplete="new-phone">
                                @error('phone')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="photo" class="text-md-end">{{ __('Profile Picture') }}</label>
                                <input id="photo" type="file" class="form-control @error('photo') is-invalid @enderror" {{@($formData->id ? '' : 'required')}} name="photo">
                                @error('photol')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                                {!!@(!$formData->photo ? "----" : "<br><img class='profile-pic' src='".asset('storage/images/'.$formData->photo)."'>")!!}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="text-md-end">{{ __('Password') }}</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" value="{{old('password')}}" {{@($formData->id ? '' : 'required')}} autocomplete="new-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="confirm_password" class="text-md-end">{{ __('Confirm Password') }}</label>
                                <input id="confirm_password" type="password" class="form-control @error('confirm_password') is-invalid @enderror" name="confirm_password" value="{{old('confirm_password')}}" {{@($formData->id ? '' : 'required')}} autocomplete="new-confirm_password">
                                @error('confirm_password')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">{{@($formData->id ? __('Update') : __('Create'))}}</button>
                                <a href="{{route('home')}}" type="submit" class="btn btn-secondary">{{ __('Go Back') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
