<dl>
    <dt>Date:</dt>
    <dd>{{Carbon\Carbon::createFromDate($created_at)->format('M-d, Y h:i A')}}</dd>
    <dt>Customer name:</dt>
    <dd>{{$customer_name}}</dd>
    <dt>RFID:</dt>
    <dd>{{$rfid}}</dd>
    <dt>Amount:</dt>
    <dd>{{$amount}}</dd>
    <dt>Remaining balance:</dt>
    <dd>{{$remaining_balance}}</dd>
    <dt>Current balance:</dt>
    <dd>{{$current_balance}}</dd>
    <dt>Cash:</dt>
    <dd>{{$cash}}</dd>
    <dt>Change:</dt>
    <dd>{{$change}}</dd>
    <dt>Staff name:</dt>
    <dd>{{$staff_name}}</dd>
    <dt>Remarks:</dt>
    <dd>{{$remarks}}</dd>
</dl>
