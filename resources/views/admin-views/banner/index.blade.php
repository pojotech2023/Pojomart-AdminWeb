@extends('layouts.admin.app')

@section('title', translate('Add new banner'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset_path('assets/admin/img/banner.png')}}" class="w--20" alt="{{ translate('banner') }}">
                </span>
                <span>
                    {{translate('banner setup')}}
                </span>
            </h1>
        </div>
        <div class="card mb-3">
            <div class="card-body">
                <form action="{{route('admin.banner.store')}}" method="post" enctype="multipart/form-data" id="banner-add-form">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('title')}}</label>
                                        <input type="text" name="title" value="{{old('title')}}" class="form-control" placeholder="{{ translate('New banner') }}" maxlength="255" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('item')}} {{translate('type')}}<span
                                                class="input-label-secondary">*</span></label>
                                        <select name="item_type" class="form-control show-item">
                                            <option value="product">{{translate('product')}}</option>
                                            <option value="category">{{translate('category')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group mb-0" id="type-product">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('product')}} <span
                                                class="input-label-secondary">*</span></label>
                                        <select name="product_id" class="form-control js-select2-custom">
                                            @foreach($products as $product)
                                                <option value="{{$product['id']}}">{{$product['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mb-0" id="type-category" style="display: none">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('category')}} <span
                                                class="input-label-secondary">*</span></label>
                                        <select name="category_id" class="form-control js-select2-custom">
                                            @foreach($categories as $category)
                                                <option value="{{$category['id']}}">{{$category['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column justify-content-center h-100">
                                <h5 class="text-center mb-3 text--title text-capitalize">
                                    {{translate('banner')}} {{translate('image')}}
                                    <small class="text-danger">* ( {{translate('ratio')}} 2:1 )</small>
                                </h5>
                                <label class="upload--vertical">
                                    <input type="file" name="image" id="customFileEg1" class="" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" hidden>
                                    <img id="viewer" src="{{asset_path('assets/admin/img/upload-vertical.png')}}" alt="{{ translate('banner image') }}"/>
                                </label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="btn--container justify-content-end">
                                <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                                <button type="submit"
                                        class="btn btn--primary"
                                        id="banner-submit-btn"
                                        data-banner-limit="{{ $bannerLimit }}"
                                        data-banner-count="{{ $bannerCount }}">{{translate('submit')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-0">
                <div class="card--header justify-content-between max--sm-grow">
                    <h5 class="card-title">{{translate('Banner List')}} <span class="badge badge-soft-secondary">{{ $banners->total() }}</span></h5>
                    <form action="{{url()->current()}}" method="GET">
                        <div class="input-group">
                            <input type="search" name="search" class="form-control"
                                   placeholder="{{translate('Search_by_ID_or_name')}}" aria-label="Search"
                                   value="{{$search}}" required autocomplete="off">
                            <div class="input-group-append">
                                <button type="submit" class="input-group-text">
                                    {{translate('search')}}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive datatable-custom">
                <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                    <tr>
                        <th class="border-0">{{translate('#')}}</th>
                        <th class="border-0">{{translate('banner image')}}</th>
                        <th class="border-0">{{translate('title')}}</th>
                        <th class="border-0">{{translate('banner type')}}</th>
                        <th class="text-center border-0">{{translate('status')}}</th>
                        <th class="text-center border-0">{{translate('action')}}</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($banners as $key=>$banner)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>
                                <div>
                                     <img class="img-vertical-150"
                                         src="{{ $banner->imageFullPath }}"
                                         alt="{{ translate('banner image') }}"
                                         onerror="this.src='{{ asset_path('assets/admin/img/900x400/img1.jpg') }}'">
                                </div>
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body text-trim-25">
                                    {{$banner['title']}}
                                </span>
                            </td>
                            <td>
                                @if($banner['product_id'])
                                    {{ translate('Product') }} : {{$banner->product?$banner->product->name:''}}
                                @elseif($banner['category_id'])
                                    {{ translate('Category') }} : {{$banner->category?$banner->category->name:''}}
                                @endif
                            </td>
                            <td>
                                <label class="toggle-switch my-0">
                                    <input type="checkbox"
                                           class="toggle-switch-input status-change-alert" id="stocksCheckbox{{ $banner->id }}"
                                           data-route="{{ route('admin.banner.status', [$banner->id, $banner->status ? 0 : 1]) }}"
                                           data-message="{{ $banner->status? translate('you_want_to_disable_this_banner'): translate('you_want_to_active_this_banner') }}"
                                        {{ $banner->status ? 'checked' : '' }}>
                                    <span class="toggle-switch-label mx-auto text">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="action-btn"
                                       href="{{route('admin.banner.edit',[$banner['id']])}}">
                                        <i class="tio-edit"></i></a>
                                    <a class="action-btn btn--danger btn-outline-danger form-alert" href="javascript:"
                                       data-id="banner-{{$banner['id']}}"
                                       data-message="{{ translate("Want to delete this") }}">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                </div>
                                <form action="{{route('admin.banner.delete',[$banner['id']])}}"
                                      method="post" id="banner-{{$banner['id']}}">
                                    @csrf @method('delete')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <table>
                    <tfoot>
                    {!! $banners->links() !!}
                    </tfoot>
                </table>

            </div>
            @if(count($banners) == 0)
                <div class="text-center p-4">
                    <img class="w-120px mb-3" src="{{asset_path('assets/admin/svg/illustrations/sorry.svg')}}" alt="{{ translate('image') }}">
                    <p class="mb-0">{{translate('No_data_to_show')}}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="banner-limit-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content banner-limit-modal-content">
                <div class="modal-body">
                    <div class="banner-limit-modal-icon">!</div>
                    <div class="banner-limit-modal-text">
                        You have reached the limit to add more banner.
                    </div>
                    <div class="banner-limit-modal-actions">
                        <button type="button" class="btn banner-limit-close-btn" data-dismiss="modal">Close</button>
                        <a href="{{ route('admin.pricing-plan') }}" class="btn banner-limit-plan-btn">View Pricing Plan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('css_or_js')
    <style>
        .banner-limit-modal-content {
            border-radius: 12px;
            border: 0;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
        }

        #banner-limit-modal .modal-body {
            padding: 28px 28px 30px;
            text-align: center;
        }

        .banner-limit-modal-icon {
            width: 74px;
            height: 74px;
            margin: 0 auto 22px;
            border: 3px solid #f6c48d;
            border-radius: 50%;
            color: #f6c48d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 42px;
            font-weight: 300;
            line-height: 1;
        }

        .banner-limit-modal-text {
            color: #6b7280;
            font-size: 15px;
            text-align: center;
            margin: 0 0 24px;
        }

        .banner-limit-modal-actions {
            display: flex;
            justify-content: center;
            gap: 12px;
        }

        .banner-limit-close-btn,
        .banner-limit-plan-btn {
            min-width: 128px;
            border-radius: 6px;
            padding: 10px 18px;
            font-size: 14px;
            font-weight: 600;
        }

        .banner-limit-close-btn {
            color: #5f6368;
            background-color: #f1f3f4;
            border: 1px solid #f1f3f4;
        }

        .banner-limit-plan-btn {
            color: #fff;
            background-color: #69c7f1;
            border: 1px solid #69c7f1;
        }

        .banner-limit-plan-btn:hover {
            color: #fff;
            background-color: #52b7ec;
            border-color: #52b7ec;
        }
    </style>
@endpush

@push('script_2')
    <script src="{{ asset_path('assets/admin/js/banner.js') }}"></script>
    <script>
        const bannerLimit = Number($('#banner-submit-btn').data('banner-limit')) || 3;
        const bannerCount = Number($('#banner-submit-btn').data('banner-count')) || 0;

        function showBannerLimitPopup() {
            $('#banner-limit-modal').appendTo('body').modal('show');
        }

        $('#banner-submit-btn').on('click', function(e) {
            if (bannerCount >= bannerLimit) {
                e.preventDefault();
                e.stopImmediatePropagation();
                showBannerLimitPopup();
            }
        });

        @if(session('banner_limit_reached'))
            showBannerLimitPopup();
        @endif
    </script>
@endpush


