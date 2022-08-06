<div class="overflow-x-auto border">
    <table class="table table-zebra w-full">
    <thead>
        <tr>
            <th>Date</th>
            <th>Days</th>
            <th>Company Name</th>
            <th>Invoice number</th>
            <th>Sent</th>
            <th>Amount</th>
            <th>Paid</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoices as $invoice)
            <tr>
                <td>{{ $invoice->from_date->format('d M Y') }} - {{ $invoice->to_date->format('d M Y') }}</td>
                <td>{{ $invoice->from_date->diffInDays($invoice->to_date) }}</th>
                <td>{{ $invoice->company->name }}</td>
                <td>{{ $invoice->formatted_number() }}</td>
                <td>{{ BooleanHelper::tick_or_cross($invoice->sent) }}</td>
                <td>{{ MoneyHelper::format_money(MoneyHelper::total_earnt($invoice->billed_shifts)) }}</td>
                <td>{{ BooleanHelper::tick_or_cross($invoice->paid) }}</td>
                <td class="flex gap-1">
                    <a href="/invoice/{{ $invoice->id }}" class="btn btn-info">View Invoice</a>
                    <form action="/shifts" method="GET">
                        <input type="hidden" name="from_date" value="{{ $invoice->from_date }}">
                        <input type="hidden" name="to_date" value="{{ $invoice->to_date }}">
                        <input type="submit" class="btn btn-info" value="View Shifts">
                    </form>
                    <a href="/invoice/download/{{ $invoice->id }}" class="btn btn-warning">Download</a>
                    <a href="/invoice/{{ $invoice->id }}/destroy" class="btn btn-error">Destroy</a>
                </td>
            </tr>
        @endforeach
    </tbody>
    </table>
</div>
