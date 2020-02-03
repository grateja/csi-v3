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

    <dt>Status:</dt>
    <dd>
        {{$status}}
    </dd>

    <dt>Total amount:</dt>
    <dd>
        P {{number_format($total_amount, 2)}}
    </dd>
</dl>

<div class="button-container">
    <button onclick="window.print()">PRINT</button>
</div>

<script>
</script>

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
