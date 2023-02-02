<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
        <title>{{$details['title']}}</title>
        <style>
            .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            }
            .header p {
            color: #28A802;
            margin-bottom: 20px;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
            font-size: 24px;
            }
            .btn {
            background-color: #28A802;
            color: #fff;
            padding: 10px 20px;
            display:inline-block;
            border-radius: 5px;
            text-decoration: none;
            }
        </style>
    </head>
    <body>
      <div class="container">
        <div class="header">
            <p>Hirebalidriver HRM</p>
        </div>
        <strong>Hello {{$details['name']}}!</strong>
        <p>Booking details : </p>

        <table style="width: 100%;">
            <tr>
                <td>Ref ID</td>
                <td>: {{$details['ref_id']}}</td>
            </tr>
            <tr>
                <td>Date</td>
                <td>: {{$details['date']}}</td>
            </tr>
            <tr>
                <td>Time</td>
                <td>: {{$details['time']}}</td>
            </tr>
            <tr>
                <td>Name</td>
                <td>: {{$details['guestName']}}</td>
            </tr>
            <tr>
                <td>Phone</td>
                <td>: {{$details['phone']}}</td>
            </tr>
            <tr>
                <td>Hotel</td>
                <td>: {{$details['hotel']}}</td>
            </tr>
            <tr>
                <td>Collect</td>
                <td>: {{$details['collect']}}</td>
            </tr>
            <tr>
                <td>People</td>
                <td>: {{$details['adult']}} Adult, {{$details['child']}} Child,</td>
            </tr>
            <tr>
                <td>Note</td>
                <td>: {{$details['note']}}</td>
            </tr>
        </table>
        <br />
        <strong>Lihat detail booking di Aplikasi.</strong>

        <p>Thank You,</p>
        <p>Hirebalidriver.com</p>
       </div>
    </body>
</html>
