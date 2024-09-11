@if ($data->count() > 0)
<div class="scroller">
    <table class="table table-striped">
        <thead>
            <tr>
            @foreach($fields as $field)
            <th>{{ $field }}</th>
            @endforeach
            </tr>
        </thead>
        <tbody>
        @foreach($data as $k => $value)
        <tr>
            @foreach($fields as $field)
             <td>{{ $value[$field] }}</td>
            @endforeach
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@if ($data instanceof Illuminate\Pagination\LengthAwarePaginator && $data->total() > $limit)
    <div class="widget_footer">
        @include('core/dashboard::partials.paginate', ['data' => $data, 'limit' => $limit])
    </div>
@endif
@else
    @include('core/dashboard::partials.no-data', ['message' => trans('plugins/request-log::request-log.no_request_error')])
@endif
