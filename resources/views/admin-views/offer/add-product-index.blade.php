@extends('layouts.admin.app')

@section('title', translate('flash_sale_product'))
@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset_path('assets/admin/img/flash_sale.png')}}" class="w--20" alt="">
                </span>
                <span>
                    {{translate('flash deal product')}}
                </span>
            </h1>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0 text-capitalize">{{$flashDeal['title']}}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.offer.flash.add-product',[$flashDeal['id']])}}" method="post">
                            @csrf
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="flash-deal-category" class="title-color text-capitalize">{{ translate('category') }}</label>
                                        <select class="form-control js-select2-custom" id="flash-deal-category">
                                            <option value="">{{ translate('Select Category') }}</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="flash-deal-sub-category" class="title-color text-capitalize">{{ translate('sub_category') }}</label>
                                        <select class="form-control js-select2-custom" id="flash-deal-sub-category" disabled>
                                            <option value="">{{ translate('Select Sub Category') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="name" class="title-color text-capitalize">{{ translate('Add new product')}}</label>
                                        <select class="js-example-basic-multiple js-states js-example-responsive form-control h--45px" id="flash-deal-product" name="product_id">
                                            <option disabled selected>{{ translate('Select Product')}}</option>
                                            @foreach ($products as $key => $product)
                                                @php
                                                    $productCategories = collect(json_decode($product->category_ids, true) ?? []);
                                                    $categoryId = data_get($productCategories->firstWhere('position', 1), 'id', '');
                                                    $subCategoryId = data_get($productCategories->firstWhere('position', 2), 'id', '');
                                                @endphp
                                                <option value="{{ $product->id }}"
                                                        data-category-id="{{ $categoryId }}"
                                                        data-sub-category-id="{{ $subCategoryId }}">
                                                    {{$product['name']}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <div class="btn--container justify-content-end">
                                    <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <h5 class="mb-0 text-capitalize">
                            {{ translate('Product')}} {{ translate('Table')}}
                            <span class="badge badge-soft-dark radius-50 fz-12 ml-1">{{ $flashDealProducts->total() }}</span>
                        </h5>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100" cellspacing="0">
                            <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th>{{ translate('SL')}}</th>
                                <th>{{ translate('name')}}</th>
                                <th>{{ translate('actual_price')}}</th>
                                <th>{{ translate('discount')}}</th>
                                <th>{{ translate('discount_price')}}</th>
                                <th class="text-center">{{ translate('action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($flashDealProducts as $k=>$flashProduct)
                                @php($discount = Helpers::discount_calculate($flashProduct, $flashProduct['price']))
                                <tr>
                                    <td>{{$flashDealProducts->firstitem()+$k}}</td>
                                    <td class="pt-1 pb-3  {{$k == 0 ? 'pt-4' : '' }}">
                                        <a href="{{route('admin.product.view',[$flashProduct['id']])}}" target="_blank" class="product-list-media">
                                            @if (!empty(json_decode($flashProduct['image'],true)))
                                                <img src="{{ $flashProduct->identityImageFullPath[0] ?? asset_path('assets/admin/img/160x160/2.png') }}"
                                                    alt="{{ translate('product') }}">
                                            @else
                                                <img src="{{asset_path('assets/admin/img/400x400/img2.jpg')}}">
                                            @endif
                                            <h6 class="name line--limit-2">
                                                {{\Illuminate\Support\Str::limit($flashProduct['name'], 20, $end='...')}}
                                            </h6>
                                        </a>
                                    </td>
                                    <td>{{ Helpers::set_symbol($flashProduct['price']) }}</td>
                                    <td>{{ Helpers::set_symbol($discount) }}</td>
                                    <td>{{ Helpers::set_symbol($flashProduct['price'] - $discount) }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <a  title="{{ trans ('Delete')}}"
                                                class="btn btn-outline-danger btn-sm delete"
                                                id="{{$flashProduct['id']}}">
                                                <i class="tio-delete"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <table>
                            <tfoot>
                            {!! $flashDealProducts->links() !!}
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script_2')

    <script>
        "use strict";

        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            width: 'resolve'
        });

        const $categorySelect = $('#flash-deal-category');
        const $subCategorySelect = $('#flash-deal-sub-category');
        const $productSelect = $('#flash-deal-product');
        const initialProductOptions = $productSelect.find('option').clone();

        function resetSubCategoryOptions() {
            $subCategorySelect.html('<option value="">{{ translate('Select Sub Category') }}</option>');
            $subCategorySelect.prop('disabled', true).val(null).trigger('change');
        }

        function rebuildProductOptions() {
            const selectedCategoryId = String($categorySelect.val() || '');
            const selectedSubCategoryId = String($subCategorySelect.val() || '');

            const filteredOptions = initialProductOptions.filter(function () {
                const optionValue = $(this).val();

                if (!optionValue) {
                    return true;
                }

                const optionCategoryId = String($(this).data('category-id') || '');
                const optionSubCategoryId = String($(this).data('sub-category-id') || '');

                if (selectedCategoryId && optionCategoryId !== selectedCategoryId) {
                    return false;
                }

                if (selectedSubCategoryId && optionSubCategoryId !== selectedSubCategoryId) {
                    return false;
                }

                return true;
            }).clone();

            $productSelect.html(filteredOptions);
            $productSelect.val(null).trigger('change');
        }

        function loadSubCategories(categoryId) {
            if (!categoryId) {
                resetSubCategoryOptions();
                rebuildProductOptions();
                return;
            }

            $.get({
                url: '{{ url("/") }}/admin/product/get-categories?parent_id=' + categoryId,
                dataType: 'json',
                success: function (data) {
                    $subCategorySelect.html(data.options);
                    $subCategorySelect.prop('disabled', false).val(null).trigger('change');
                    rebuildProductOptions();
                },
                error: function () {
                    resetSubCategoryOptions();
                    rebuildProductOptions();
                }
            });
        }

        $categorySelect.on('change', function () {
            loadSubCategories($(this).val());
        });

        $subCategorySelect.on('change', function () {
            rebuildProductOptions();
        });

        rebuildProductOptions();

        $(document).on('click', '.delete', function () {
            var id = $(this).attr("id");
            var flash_deal_id = {{ $flashDeal->id }}
            Swal.fire({
                title: "{{translate('Are_you_sure_remove_this_product')}}?",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{translate('Yes')}}, {{translate('delete_it')}}!',
                type: 'warning',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        }
                    });
                    $.ajax({
                        url: "{{route('admin.offer.flash.delete.product')}}",
                        method: 'POST',
                        data: {
                                id: id,
                                flash_deal_id : flash_deal_id
                            },
                        success: function (data) {
                            toastr.success('{{translate('product_removed_successfully')}}');
                            location.reload();
                        },
                    });
                }
            })
        });
    </script>

@endpush


