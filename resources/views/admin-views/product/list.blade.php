@extends('layouts.admin.app')

@section('title', translate('Product List'))

@section('content')
    <div class="content container-fluid product-list-page">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset_path('assets/admin/img/products.png')}}" class="w--24" alt="">
                </span>
                <span>
                    {{ translate('product List') }}
                    <span class="badge badge-soft-secondary">{{ $products->total() }}</span>
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <!-- Card -->
                <div class="card">
                    <!-- Header -->
                    <div class="card-header border-0">
                        <div class="w-100">
                            <form action="{{ url()->current() }}" method="GET" id="product-filter-form">
                                <div class="row g-2 align-items-end mb-3">
                                    <div class="col-sm-6 col-lg-6">
                                        <label class="input-label">{{ translate('category') }}</label>
                                        <select name="category_id" id="category-filter"
                                                class="form-control js-select2-custom"
                                                onchange="handleCategoryChange(this.value)">
                                            <option value="">---{{ translate('select') }}---</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category['id'] }}" {{ (string)$categoryId === (string)$category['id'] ? 'selected' : '' }}>
                                                    {{ $category['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-6 col-lg-6">
                                        <label class="input-label">{{ translate('sub_category') }}</label>
                                        <select name="sub_category_id" id="sub-category-filter"
                                                class="form-control js-select2-custom"
                                                onchange="submitProductFilter()"
                                                data-id="{{ $subCategoryId }}">
                                            <option value="">---{{ translate('select') }}---</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-12 col-lg-8" style="min-width: 420px;">
                                        <label class="input-label">{{ translate('Search_by_ID_or_name') }}</label>
                                        <div class="input-group">
                                            <input id="datatableSearch_" type="search" name="search"
                                                class="form-control"
                                                placeholder="{{ translate('Search_by_ID_or_name') }}" aria-label="Search"
                                                value="{{ $search }}" autocomplete="off">
                                            <div class="input-group-append">
                                                <button type="submit" class="input-group-text">
                                                    {{ translate('search') }}
                                                </button>
                                            </div>
                                            @if($search || $categoryId || $subCategoryId)
                                                <div class="input-group-append">
                                                    <a href="{{ route('admin.product.list') }}" class="input-group-text bg-white text-body">
                                                        {{ translate('reset') }}
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card--header justify-content-end max--sm-grow">
                            <div class="hs-unfold mr-2">
                                <a class="js-hs-unfold-invoker btn btn-sm btn-outline-primary-2 dropdown-toggle min-height-40" href="javascript:;"
                                    data-hs-unfold-options='{
                                            "target": "#usersExportDropdown",
                                            "type": "css-animation"
                                        }'>
                                    <i class="tio-download-to mr-1"></i> {{ translate('export') }}
                                </a>

                                <div id="usersExportDropdown"
                                    class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                                    <span class="dropdown-header">{{ translate('download') }}
                                        {{ translate('options') }}</span>
                                    <a id="export-excel" class="dropdown-item" href="{{route('admin.product.bulk-export')}}">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ asset_path('assets/admin') }}/svg/components/excel.svg"
                                            alt="Image Description">
                                        {{ translate('excel') }}
                                    </a>
                                </div>
                            </div>
                            <div>
                                <a href="{{route('admin.product.limited-stock')}}" class="btn btn--primary-2 min-height-40">{{translate('limited stocks')}}</a>
                            </div>
                            <div>
                                <a href="{{route('admin.product.add-new')}}"
                                    class="btn btn-primary min-height-40 py-2"
                                    id="product-add-btn"
                                    data-product-limit="{{ $productLimit }}"
                                    data-product-count="{{ $productCount }}"><i
                                        class="tio-add"></i>
                                    {{translate('add new product')}}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive datatable-custom">
                        <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                            <tr>
                                <th>{{translate('#')}}</th>
                                <th>{{translate('product_name')}}</th>
                                <th>{{translate('selling_price')}}</th>
                                <th class="text-center">{{translate('total_sale')}}</th>
                                <th class="text-center">{{translate('show_in_daily_needs')}}</th>
                                <th class="text-center">{{translate('featured')}}</th>
                                <th class="text-center">{{translate('status')}}</th>
                                <th class="text-center">{{translate('action')}}</th>
                            </tr>
                            </thead>

                            <tbody id="set-rows">
                            @foreach($products as $key=>$product)
                                <tr>
                                    <td class="pt-1 pb-3  {{$key == 0 ? 'pt-4' : '' }}">{{$products->firstItem()+$key}}</td>
                                    <td class="pt-1 pb-3  {{$key == 0 ? 'pt-4' : '' }}">
                                        <a href="{{route('admin.product.view',[$product['id']])}}" class="product-list-media">
                                            <img src="{{ $product->identityImageFullPath[0] ?? asset_path('assets/admin/img/400x400/img2.jpg') }}"
                                                class="img-fit"
                                                alt="{{ $product->name }}"
                                                onerror="this.src='{{ asset_path('assets/admin/img/400x400/img2.jpg') }}'">
                                        
                                        <h6 class="name line--limit-2">
                                            {{\Illuminate\Support\Str::limit($product['name'], 20, $end='...')}}
                                        </h6>
                                        </a>
                                    </td>
                                    <td class="pt-1 pb-3  {{$key == 0 ? 'pt-4' : '' }}">
                                        <div class="max-85 text-right">
                                            {{ Helpers::set_symbol($product['price']) }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        {{ $product->total_sold }}
                                    </td>
                                    <td class="pt-1 pb-3  {{$key == 0 ? 'pt-4' : '' }}">
                                        <div class="text-center">
                                            <label class="switch my-0">
                                                <input type="checkbox" class="status" onchange="daily_needs('{{$product['id']}}','{{$product->daily_needs==1?0:1}}')"
                                                    id="{{$product['id']}}" {{$product->daily_needs == 1?'checked':''}}>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="pt-1 pb-3  {{$key == 0 ? 'pt-4' : '' }}">
                                        <label class="toggle-switch my-0">
                                            <input type="checkbox"
                                                   onclick="featured_status_change_alert('{{ route('admin.product.feature', [$product->id, $product->is_featured ? 0 : 1]) }}', '{{ $product->is_featured? translate('want to remove from featured product'): translate('want to add in featured product') }}', event)"
                                                   class="toggle-switch-input" id="stocksCheckbox{{ $product->id }}"
                                                {{ $product->is_featured ? 'checked' : '' }}>
                                            <span class="toggle-switch-label mx-auto text">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </td>
                                    <td class="pt-1 pb-3  {{$key == 0 ? 'pt-4' : '' }}">
                                        <label class="toggle-switch my-0">
                                            <input type="checkbox"
                                                onclick="status_change_alert('{{ route('admin.product.status', [$product->id, $product->status ? 0 : 1]) }}', '{{ $product->status? translate('you want to disable this product'): translate('you want to active this product') }}', event)"
                                                class="toggle-switch-input" id="stocksCheckbox{{ $product->id }}"
                                                {{ $product->status ? 'checked' : '' }}>
                                            <span class="toggle-switch-label mx-auto text">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </td>
                                    <td class="pt-1 pb-3  {{$key == 0 ? 'pt-4' : '' }}">
                                        <!-- Dropdown -->
                                        <div class="btn--container justify-content-center">
                                            <a class="action-btn"
                                                href="{{route('admin.product.edit',[$product['id']])}}">
                                            <i class="tio-edit"></i></a>
                                            <a class="action-btn btn--danger btn-outline-danger" href="javascript:"
                                                onclick="form_alert('product-{{$product['id']}}','{{ translate("Want to delete this") }}')">
                                                <i class="tio-delete-outlined"></i>
                                            </a>
                                        </div>
                                        <form action="{{route('admin.product.delete',[$product['id']])}}"
                                                method="post" id="product-{{$product['id']}}">
                                            @csrf @method('delete')
                                        </form>
                                        <!-- End Dropdown -->
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        <div class="page-area">
                            <table>
                                <tfoot class="border-top">
                                {!! $products->links() !!}
                                </tfoot>
                            </table>
                        </div>
                        @if(count($products)==0)
                            <div class="text-center p-4">
                                <img class="w-120px mb-3" src="{{asset_path('assets/admin/svg/illustrations/sorry.svg')}}" alt="Image Description">
                                <p class="mb-0">{{translate('No_data_to_show')}}</p>
                            </div>
                        @endif
                    </div>
                    <!-- End Table -->
                </div>
                <!-- End Card -->
            </div>
        </div>
    </div>

    <div class="modal fade" id="product-limit-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content product-limit-modal-content">
                <div class="modal-body">
                    <div class="product-limit-modal-icon">!</div>
                    <div class="product-limit-modal-text">
                        You have reached the limit to add more product.
                    </div>
                    <div class="product-limit-modal-actions">
                        <button type="button" class="btn product-limit-close-btn" data-dismiss="modal">Close</button>
                        <a href="{{ route('admin.pricing-plan') }}" class="btn product-limit-plan-btn">View Pricing Plan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('css_or_js')
<style>
    .product-limit-modal-content {
        border-radius: 12px;
        border: 0;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
    }

    #product-limit-modal .modal-body {
        padding: 28px 28px 30px;
        text-align: center;
    }

    .product-limit-modal-icon {
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

    .product-limit-modal-text {
        color: #6b7280;
        font-size: 15px;
        text-align: center;
        margin: 0 0 24px;
    }

    .product-limit-modal-actions {
        display: flex;
        justify-content: center;
        gap: 12px;
    }

    .product-limit-close-btn,
    .product-limit-plan-btn {
        min-width: 128px;
        border-radius: 6px;
        padding: 10px 18px;
        font-size: 14px;
        font-weight: 600;
    }

    .product-limit-close-btn {
        color: #5f6368;
        background-color: #f1f3f4;
        border: 1px solid #f1f3f4;
    }

    .product-limit-plan-btn {
        color: #fff;
        background-color: #69c7f1;
        border: 1px solid #69c7f1;
    }

    .product-limit-plan-btn:hover {
        color: #fff;
        background-color: #52b7ec;
        border-color: #52b7ec;
    }
</style>
@endpush

@push('script_2')
<script>
        const productLimit = Number($('#product-add-btn').data('product-limit')) || 4;
        const productCount = Number($('#product-add-btn').data('product-count')) || 0;

        function showProductLimitPopup() {
            $('#product-limit-modal').appendTo('body').modal('show');
        }

        $('#product-add-btn').on('click', function(e) {
            if (productCount >= productLimit) {
                e.preventDefault();
                showProductLimitPopup();
            }
        });

        @if(session('product_limit_reached'))
            showProductLimitPopup();
        @endif

        $(document).ready(function () {
            const selectedCategoryId = $('#category-filter').val();
            const selectedSubCategoryId = $('#sub-category-filter').data('id');

            if (selectedCategoryId) {
                loadSubCategories(selectedCategoryId, selectedSubCategoryId);
            }
        });

        function submitProductFilter() {
            document.getElementById('product-filter-form').submit();
        }

        function refreshSubCategorySelect() {
            const $subCategoryFilter = $('#sub-category-filter');

            if ($subCategoryFilter.hasClass('select2-hidden-accessible')) {
                $subCategoryFilter.select2('destroy');
            }

            $.HSCore.components.HSSelect2.init($subCategoryFilter);
        }

        function handleCategoryChange(categoryId) {
            const $subCategoryFilter = $('#sub-category-filter');

            if (!categoryId) {
                $subCategoryFilter.html('<option value="">---{{ translate('select') }}---</option>');
                refreshSubCategorySelect();
                submitProductFilter();
                return;
            }

            loadSubCategories(categoryId);
        }

        function loadSubCategories(categoryId, selectedSubCategoryId = '') {
            const $subCategoryFilter = $('#sub-category-filter');

            if (!categoryId) {
                $subCategoryFilter.html('<option value="">---{{ translate('select') }}---</option>');
                refreshSubCategorySelect();
                return;
            }

            $.get({
                url: '{{ url("/") }}/admin/product/get-categories?parent_id=' + categoryId + '&sub_category=' + selectedSubCategoryId,
                dataType: 'json',
                success: function (data) {
                    $subCategoryFilter.empty().append(data.options);
                    refreshSubCategorySelect();
                },
            });
        }

        function status_change_alert(url, message, e) {
            e.preventDefault();
            Swal.fire({
                title: '{{ translate("Are you sure?") }}',
                text: message,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#107980',
                cancelButtonText: '{{ translate("No") }}',
                confirmButtonText: '{{ translate("Yes") }}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    location.href = url;
                }
            })
        }
</script>

<script>
    function featured_status_change_alert(url, message, e) {
        e.preventDefault();
        Swal.fire({
            title: '{{ translate("Are you sure?") }}',
            text: message,
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#107980',
            cancelButtonText: '{{ translate("No") }}',
            confirmButtonText: '{{ translate("Yes") }}',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                location.href = url;
            }
        })
    }
</script>

    <script>
        function daily_needs(id, status) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.product.daily-needs')}}",
                method: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function () {
                    toastr.success('{{ translate("Daily need status updated successfully") }}');
                }
            });
        }
    </script>
@endpush
