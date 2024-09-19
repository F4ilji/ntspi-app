<!DOCTYPE html>
<html>
<head>
    <title>Ответ на форму</title>
</head>
<body>
<h1>Ответ на форму</h1>

@foreach ($data as $item)
    @if ($item['type'] === 'text')
        {!! $item['data']['content'] !!}
    @elseif ($item['type'] === 'answers')
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
            <tr>
                <th style="padding: 8px; background-color: #f2f2f2; border: 1px solid #ddd;">Поле</th>
                <th style="padding: 8px; background-color: #f2f2f2; border: 1px solid #ddd;">Значение</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($columns as $column)
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $column['data']['title_field'] }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">
                        @if ($column['type'] == 'email')
                            {{ $answers[$column['data']['name_field']] }}
                        @elseif ($column['type'] == 'multiple_choice')
                            @foreach ($column['data']['columns'] as $option)
                                @if (in_array($option['name_field'], $answers[$column['data']['name_field']]))
                                    <span style="display: inline-block; background-color: #007bff; color: #fff; padding: 4px 8px; border-radius: 20px; margin-right: 4px;">{{ $option['title_field'] }}</span>
                                @endif
                            @endforeach
                        @else
                            {{ $answers[$column['data']['name_field']] }}
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>    @endif
@endforeach

</body>
</html>