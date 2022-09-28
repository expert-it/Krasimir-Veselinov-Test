@extends('layouts.app')
@section('content')
    <div class="card-deck mb-3">
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h4 class="my-0 font-weight-normal">
                    Manage Users
                    <a href="{{route('addUser')}}" title="Create User" class="pull-right btn btn-sm btn-secondary"><i class="fa fa-plus"></i> Create User</a>
                </h4>
            </div>
            <div class="card-body">
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
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Profile Pic</th>
                            <th>Register On</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{@$user->name}}</td>
                                <td>{{@$user->email}}</td>
                                <td>{{@$user->phone}}</td>
                                <td>{!!@(!$user->photo ? "----" : "<img class='profile-pic' src='".asset('storage/images/'.$user->photo)."'>")!!}</td>
                                <td>{{@date("d F, Y",strtotime($user->created_at))}}</td>
                                <td>
                                    <a href="{{route('viewUser',$user->id)}}" title="View User" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                                    <a href="{{route('editUser',$user->id)}}" title="Edit User" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>
                                    <a href="{{route('deleteUser',$user->id)}}" title="Delete User" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">No User's in the List</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
