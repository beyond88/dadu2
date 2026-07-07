@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">
                            {{ __('custom.company') }}
                        </a></li>
                    <li class="breadcrumb-item active">
                        {{ __('custom.edit_company') }}
                    </li>
                </ol>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4 class="header-title">{{ __('custom.edit_company') }}</h4>

            <form action="{{ route('admin.companies.update', $company->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="form-group col-sm-6">
                        <label for="name">Company Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $company->name) }}" required>
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-group col-sm-6">
                        <label for="phone">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $company->phone) }}">
                        @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-group col-sm-6">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $company->email) }}">
                        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-group col-sm-6">
                        <label for="status">Status <span class="text-danger">*</span></label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" value="active" id="status_active" {{ old('status', $company->status) === 'active' ? 'checked' : '' }}>
                            <label class="form-check-label" for="status_active">Active</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" value="inactive" id="status_inactive" {{ old('status', $company->status) === 'inactive' ? 'checked' : '' }}>
                            <label class="form-check-label" for="status_inactive">Inactive</label>
                        </div>
                        @error('status') <small class="text-danger d-block">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-group col-sm-6">
                        <label for="address">Address</label>
                        <textarea name="address" class="form-control">{{ old('address', $company->address) }}</textarea>
                        @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-group col-sm-6">
                        <label for="description">Description</label>
                        <textarea name="description" class="form-control">{{ old('description', $company->description) }}</textarea>
                        @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Update
                    </button>
                    <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary">
                        <i class="fa fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
