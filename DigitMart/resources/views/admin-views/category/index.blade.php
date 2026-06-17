@extends('layouts.admin.app')

@section('title', translate('Add new category'))

@section('content')
    <div class="content container-fluid">

        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('assets/admin/img/category.png')}}" class="w--24" alt="{{ translate('category') }}">
                </span>
                <span>
                    {{translate('category_setup')}}
                </span>
            </h1>
        </div>

        <div class="row g-2">
            <div class="col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body pt-sm-0 pb-sm-4">
                        <form action="{{ route('admin.category.store') }}" method="post" enctype="multipart/form-data" id="category-add-form">
                            @csrf
                            @php
                                $data = Helpers::get_business_settings('language');
                                $defaultLanguage = Helpers::get_default_language();
                            @endphp

                            {{-- LANGUAGE TABS --}}
                            @if ($data && array_key_exists('code', $data[0]))
                                <ul class="nav nav-tabs d-inline-flex mb--n-30">
                                    @foreach ($data as $lang)
                                        <li class="nav-item">
                                            <a class="nav-link lang_link {{ $lang['default'] ? 'active' : '' }}"
                                            href="#"
                                            id="{{ $lang['code'] }}-link">
                                                {{ Helpers::get_language_name($lang['code']) }}
                                                ({{ strtoupper($lang['code']) }})
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            {{-- 🔴 ROW OPENED ONLY ONCE --}}
                            <div class="row align-items-end g-4">

                                {{-- CATEGORY NAME --}}
                                @if ($data && array_key_exists('code', $data[0]))
                                    @foreach ($data as $lang)
                                        <div class="col-sm-6 {{ !$lang['default'] ? 'd-none' : '' }} lang_form"
                                            id="{{ $lang['code'] }}-form">
                                            <label class="form-label">
                                                {{ translate('category') }} {{ translate('name') }}
                                                ({{ strtoupper($lang['code']) }})
                                            </label>

                                            <input type="text"
                                                name="name[]"
                                                class="form-control"
                                                placeholder="{{ translate('Ex: Size') }}"
                                                maxlength="255"
                                                {{ $lang['status'] ? 'required' : '' }}>
                                        </div>

                                        <input type="hidden" name="lang[]" value="{{ $lang['code'] }}">
                                    @endforeach
                                @else
                                    <div class="col-sm-6">
                                        <label class="form-label">
                                            {{ translate('category') }} {{ translate('name') }}
                                            ({{ strtoupper($defaultLanguage) }})
                                        </label>

                                        <input type="text"
                                            name="name[]"
                                            class="form-control"
                                            placeholder="{{ translate('New Category') }}"
                                            required>
                                    </div>

                                    <input type="hidden" name="lang[]" value="{{ $defaultLanguage }}">
                                @endif

                                <input type="hidden" name="position" value="0">

                                {{-- IMAGE --}}
                                <div class="col-sm-6">
                                    <div class="text-center mb-3">
                                        <img id="viewer"
                                            class="img--105"
                                            src="{{ asset('assets/admin/img/160x160/1.png') }}"
                                            alt="{{ translate('image') }}">
                                    </div>

                                    <label class="form-label text-capitalize">
                                        {{ translate('category image') }}
                                    </label>
                                    <small class="text-danger">* ({{ translate('ratio') }} 3:1)</small>

                                    <div class="custom-file">
                                        <input type="file"
                                            name="image"
                                            id="customFileEg1"
                                            class="custom-file-input"
                                            accept="image/*"
                                            required>
                                        <label class="custom-file-label" for="customFileEg1">
                                            {{ translate('choose') }} {{ translate('file') }}
                                        </label>
                                    </div>
                                </div>

                                {{-- BUTTONS --}}
                                <div class="col-12">
                                    <div class="btn--container justify-content-end">
                                        <button type="reset" class="btn btn--reset">{{ translate('reset') }}</button>
                                        <button type="submit"
                                                class="btn btn--primary"
                                                id="category-submit-btn"
                                                data-category-limit="{{ $categoryLimit }}"
                                                data-category-count="{{ $categoryCount }}"
                                                data-pricing-url="{{ route('admin.pricing-plan') }}">
                                            {{ translate('submit') }}
                                        </button>
                                    </div>
                                </div>

                            </div>
                            {{-- 🔴 ROW CLOSED PROPERLY --}}
                        </form>

                        </div>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                            <tr>
                                <th class="text-center">{{translate('#')}}</th>
                                <th>{{translate('category_image')}}</th>
                                <th>{{translate('name')}}</th>
                                <th>{{translate('status')}}</th>
                                <th>{{translate('priority')}}</th>
                                <th class="text-center">{{translate('action')}}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($categories as $key=>$category)
                                <tr>
                                    <td class="text-center">{{$categories->firstItem()+$key}}</td>
                                    <td>
                                        @php
                                            $image = $category->image;
                                            // Try to decode JSON, fallback to string
                                            if (is_string($image)) {
                                                $decoded = json_decode($image, true);
                                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                    $image = $decoded;
                                                }
                                            }
                                            if (is_array($image) && !empty($image[0])) {
                                                $imagePath = asset('storage/' . ltrim($image[0], '/'));
                                            } elseif (is_string($image) && !empty($image)) {
                                                $imagePath = asset('storage/category/' . basename($image));
                                            } else {
                                                $imagePath = asset('assets/admin/img/160x160/1.png');
                                            }
                                        @endphp
                                        <img src="{{ $imagePath }}" class="img--50 ml-3" alt="{{ translate('category') }}" onerror="this.src='{{ asset('assets/admin/img/160x160/1.png') }}'">
                                    </td>

                                    <td>
                                    <span class="d-block font-size-sm text-body text-trim-50">
                                        {{$category['name']}}
                                    </span>
                                    </td>
                                    <td>

                                        <label class="toggle-switch">
                                            <input type="checkbox"
                                                class="toggle-switch-input status-change-alert" id="stocksCheckbox{{ $category->id }}"
                                                   data-route="{{ route('admin.category.status', [$category->id, $category->status ? 0 : 1]) }}"
                                                   data-message="{{ $category->status? translate('you_want_to_disable_this_category'): translate('you_want_to_active_this_category') }}"
                                                {{ $category->status ? 'checked' : '' }}>
                                            <span class="toggle-switch-label text">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>

                                    </td>
                                    <td>
                                        <div class="max-85">
                                            <select name="priority" class="custom-select"
                                                    onchange="location.href='{{ route('admin.category.priority', ['id' => $category['id'], 'priority' => '']) }}' + this.value">
                                                @for($i = 1; $i <= 10; $i++)
                                                    <option value="{{ $i }}" {{ $category->priority == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
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
                            <img class="w-120px mb-3" src="{{asset('/assets/admin/svg/illustrations/sorry.svg')}}" alt="{{ translate('image') }}">
                            <p class="mb-0">{{translate('No_data_to_show')}}</p>
                        </div>
                        @endif

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

    <div class="modal fade" id="category-limit-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content category-limit-modal-content">
                <div class="modal-body">
                    <div class="category-limit-modal-icon">!</div>
                    <div class="category-limit-modal-text">
                        You have reached the limit to add more category.
                    </div>
                    <div class="category-limit-modal-actions">
                        <button type="button" class="btn category-limit-close-btn" data-dismiss="modal">Close</button>
                        <a href="{{ route('admin.pricing-plan') }}" class="btn category-limit-plan-btn">View Pricing Plan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('css_or_js')
    <style>
        .category-limit-modal-content {
            border-radius: 12px;
            border: 0;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
        }

        #category-limit-modal .modal-body {
            padding: 28px 28px 30px;
            text-align: center;
        }

        .category-limit-modal-icon {
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

        .category-limit-modal-text {
            color: #6b7280;
            font-size: 15px;
            text-align: center;
            margin: 0 0 24px;
        }

        .category-limit-modal-actions {
            display: flex;
            justify-content: center;
            gap: 12px;
        }

        .category-limit-close-btn,
        .category-limit-plan-btn {
            min-width: 128px;
            border-radius: 6px;
            padding: 10px 18px;
            font-size: 14px;
            font-weight: 600;
        }

        .category-limit-close-btn {
            color: #5f6368;
            background-color: #f1f3f4;
            border: 1px solid #f1f3f4;
        }

        .category-limit-plan-btn {
            color: #fff;
            background-color: #69c7f1;
            border: 1px solid #69c7f1;
        }

        .category-limit-plan-btn:hover {
            color: #fff;
            background-color: #52b7ec;
            border-color: #52b7ec;
        }
    </style>
@endpush

@push('script_2')
    <script src="{{ asset('assets/admin/js/category.js') }}"></script>
    <script>
        "use strict";

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

        const categoryLimit = Number($('#category-submit-btn').data('category-limit')) || 9;
        const categoryCount = Number($('#category-submit-btn').data('category-count')) || 0;

        function showCategoryLimitPopup() {
            $('#category-limit-modal').appendTo('body').modal('show');
        }

        $('#category-submit-btn').on('click', function(e) {
            if (categoryCount >= categoryLimit) {
                e.preventDefault();
                e.stopImmediatePropagation();
                showCategoryLimitPopup();
            }
        });

        @if(session('category_limit_reached'))
            showCategoryLimitPopup();
        @endif
    </script>
@endpush
