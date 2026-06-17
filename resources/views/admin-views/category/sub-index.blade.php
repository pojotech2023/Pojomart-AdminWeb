@extends('layouts.admin.app')

@section('title', translate('Add new sub category'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset_path('assets/admin/img/category.png')}}" class="w--24" alt="{{ translate('category') }}">
                </span>
                <span>
                    {{translate('sub_category_setup')}}
                </span>
            </h1>
        </div>
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.category.store')}}" method="post" id="sub-category-add-form">
                            @csrf
                            @php($data = Helpers::get_business_settings('language'))
                            @php($defaultLanguage = Helpers::get_default_language())

                            @if($data && array_key_exists('code', $data[0]))

                                <ul class="nav nav-tabs mb-4 d-inline-flex">
                                    @foreach($data as $lang)
                                        <li class="nav-item">
                                            <a class="nav-link lang_link {{$lang['default'] == true? 'active':''}}" href="#" id="{{$lang['code']}}-link">{{\App\CentralLogics\Helpers::get_language_name($lang['code']).'('.strtoupper($lang['code']).')'}}</a>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="row">
                                @foreach($data as $lang)
                                    <div class="col-sm-6 {{$lang['default'] == false ? 'd-none':''}} lang_form" id="{{$lang['code']}}-form">
                                        <label class="form-label" for="exampleFormControlInput1">{{translate('sub_category')}} {{translate('name')}} ({{strtoupper($lang['code'])}})</label>
                                        <input type="text" name="name[]" class="form-control" maxlength="255" placeholder="{{ translate('New Sub Category') }}" {{$lang['status'] == true ? 'required':''}}
                                        @if($lang['status'] == true) oninvalid="document.getElementById('{{$lang['code']}}-link').click()" @endif>
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{$lang['code']}}">
                                @endforeach
                                @else
                                <div class="col-sm-6 lang_form" id="{{$defaultLanguage}}-form">
                                    <label class="form-label" for="exampleFormControlInput1">{{translate('sub_category')}} {{translate('name')}}({{strtoupper($defaultLanguage)}})</label>
                                    <input type="text" name="name[]" class="form-control" placeholder="{{ translate('New Sub Category') }}" required>
                                </div>
                                <input type="hidden" name="lang[]" value="{{$defaultLanguage}}">
                                @endif
                                <input name="position" value="1" hidden>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label"
                                            for="exampleFormControlSelect1">{{translate('main')}} {{translate('category')}}
                                            <span class="input-label-secondary">*</span></label>
                                        <select id="exampleFormControlSelect1" name="parent_id" class="form-control" required>
                                            @foreach(\App\Model\Category::where(['position'=>0])->get() as $category)
                                                <option value="{{$category['id']}}">{{$category['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="btn--container justify-content-end">
                                        <a href="" class="btn btn--reset min-w-120px">{{translate('reset')}}</a>
                                        <button type="submit"
                                                class="btn btn--primary"
                                                id="sub-category-submit-btn"
                                                data-sub-category-limit="{{ $subCategoryLimit }}"
                                                data-sub-category-count="{{ $subCategoryCount }}">
                                            {{translate('submit')}}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="card--header">
                            <h5 class="card-title">{{translate('Sub Category Table')}} <span class="badge badge-soft-secondary">{{ $categories->total() }}</span> </h5>
                            <form action="{{url()->current()}}" method="GET">
                                <div class="input-group">
                                    <input id="datatableSearch_" type="search" name="search"
                                            class="form-control pl-5"
                                           placeholder="{{translate('Search_by_Name')}}" aria-label="Search"
                                            value="{{$search}}" required autocomplete="off">
                                           <i class="tio-search tio-input-search"></i>
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
                                <th class="text-center">{{translate('#')}}</th>
                                <th>{{translate('main')}} {{translate('category')}}</th>
                                <th>{{translate('sub_category')}}</th>
                                <th>{{translate('status')}}</th>
                                <th class="text-center">{{translate('action')}}</th>
                            </tr>

                            </thead>

                            <tbody id="set-rows">
                            @foreach($categories as $key=>$category)
                                <tr>
                                    <td class="text-center">{{$categories->firstItem()+$key}}</td>
                                    <td>
                                        <span class="d-block font-size-sm text-body">
                                            {{$category->parent['name']}}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="d-block font-size-sm text-body">
                                            {{$category['name']}}
                                        </span>
                                    </td>

                                    <td>

                                        <label class="toggle-switch">
                                            <input type="checkbox" class="toggle-switch-input status-change-alert" id="stocksCheckbox{{ $category->id }}"
                                                   data-route="{{ route('admin.category.status', [$category->id, $category->status ? 0 : 1]) }}"
                                                   data-message="{{ $category->status? translate('you_want_to_disable_this_category'): translate('you_want_to_active_this_category') }}"
                                                {{ $category->status ? 'checked' : '' }}>
                                            <span class="toggle-switch-label text">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>

                                    </td>
                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="action-btn"
                                                href="{{route('admin.category.edit',[$category['id']])}}">
                                            <i class="tio-edit"></i></a>
                                            <a class="action-btn btn--danger btn-outline-danger form-alert" href="javascript:"
                                               data-id="category-{{$category['id']}}"
                                               data-message="{{ translate("Want to delete this") }}?">
                                                <i class="tio-delete-outlined"></i>
                                            </a>
                                        </div>
                                        <form action="{{route('admin.category.delete',[$category['id']])}}"
                                                method="post" id="category-{{$category['id']}}">
                                            @csrf @method('delete')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        @if(count($categories) == 0)
                        <div class="text-center p-4">
                            <img class="w-120px mb-3" src="{{asset_path('assets/admin/svg/illustrations/sorry.svg')}}" alt="{{ translate('image') }}">
                            <p class="mb-0">{{translate('No_data_to_show')}}</p>
                        </div>
                        @endif

                        <div class="page-area">
                            <table>
                                <tfoot>
                                {!! $categories->links() !!}
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="sub-category-limit-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content sub-category-limit-modal-content">
                <div class="modal-body">
                    <div class="sub-category-limit-modal-icon">!</div>
                    <div class="sub-category-limit-modal-text">
                        You have reached the limit to add more sub category.
                    </div>
                    <div class="sub-category-limit-modal-actions">
                        <button type="button" class="btn sub-category-limit-close-btn" data-dismiss="modal">Close</button>
                        <a href="{{ route('admin.pricing-plan') }}" class="btn sub-category-limit-plan-btn">View Pricing Plan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css_or_js')
    <style>
        .sub-category-limit-modal-content {
            border-radius: 12px;
            border: 0;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
        }

        #sub-category-limit-modal .modal-body {
            padding: 28px 28px 30px;
            text-align: center;
        }

        .sub-category-limit-modal-icon {
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

        .sub-category-limit-modal-text {
            color: #6b7280;
            font-size: 15px;
            text-align: center;
            margin: 0 0 24px;
        }

        .sub-category-limit-modal-actions {
            display: flex;
            justify-content: center;
            gap: 12px;
        }

        .sub-category-limit-close-btn,
        .sub-category-limit-plan-btn {
            min-width: 128px;
            border-radius: 6px;
            padding: 10px 18px;
            font-size: 14px;
            font-weight: 600;
        }

        .sub-category-limit-close-btn {
            color: #5f6368;
            background-color: #f1f3f4;
            border: 1px solid #f1f3f4;
        }

        .sub-category-limit-plan-btn {
            color: #fff;
            background-color: #69c7f1;
            border: 1px solid #69c7f1;
        }

        .sub-category-limit-plan-btn:hover {
            color: #fff;
            background-color: #52b7ec;
            border-color: #52b7ec;
        }
    </style>
@endpush

@push('script_2')

    <script>
        const subCategoryLimit = Number($('#sub-category-submit-btn').data('sub-category-limit')) || 8;
        const subCategoryCount = Number($('#sub-category-submit-btn').data('sub-category-count')) || 0;

        $(".lang_link").click(function(e){
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.split("-")[0];
            console.log(lang);
            $("#"+lang+"-form").removeClass('d-none');
            if(lang == '{{$defaultLanguage}}')
            {
                $(".from_part_2").removeClass('d-none');
            }
            else
            {
                $(".from_part_2").addClass('d-none');
            }
        });

        function showSubCategoryLimitPopup() {
            $('#sub-category-limit-modal').appendTo('body').modal('show');
        }

        $('#sub-category-submit-btn').on('click', function(e) {
            if (subCategoryCount >= subCategoryLimit) {
                e.preventDefault();
                e.stopImmediatePropagation();
                showSubCategoryLimitPopup();
            }
        });

        @if(session('sub_category_limit_reached'))
            showSubCategoryLimitPopup();
        @endif
    </script>
@endpush


