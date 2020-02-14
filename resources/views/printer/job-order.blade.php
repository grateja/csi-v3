<div>
    <dl>
        <dt>Job order:</dt>
        <dd>
            {{$job_order}}
        </dd>

        <dt>Date:</dt>
        <dd>
            {{$date}}
        </dd>

        <dt>Customer name:</dt>
        <dd>
            {{$customer_name}}
        </dd>

        <dt>Staff name:</dt>
        <dd>
            {{$staff_name}}
        </dd>
    </dl>
    <table class="v-table">
        <tr>
            <th colspan="4">Services</th>
        </tr>
        <tr>
            <th>NAME</th>
            <th>UNIT PRICE</th>
            <th>QUANTITY</th>
            <th>TOTAL</th>
        </tr>

        @foreach($posServiceItems as $item)
            <tr>
                <td class="pl-1">{{$item['name']}}</td>
                <td class="text-xs-center">{{$item['unit_price'] ? 'P ' . $item['unit_price'] : 'FREE'}}</td>
                <td class="text-xs-center">
                    {{$item['quantity']}}
                </td>
                <td class="text-xs-center">{{$item['total_price'] ? 'P ' . $item['total_price'] : 'FREE'}}</td>
            </tr>
        @endforeach
        <tr class=" font-weight-bold">
            <td colspan="2" class="pl-1">Total</td>
            <td class="text-xs-center">{{$posServiceSummary['total_quantity']}}</td>
            <td class="text-xs-center">P {{$posServiceSummary['total_price']}}</td>
        </tr>
        <tr>
            <th colspan="4">Products</th>
        </tr>
        <tr>
            <th>NAME</th>
            <th>UNIT PRICE</th>
            <th>QUANTITY</th>
            <th>TOTAL</th>
        </tr>
        @foreach($posProductItems as $item)
            <tr>
                <td class="pl-1">{{$item['name']}}</td>
                <td class="text-xs-center">{{$item['unit_price'] ? 'P ' . $item['unit_price'] : 'FREE'}}</td>
                <td class="text-xs-center">
                    {{$item['quantity']}}
                </td>
                <td class="text-xs-center">{{$item['total_price'] ? 'P ' . $item['total_price'] : 'FREE'}}</td>
            </tr>
        @endforeach
        <tr class=" font-weight-bold">
            <td colspan="2" class="pl-1">Total</td>
            <td class="text-xs-center">{{$posProductSummary['total_quantity']}}</td>
            <td class="text-xs-center">P {{$posProductSummary['total_price']}}</td>
        </tr>
        <tr class="font-weight-bold title">
            <td>Grand total</td>
            <td>&nbsp;&nbsp;&nbsp;</td>
            <td class="text-xs-center">{{$posProductSummary['total_quantity'] + $posServiceSummary['total_quantity']}}</td>
            <td class="text-xs-center">P {{$total_amount}}</td>
        </tr>
    </table>

    <dl>
        <dt>Date paid:</dt>
        <dd>
            {{$date_paid}}
        </dd>

        <dt>Paid to:</dt>
        <dd>
            {{$paid_to}}
        </dd>

        <dt>Cash:</dt>
        <dd>
            {{$cash}}
        </dd>

        <dt>Change:</dt>
        <dd>
            {{$change}}
        </dd>
        @if($points)
            <dt>Points used:</dt>
            <dd>
                P {{number_format($points_in_peso, 2)}} ({{number_format($points, 1)}} points)
            </dd>
        @endif
        @if($discount)
            <dt>Discount:</dt>
            <dd>
                P {{number_format($discount_in_peso, 2)}} ({{number_format($discount, 1)}}%)
            </dd>
        @endif
        @if($rfid)
            <dt>RFID:</dt>
            <dd>
                P {{number_format($card_load_used, 2)}} ({{$rfid}})
            </dd>
        @endif
    </dl>

</div>


<style>
    * {
        font-family: 'sans-serif';
    }
    dt {
        color: #999;
    }
    .button-container {
        position: fixed;
        right: 10px;
        top: 10px;
    }
    @media print {
        .button-container {
            display: none;
        }
    }
</style>
