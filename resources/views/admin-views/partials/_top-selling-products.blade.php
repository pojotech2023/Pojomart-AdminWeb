<div class="card-header border-0 order-header-shadow">
    <h5 class="card-title d-flex justify-content-between flex-grow-1">
        <span>{{translate('top_selling_products')}}</span>
        <a href="{{route('admin.product.list')}}" class="fz-12px font-medium text-006AE5">{{translate('view_all')}}</a>
    </h5>
</div>

<div class="card-body">
    <div class="top--selling">
        @foreach($top_sell as $key=>$item)
            @if(isset($item->product))
            <a class="grid--card" href="{{route('admin.product.view',[$item['product_id']])}}">
                <img src="{{ $item->product->identityImageFullPath[0] ?? asset_path('assets/admin/img/400x400/img2.jpg') }}"
                    class="img-fit"
                    alt="{{ $item->product->name }}"
                    onerror="this.src='{{ asset_path('assets/admin/img/400x400/img2.jpg') }}'">
                <div class="cont pt-2">
                    <h6 class="line--limit-2">{{ substr($item->product['name'],0,20) . (strlen($item->product['name'])>20?'...':'')}}</h6>
                </div>
                <div class="ml-auto">
                    <span class="badge badge-soft">{{ translate('Sold') }} : {{$item['count']}}</span>
                </div>
            </a>
            @endif
        @endforeach
    </div>
</div>


