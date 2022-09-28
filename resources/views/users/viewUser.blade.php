@extends('layouts.app')
@section('content')
    <div class="card-deck mb-3">
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h4 class="my-0 font-weight-normal">{{@$page_title}}</h4>
            </div>
            <div class="card-body">
                <div class="col-md-10 offset-1">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="text-md-end">{{ __('Full Name') }}</label>
                                <input id="name" type="text" class="form-control" name="name" value="{{@$formData->name}}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="text-md-end">{{ __('Email Address') }}</label>
                                <input id="name" type="text" class="form-control" name="name" value="{{@$formData->email}}" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="text-md-end">{{ __('Phone Number') }}</label>
                                <input id="name" type="text" class="form-control" name="name" value="{{@$formData->phone}}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="photo" class="text-md-end">{{ __('Profile Picture') }}</label><br>
                                {!!@(!$formData->photo ? "----" : "<img class='profile-pic' src='".asset('storage/images/'.$formData->photo)."'>")!!}
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-md-12">
                                <a href="{{route('home')}}" type="submit" class="btn btn-secondary">{{ __('Go Back') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection