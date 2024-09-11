<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Print Table</title>
        <meta charset="UTF-8">
        <meta name=description content="">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Bootstrap CSS -->
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        <style>
            body {margin: 20px}
        </style>
        <link href="/vendor/core/plugins/crud/css/module_custom_styles.css" rel="stylesheet">
    </head>
    <body>
        <table class="table table-bordered table-condensed table-striped">
           
            @foreach($data as $row)
                @if ($row == reset($data)) 
                <tr >
                    <th colspan="{{count($row)}}" class="printHeader">
                        <img src="/storage/{{setting('admin_logo')}}" alt="logo" >
                        <h4></h4>
                        <h4>{{page_title()->getTitle()}}</h4>
                    </th>
                </tr>
                    <tr>
                        @foreach($row as $key => $value)
                            <th>{!! $key !!}</th>
                        @endforeach
                    </tr>
                @endif
                <tr>
                    @foreach($row as $key => $value)
                        @if(is_string($value) || is_numeric($value))
                            <td>{!! $value !!}</td>
                        @else
                            <td></td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </table>
    </body>
</html>
